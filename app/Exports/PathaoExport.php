<?php

namespace App\Exports;

use App\Models\Order;
use App\Pathao\Facade\Pathao;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PathaoExport implements FromCollection, WithHeadings, WithMapping
{
    private $storeName;

    public function __construct()
    {
        $this->storeName = current(array_filter(Pathao::store()->list()->data, fn ($store): bool => $store->store_id == setting('Pathao')->store_id ?? 0))->store_name ?? 'N/A';
    }

    public function headings(): array
    {
        return [
            'ItemType(*)',
            'StoreName(*)',
            'MerchantOrderId',
            'RecipientName(*)',
            'RecipientPhone(*)',
            'RecipientCity(*)',
            'RecipientZone(*)',
            'RecipientAddress(*)',
            'AmountToCollect(*)',
            'ItemQuantity(*)',
            'ItemWeight(*)',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::where('status', 'INVOICED')->where('data->courier', 'Pathao')->get();
    }

    /**
     * @param  Order  $order
     */
    public function map($order): array
    {
        return [
            'parcel',
            $this->storeName,
            $order->id,
            $order->name,
            str_replace('+88', '', $order->phone),
            $order->data['city_name'] ?? 'N/A',
            $order->data['area_name'] ?? 'N/A',
            $order->address,
            $order->condition,
            array_sum(array_column((array) $order->products, 'quantity')) ?? 1,
            $order->data['weight'] ?? 0.5,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'RecipientPhone(*)' => '0',
        ];
    }
}
