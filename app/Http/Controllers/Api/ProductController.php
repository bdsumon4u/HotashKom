<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $select = [
            'products.*',
            DB::raw('COALESCE((SELECT NULLIF(SUM(v.stock_count), 0) FROM products v WHERE v.parent_id = products.id), products.stock_count) as stock'),
        ];

        $query = Product::query()
            ->whereNull('parent_id')
            ->with('variations')
            ->select($select);

        $dt = DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image', fn (Product $product): string => '<img src="'.asset(optional($product->base_image)->src).'" width="100" height="100" />')
            ->editColumn('name', function (Product $product): string {
                $variationCount = $product->variations->count();
                $variationLabel = $variationCount > 0 ? ' <span class="badge badge-secondary">'.$variationCount.' variation'.($variationCount > 1 ? 's' : '').'</span>' : '';

                return '<a href="'.route('products.show', $product).'" target="_blank">'.$product->name.'</a>'.$variationLabel;
            })
            ->addColumn('price', function (Product $product): string {
                $averagePurchasePrice = $product->average_purchase_price ?? 0;
                $priceHtml = '';
                if ($averagePurchasePrice > 0) {
                    $priceHtml .= '<span style="font-size: 13px; color: #888;">Purchase: <strong>'.theMoney($averagePurchasePrice).'</strong></span><hr style="margin: 2px 0;">';
                }
                if ($product->price == $product->selling_price) {
                    $priceHtml .= '<p>Sell: '.theMoney($product->price).'</p>';
                } else {
                    $priceHtml .= '<del style="color: #ff0000;">Old Sell: '.theMoney($product->price).'</del><br><ins style="text-decoration: none;">New Sell: '.theMoney($product->selling_price).'</ins>';
                }

                return $priceHtml;
            })
            ->addColumn('stock', function (Product $product) {
                if ($product->should_track) {
                    $stock = $product->stock;
                    $stockText = '<span class="text-'.($stock ? 'success' : 'danger').'">'.$stock.' In Stock</span>';

                    $averagePurchasePrice = $product->average_purchase_price ?? 0;
                    $sellingPrice = $product->selling_price ?? 0;

                    $totalPurchaseValue = $stock * $averagePurchasePrice;
                    $totalSaleValue = $stock * $sellingPrice;

                    $purchaseValueText = '<br><small>Buy: <strong>'.theMoney($totalPurchaseValue).'</strong></small>';
                    $saleValueText = '<br><small>Sell: <strong>'.theMoney($totalSaleValue).'</strong></small>';

                    return $stockText.$purchaseValueText.$saleValueText;
                } else {
                    return '<span class="text-success">In Stock</span>';
                }
            })
            ->addColumn('actions', fn (Product $product): string => '<div>
                    <a href="'.route('admin.products.edit', $product).'" class="btn btn-sm btn-block btn-primary">Edit</a>
                    <a target="_blank" href="/landing/'.$product->id.'" class="btn btn-sm btn-block btn-info">Landing</a>
                    <a href="'.route('admin.products.destroy', $product).'" data-action="delete" class="btn btn-sm btn-block btn-danger">Delete</a>
                </div>')
            ->rawColumns(['image', 'name', 'price', 'stock', 'actions'])
            ->orderColumn('stock', function ($query, $direction) {
                $query->orderByRaw(
                    "should_track DESC, stock $direction"
                );
            })
            ->orderColumn('price', function ($query, $direction) {
                $query->orderBy('selling_price', $direction);
            });

        return $dt->make(true);
    }
}
