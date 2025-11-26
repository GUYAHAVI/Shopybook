<?php

namespace App\Imports;

use App\Models\ImportedContact;
use App\Models\ContactGroup;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Auth;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $contactGroupId;
    protected $businessId;

    public function __construct($contactGroupId, $businessId)
    {
        $this->contactGroupId = $contactGroupId;
        $this->businessId = $businessId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Handle different possible column names
        $name = $row['name'] ?? $row['full_name'] ?? $row['first_name'] . ' ' . ($row['last_name'] ?? '');
        $phone = $row['phone'] ?? $row['mobile'] ?? $row['phone_number'] ?? $row['telephone'] ?? null;
        $email = $row['email'] ?? $row['email_address'] ?? null;
        
        // Skip if no name or phone
        if (empty($name) || empty($phone)) {
            return null;
        }

        return new ImportedContact([
            'business_id' => $this->businessId,
            'contact_group_id' => $this->contactGroupId,
            'name' => $name,
            'email' => $email,
            'phone' => $this->formatPhone($phone),
            'company' => $row['company'] ?? $row['organization'] ?? null,
            'position' => $row['position'] ?? $row['title'] ?? $row['job_title'] ?? null,
            'address' => $row['address'] ?? null,
            'notes' => $row['notes'] ?? null,
            'source' => 'csv',
            'metadata' => [
                'imported_at' => now()->toDateTimeString(),
                'original_data' => $row,
            ],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ];
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



