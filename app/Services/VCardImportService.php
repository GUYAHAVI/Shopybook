<?php

namespace App\Services;

use App\Models\ImportedContact;
use App\Models\ContactGroup;
use Illuminate\Support\Facades\Log;

class VCardImportService
{
    /**
     * Import contacts from VCF (vCard) file
     */
    public function import($filePath, $contactGroupId, $businessId)
    {
        $content = file_get_contents($filePath);
        $vcards = $this->parseVCards($content);
        
        $imported = 0;
        $errors = [];

        foreach ($vcards as $vcard) {
            try {
                $contact = $this->createContactFromVCard($vcard, $contactGroupId, $businessId);
                if ($contact) {
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                Log::error('VCard import error: ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'imported' => $imported,
            'errors' => $errors,
            'total' => count($vcards),
        ];
    }

    /**
     * Parse VCF file content into individual vCards
     */
    private function parseVCards($content)
    {
        // Split by BEGIN:VCARD
        $vcards = [];
        $lines = explode("\n", $content);
        $currentVCard = [];
        $inVCard = false;

        foreach ($lines as $line) {
            $line = trim($line);
            
            if ($line === 'BEGIN:VCARD') {
                $inVCard = true;
                $currentVCard = [];
            } elseif ($line === 'END:VCARD') {
                $inVCard = false;
                if (!empty($currentVCard)) {
                    $vcards[] = $this->parseVCardData($currentVCard);
                }
            } elseif ($inVCard && !empty($line)) {
                $currentVCard[] = $line;
            }
        }

        return $vcards;
    }

    /**
     * Parse a single vCard's data
     */
    private function parseVCardData($lines)
    {
        $data = [
            'name' => '',
            'phone' => '',
            'email' => '',
            'company' => '',
            'position' => '',
            'address' => '',
        ];

        foreach ($lines as $line) {
            // Handle line folding (lines starting with space or tab)
            if (preg_match('/^[\s\t]/', $line)) {
                continue;
            }

            // Split by first colon
            $parts = explode(':', $line, 2);
            if (count($parts) < 2) {
                continue;
            }

            $field = $parts[0];
            $value = $parts[1];

            // Handle FN (Full Name)
            if (strpos($field, 'FN') === 0) {
                $data['name'] = $this->decodeValue($value);
            }
            // Handle N (Name) if FN is not present
            elseif (strpos($field, 'N') === 0 && empty($data['name'])) {
                $nameParts = explode(';', $value);
                $data['name'] = trim(($nameParts[1] ?? '') . ' ' . ($nameParts[0] ?? ''));
            }
            // Handle TEL (Telephone)
            elseif (strpos($field, 'TEL') === 0 && empty($data['phone'])) {
                $data['phone'] = $this->decodeValue($value);
            }
            // Handle EMAIL
            elseif (strpos($field, 'EMAIL') === 0 && empty($data['email'])) {
                $data['email'] = $this->decodeValue($value);
            }
            // Handle ORG (Organization)
            elseif (strpos($field, 'ORG') === 0) {
                $data['company'] = $this->decodeValue($value);
            }
            // Handle TITLE
            elseif (strpos($field, 'TITLE') === 0) {
                $data['position'] = $this->decodeValue($value);
            }
            // Handle ADR (Address)
            elseif (strpos($field, 'ADR') === 0 && empty($data['address'])) {
                $addressParts = explode(';', $value);
                $data['address'] = trim(implode(', ', array_filter($addressParts)));
            }
        }

        return $data;
    }

    /**
     * Decode vCard value (handle quoted-printable, etc.)
     */
    private function decodeValue($value)
    {
        // Remove any encoding specifications
        $value = preg_replace('/^[^:]*:/', '', $value);
        
        // Decode quoted-printable if needed
        if (strpos($value, '=') !== false) {
            $value = quoted_printable_decode($value);
        }
        
        // Decode UTF-8
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        return trim($value);
    }

    /**
     * Create contact from vCard data
     */
    private function createContactFromVCard($vcard, $contactGroupId, $businessId)
    {
        // Skip if no name or phone
        if (empty($vcard['name']) || empty($vcard['phone'])) {
            return null;
        }

        return ImportedContact::create([
            'business_id' => $businessId,
            'contact_group_id' => $contactGroupId,
            'name' => $vcard['name'],
            'email' => $vcard['email'] ?? null,
            'phone' => $this->formatPhone($vcard['phone']),
            'company' => $vcard['company'] ?? null,
            'position' => $vcard['position'] ?? null,
            'address' => $vcard['address'] ?? null,
            'source' => 'vcf',
            'metadata' => [
                'imported_at' => now()->toDateTimeString(),
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



