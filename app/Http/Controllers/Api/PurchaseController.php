<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    /**
     * Handle the incoming request for purchases DataTable.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $query = Purchase::with(['productPurchases.product', 'admin']);

        // Remove all filters: search, product_filter, date_from, date_to, supplier_filter

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('products_count', function ($purchase) {
                return $purchase->productPurchases->count();
            })
            ->addColumn('formatted_date', function ($purchase) {
                return $purchase->purchase_date ? $purchase->purchase_date->format('d M Y') : '-';
            })
            ->addColumn('formatted_amount', function ($purchase) {
                return number_format($purchase->total_amount ?? 0, 2).' BDT';
            })
            ->addColumn('supplier_display', function ($purchase) {
                return $purchase->supplier_name ?? '-';
            })
            ->addColumn('admin_display', function ($purchase) {
                return $purchase->admin ? $purchase->admin->name : '-';
            })
            ->addColumn('actions', function ($purchase) {
                return '<a target="_blank" href="'.route('admin.purchases.show', $purchase).'" class="btn btn-sm btn-info">
                    <i class="fa fa-eye"></i>
                </a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Get products for filter dropdown.
     */
    public function getProducts(Request $request)
    {
        $search = $request->get('search');

        $query = Product::where('should_track', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')
            ->take(10)
            ->get(['id', 'name', 'sku'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name.' ('.$product->sku.')',
                ];
            });

        return response()->json($products);
    }

    /**
     * Get suppliers for filter dropdown.
     */
    public function getSuppliers(Request $request)
    {
        $search = $request->get('search');

        $query = Purchase::whereNotNull('supplier_name');

        if ($search) {
            $query->where('supplier_name', 'like', "%{$search}%");
        }

        $suppliers = $query->distinct()
            ->pluck('supplier_name')
            ->filter()
            ->sort()
            ->values()
            ->take(10)
            ->map(function ($supplier) {
                return [
                    'id' => $supplier,
                    'text' => $supplier,
                ];
            });

        return response()->json($suppliers);
    }
}
