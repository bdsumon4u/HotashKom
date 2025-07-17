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
        $user = auth('admin')->user();
        $isAdmin = $user && $user->is('admin');

        $select = [
            'products.*',
            DB::raw('COALESCE((SELECT NULLIF(SUM(v.stock_count), 0) FROM products v WHERE v.parent_id = products.id), products.stock_count) as stock'),
        ];
        if ($isAdmin) {
            $select[] = DB::raw('(
                SELECT
                    CASE WHEN SUM(pp.quantity) > 0
                        THEN ROUND(SUM(pp.price * pp.quantity) / SUM(pp.quantity), 2)
                        ELSE 0 END
                FROM product_purchase pp
                WHERE pp.product_id = products.id
            ) as average_purchase_price');
        }

        $query = Product::query()
            ->whereNull('parent_id')
            ->with('variations')
            ->select($select);

        $dt = DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image', fn (Product $product): string => '<img src="'.asset(optional($product->base_image)->src).'" width="100" height="100" />')
            ->editColumn('name', fn (Product $product): string => '<a href="'.route('products.show', $product).'" target="_blank">'.$product->name.'</a>')
            ->addColumn('price', fn (Product $product): string => $product->price == $product->selling_price
                ? '<p>'.theMoney($product->price).'</p>'
                : '<del style="color: #ff0000;">'.theMoney($product->price).'</del><br><ins style="text-decoration: none;">'.theMoney($product->selling_price).'</ins>')
            ->addColumn('stock', function (Product $product) {
                if ($product->should_track) {
                    return '<span class="text-'.($product->stock ? 'success' : 'danger').'">'.$product->stock.' In Stock</span>';
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

        if ($isAdmin) {
            $dt = $dt
                ->addColumn('average_purchase_price', function (Product $product) {
                    return $product->average_purchase_price !== null ? number_format($product->average_purchase_price, 2) : '-';
                })
                ->orderColumn('average_purchase_price', 'average_purchase_price $1');
        }

        return $dt->make(true);
    }
}
