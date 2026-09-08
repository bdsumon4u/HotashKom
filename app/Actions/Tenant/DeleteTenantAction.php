<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteTenantAction
{
    /**
     * Completely delete a tenant and all its associated data across all tables.
     */
    public function execute(Tenant|string $tenant): void
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        DB::transaction(function () use ($tenantId) {
            // 1. Find product, category, and section IDs for pivot table cleanup
            $productIds = [];
            if (Schema::hasTable('products') && Schema::hasColumn('products', 'tenant_id')) {
                $productIds = DB::table('products')->where('tenant_id', $tenantId)->pluck('id')->all();
            }

            $categoryIds = [];
            if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'tenant_id')) {
                $categoryIds = DB::table('categories')->where('tenant_id', $tenantId)->pluck('id')->all();
            }

            $sectionIds = [];
            if (Schema::hasTable('home_sections') && Schema::hasColumn('home_sections', 'tenant_id')) {
                $sectionIds = DB::table('home_sections')->where('tenant_id', $tenantId)->pluck('id')->all();
            }

            $reviewIds = [];
            if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'tenant_id')) {
                $reviewIds = DB::table('reviews')->where('tenant_id', $tenantId)->pluck('id')->all();
            }

            // 2. Clean up child / pivot records that reference tenant entities
            if (! empty($productIds)) {
                if (Schema::hasTable('image_product')) {
                    DB::table('image_product')->whereIn('product_id', $productIds)->delete();
                }
                if (Schema::hasTable('option_product')) {
                    DB::table('option_product')->whereIn('product_id', $productIds)->delete();
                }
                if (Schema::hasTable('product_purchases')) {
                    DB::table('product_purchases')->whereIn('product_id', $productIds)->delete();
                }
                if (Schema::hasTable('product_purchase')) {
                    DB::table('product_purchase')->whereIn('product_id', $productIds)->delete();
                }
            }

            if (Schema::hasTable('category_product')) {
                $catProductQuery = DB::table('category_product');
                if (! empty($productIds) && ! empty($categoryIds)) {
                    $catProductQuery->where(function ($q) use ($productIds, $categoryIds) {
                        $q->whereIn('product_id', $productIds)
                            ->orWhereIn('category_id', $categoryIds);
                    })->delete();
                } elseif (! empty($productIds)) {
                    $catProductQuery->whereIn('product_id', $productIds)->delete();
                } elseif (! empty($categoryIds)) {
                    $catProductQuery->whereIn('category_id', $categoryIds)->delete();
                }
            }

            if (Schema::hasTable('category_home_section')) {
                $catSectionQuery = DB::table('category_home_section');
                if (! empty($sectionIds) && ! empty($categoryIds)) {
                    $catSectionQuery->where(function ($q) use ($sectionIds, $categoryIds) {
                        $q->whereIn('home_section_id', $sectionIds)
                            ->orWhereIn('category_id', $categoryIds);
                    })->delete();
                } elseif (! empty($sectionIds)) {
                    $catSectionQuery->whereIn('home_section_id', $sectionIds)->delete();
                } elseif (! empty($categoryIds)) {
                    $catSectionQuery->whereIn('category_id', $categoryIds)->delete();
                }
            }

            if (! empty($reviewIds) && Schema::hasTable('ratings')) {
                DB::table('ratings')->whereIn('review_id', $reviewIds)->delete();
            }

            // 3. Dynamically discover and delete from all tables having tenant_id column
            $tablesWithTenantId = DB::select(
                'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
                ['tenant_id']
            );

            foreach ($tablesWithTenantId as $row) {
                $tableName = $row->TABLE_NAME;
                // Exclude tenants table itself for last step
                if ($tableName !== 'tenants' && Schema::hasTable($tableName)) {
                    DB::table($tableName)->where('tenant_id', $tenantId)->delete();
                }
            }

            // 4. Delete domains
            if (Schema::hasTable('domains')) {
                DB::table('domains')->where('tenant_id', $tenantId)->delete();
            }

            // 5. Delete tenant record
            DB::table('tenants')->where('id', $tenantId)->delete();
        });
    }
}
