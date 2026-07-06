<?php

namespace Tests\Unit\Services;

use App\Services\VCardImportService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class VCardImportServiceTest extends TestCase
{
    private VCardImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VCardImportService;
    }

    private function invoke(string $method, ...$args)
    {
        $ref = new ReflectionMethod(VCardImportService::class, $method);
        $ref->setAccessible(true);

        return $ref->invoke($this->service, ...$args);
    }

    public function test_parse_vcards_extracts_multiple_contacts(): void
    {
        $content = <<<'VCF'
        BEGIN:VCARD
        VERSION:3.0
        FN:Jane Doe
        TEL:+254712345678
        EMAIL:jane@example.com
        ORG:Acme Ltd
        TITLE:Manager
        END:VCARD
        BEGIN:VCARD
        VERSION:3.0
        FN:John Smith
        TEL:0722000111
        END:VCARD
        VCF;

        $vcards = $this->invoke('parseVCards', $content);

        $this->assertCount(2, $vcards);
        $this->assertSame('Jane Doe', $vcards[0]['name']);
        $this->assertSame('+254712345678', $vcards[0]['phone']);
        $this->assertSame('jane@example.com', $vcards[0]['email']);
        $this->assertSame('Acme Ltd', $vcards[0]['company']);
        $this->assertSame('Manager', $vcards[0]['position']);
        $this->assertSame('John Smith', $vcards[1]['name']);
    }

    public function test_parse_vcards_ignores_content_outside_begin_end(): void
    {
        $content = "GARBAGE\nBEGIN:VCARD\nFN:Only One\nTEL:123\nEND:VCARD\nMORE GARBAGE";

        $vcards = $this->invoke('parseVCards', $content);

        $this->assertCount(1, $vcards);
        $this->assertSame('Only One', $vcards[0]['name']);
    }

    public function test_parse_vcard_data_builds_name_from_n_field_when_fn_missing(): void
    {
        // N is structured as Family;Given;... -> service outputs "Given Family"
        $data = $this->invoke('parseVCardData', ['N:Doe;Jane;;;', 'TEL:123456']);

        $this->assertSame('Jane Doe', $data['name']);
    }

    public function test_parse_vcard_data_joins_address_parts(): void
    {
        $data = $this->invoke('parseVCardData', ['FN:Someone', 'ADR:;;123 Main St;Nairobi;;00100;Kenya']);

        $this->assertSame('123 Main St, Nairobi, 00100, Kenya', $data['address']);
    }

    public function test_parse_vcard_data_keeps_first_phone_only(): void
    {
        $data = $this->invoke('parseVCardData', ['FN:Someone', 'TEL;TYPE=CELL:111', 'TEL;TYPE=WORK:222']);

        $this->assertSame('111', $data['phone']);
    }

    public function test_format_phone_strips_non_numeric_and_leading_plus(): void
    {
        $this->assertSame('254712345678', $this->invoke('formatPhone', '+254 712 345 678'));
        $this->assertSame('0722000111', $this->invoke('formatPhone', '(072) 2000-111'));
    }

    public function test_decode_value_trims_and_returns_plain_value(): void
    {
        $this->assertSame('Hello World', $this->invoke('decodeValue', '  Hello World  '));
    }
}
