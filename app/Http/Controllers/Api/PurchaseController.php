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

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('products_count', fn ($purchase) => $purchase->productPurchases->count())
            ->addColumn('formatted_date', fn ($purchase) => $purchase->purchase_date ? $purchase->purchase_date->format('d M Y') : '-')
            ->filterColumn('formatted_date', function ($query, $keyword): void {
                // Date search is handled in the filter() method
            })
            ->addColumn('formatted_amount', fn ($purchase): string => number_format($purchase->total_amount ?? 0, 2).' BDT')
            ->addColumn('supplier_display', fn ($purchase) => $purchase->supplier_name ?? '-')
            ->addColumn('admin_display', fn ($purchase) => $purchase->admin ? $purchase->admin->name : '-')
            ->addColumn('actions', function ($purchase) {
                $buttons = '<div class="btn-group" role="group">';
                $buttons .= '<a target="_blank" href="'.route('admin.purchases.show', $purchase).'" class="btn btn-sm btn-info" title="View">
                    <i class="fa fa-eye"></i>
                </a>';
                $buttons .= '<a href="'.route('admin.purchases.edit', $purchase).'" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>';
                $buttons .= '<button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="confirmDelete('.$purchase->id.')">
                    <i class="fa fa-trash"></i>
                </button>';

                return $buttons.'</div>';
            })
            ->rawColumns(['actions'])
            ->filter(function ($query) use ($request): void {
                $searchValue = $request->input('search.value');
                if ($searchValue) {
                    $date = \DateTime::createFromFormat('d M Y', $searchValue);
                    if ($date) {
                        $query->whereDate('purchase_date', $date->format('Y-m-d'));
                    } else {
                        $query->where(function ($q) use ($searchValue): void {
                            $q->where('supplier_name', 'like', "%{$searchValue}%")
                                ->orWhereHas('admin', function ($adminQuery) use ($searchValue): void {
                                    $adminQuery->where('name', 'like', "%{$searchValue}%");
                                });
                        });
                    }
                }
            })
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
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')
            ->take(10)
            ->get(['id', 'name', 'sku'])
            ->map(fn ($product): array => [
                'id' => $product->id,
                'text' => $product->name.' ('.$product->sku.')',
            ]);

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
            ->map(fn ($supplier): array => [
                'id' => $supplier,
                'text' => $supplier,
            ]);

        return response()->json($suppliers);
    }
}
