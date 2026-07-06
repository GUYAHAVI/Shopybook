<?php

namespace App\Services;

use App\Models\ImportedContact;
use App\Models\ContactGroup;
use Google\Client;
use Google\Service\PeopleService;
use Illuminate\Support\Facades\Log;

class GoogleContactsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(PeopleService::CONTACTS_READONLY);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    /**
     * Get the authorization URL
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for access token
     */
    public function authenticate($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new \Exception('Error authenticating: ' . $token['error']);
        }
        
        return $token;
    }

    /**
     * Import contacts from Google
     */
    public function import($accessToken, $contactGroupId, $businessId)
    {
        try {
            $this->client->setAccessToken($accessToken);
            
            // Refresh token if expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                } else {
                    throw new \Exception('Access token expired and no refresh token available');
                }
            }

            $service = new PeopleService($this->client);
            
            $imported = 0;
            $errors = [];
            $pageToken = null;

            do {
                $params = [
                    'pageSize' => 1000,
                    'personFields' => 'names,phoneNumbers,emailAddresses,organizations,addresses',
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $results = $service->people_connections->listPeopleConnections('people/me', $params);

                foreach ($results->getConnections() as $person) {
                    try {
                        $contact = $this->createContactFromGooglePerson($person, $contactGroupId, $businessId);
                        if ($contact) {
                            $imported++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                        Log::error('Google contact import error: ' . $e->getMessage());
                    }
                }

                $pageToken = $results->getNextPageToken();
            } while ($pageToken);

            return [
                'success' => true,
                'imported' => $imported,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            Log::error('Google contacts import failed', [
                'business_id' => $businessId,
                'contact_group_id' => $contactGroupId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported' => 0,
            ];
        }
    }

    /**
     * Create contact from Google Person object
     */
    private function createContactFromGooglePerson($person, $contactGroupId, $businessId)
    {
        $names = $person->getNames();
        $phoneNumbers = $person->getPhoneNumbers();
        $emailAddresses = $person->getEmailAddresses();
        $organizations = $person->getOrganizations();
        $addresses = $person->getAddresses();

        // Get name
        $name = '';
        if ($names && count($names) > 0) {
            $name = $names[0]->getDisplayName();
        }

        // Get phone
        $phone = '';
        if ($phoneNumbers && count($phoneNumbers) > 0) {
            $phone = $phoneNumbers[0]->getValue();
        }

        // Skip if no name or phone
        if (empty($name) || empty($phone)) {
            return null;
        }

        // Get email
        $email = null;
        if ($emailAddresses && count($emailAddresses) > 0) {
            $email = $emailAddresses[0]->getValue();
        }

        // Get organization info
        $company = null;
        $position = null;
        if ($organizations && count($organizations) > 0) {
            $company = $organizations[0]->getName();
            $position = $organizations[0]->getTitle();
        }

        // Get address
        $address = null;
        if ($addresses && count($addresses) > 0) {
            $address = $addresses[0]->getFormattedValue();
        }

        return ImportedContact::create([
            'business_id' => $businessId,
            'contact_group_id' => $contactGroupId,
            'name' => $name,
            'email' => $email,
            'phone' => $this->formatPhone($phone),
            'company' => $company,
            'position' => $position,
            'address' => $address,
            'source' => 'google',
            'metadata' => [
                'imported_at' => now()->toDateTimeString(),
                'resource_name' => $person->getResourceName(),
            ],
        ]);
    }

    /**
     * Format phone number
     */
    private function formatPhone($phone)
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove leading + if present
        $phone = ltrim($phone, '+');
        
        return $phone;
    }
}




