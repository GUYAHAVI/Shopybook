<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection(): Collection
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'id', 'name', 'description', 'sku', 'barcode', 'category', 'brand',
            'price', 'cost_price', 'stock_quantity', 'measurement_unit',
            'tax_treatment', 'low_stock_threshold', 'weight', 'dimensions',
            'is_active', 'is_featured',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description,
            $product->sku,
            $product->barcode,
            $product->category,
            $product->brand,
            $product->price,
            $product->cost_price,
            $product->stock_quantity,
            $product->measurement_unit,
            $product->tax_treatment,
            $product->low_stock_threshold,
            $product->weight,
            $product->dimensions,
            $product->is_active ? 1 : 0,
            $product->is_featured ? 1 : 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A' => ['font' => ['color' => ['rgb' => '888888']]],
        ];
    }
}
