<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return new Collection($this->products);
    }

    public function headings(): array
    {
        return [
            'Image Source',
            'Title',
            'SKU',
        ];
    }
}