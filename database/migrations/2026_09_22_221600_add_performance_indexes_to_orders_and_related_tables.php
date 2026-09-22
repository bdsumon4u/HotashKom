<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tenancyEnabled = (bool) config('tenancy.enabled', false);

        // 1. Orders table performance indexes
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) use ($tenancyEnabled): void {
                if (! $this->indexExists('orders', 'orders_phone_index')) {
                    $table->index('phone', 'orders_phone_index');
                }

                if (! $this->indexExists('orders', 'orders_created_at_index')) {
                    $table->index('created_at', 'orders_created_at_index');
                }

                if (Schema::hasColumn('orders', 'status_at') && ! $this->indexExists('orders', 'orders_status_at_index')) {
                    $table->index('status_at', 'orders_status_at_index');
                }

                if (Schema::hasColumn('orders', 'shipped_at') && ! $this->indexExists('orders', 'orders_shipped_at_index')) {
                    $table->index('shipped_at', 'orders_shipped_at_index');
                }

                if (Schema::hasColumn('orders', 'confirmed_at') && ! $this->indexExists('orders', 'orders_confirmed_at_index')) {
                    $table->index('confirmed_at', 'orders_confirmed_at_index');
                }

                if (Schema::hasColumn('orders', 'returned_at') && ! $this->indexExists('orders', 'orders_returned_at_index')) {
                    $table->index('returned_at', 'orders_returned_at_index');
                }

                if ($tenancyEnabled && Schema::hasColumn('orders', 'tenant_id')) {
                    if (! $this->indexExists('orders', 'orders_tenant_status_id_index')) {
                        $table->index(['tenant_id', 'status', 'id'], 'orders_tenant_status_id_index');
                    }
                    if (! $this->indexExists('orders', 'orders_tenant_created_at_index')) {
                        $table->index(['tenant_id', 'created_at'], 'orders_tenant_created_at_index');
                    }
                    if (! $this->indexExists('orders', 'orders_tenant_phone_index')) {
                        $table->index(['tenant_id', 'phone'], 'orders_tenant_phone_index');
                    }
                    if (! $this->indexExists('orders', 'orders_tenant_admin_status_index')) {
                        $table->index(['tenant_id', 'admin_id', 'status'], 'orders_tenant_admin_status_index');
                    }
                } else {
                    if (! $this->indexExists('orders', 'orders_status_id_index')) {
                        $table->index(['status', 'id'], 'orders_status_id_index');
                    }
                    if (! $this->indexExists('orders', 'orders_admin_status_index')) {
                        $table->index(['admin_id', 'status'], 'orders_admin_status_index');
                    }
                }
            });
        }

        // 2. Shopping cart table index for sidebar counter
        if (Schema::hasTable('shopping_cart')) {
            Schema::table('shopping_cart', function (Blueprint $table) use ($tenancyEnabled): void {
                if ($tenancyEnabled && Schema::hasColumn('shopping_cart', 'tenant_id')) {
                    if (! $this->indexExists('shopping_cart', 'shopping_cart_tenant_updated_at_index')) {
                        $table->index(['tenant_id', 'updated_at'], 'shopping_cart_tenant_updated_at_index');
                    }
                } else {
                    if (! $this->indexExists('shopping_cart', 'shopping_cart_updated_at_index')) {
                        $table->index('updated_at', 'shopping_cart_updated_at_index');
                    }
                }
            });
        }

        // 3. Reviews table index for pending reviews count
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) use ($tenancyEnabled): void {
                if ($tenancyEnabled && Schema::hasColumn('reviews', 'tenant_id')) {
                    if (! $this->indexExists('reviews', 'reviews_tenant_approved_index')) {
                        $table->index(['tenant_id', 'approved'], 'reviews_tenant_approved_index');
                    }
                } else {
                    if (! $this->indexExists('reviews', 'reviews_approved_index')) {
                        $table->index('approved', 'reviews_approved_index');
                    }
                }
            });
        }

        // 4. Order notes table index for latestOfMany and withCount
        if (Schema::hasTable('order_notes')) {
            Schema::table('order_notes', function (Blueprint $table) use ($tenancyEnabled): void {
                if ($tenancyEnabled && Schema::hasColumn('order_notes', 'tenant_id')) {
                    if (! $this->indexExists('order_notes', 'order_notes_tenant_order_id_index')) {
                        $table->index(['tenant_id', 'order_id', 'id'], 'order_notes_tenant_order_id_index');
                    }
                } else {
                    if (! $this->indexExists('order_notes', 'order_notes_order_id_id_index')) {
                        $table->index(['order_id', 'id'], 'order_notes_order_id_id_index');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table): void {
                $indexes = [
                    'orders_phone_index',
                    'orders_created_at_index',
                    'orders_status_at_index',
                    'orders_shipped_at_index',
                    'orders_confirmed_at_index',
                    'orders_returned_at_index',
                    'orders_tenant_status_id_index',
                    'orders_tenant_created_at_index',
                    'orders_tenant_phone_index',
                    'orders_tenant_admin_status_index',
                    'orders_status_id_index',
                    'orders_admin_status_index',
                ];

                foreach ($indexes as $index) {
                    if ($this->indexExists('orders', $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }

        if (Schema::hasTable('shopping_cart')) {
            Schema::table('shopping_cart', function (Blueprint $table): void {
                $indexes = [
                    'shopping_cart_tenant_updated_at_index',
                    'shopping_cart_updated_at_index',
                ];

                foreach ($indexes as $index) {
                    if ($this->indexExists('shopping_cart', $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $indexes = [
                    'reviews_tenant_approved_index',
                    'reviews_approved_index',
                ];

                foreach ($indexes as $index) {
                    if ($this->indexExists('reviews', $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }

        if (Schema::hasTable('order_notes')) {
            Schema::table('order_notes', function (Blueprint $table): void {
                $indexes = [
                    'order_notes_tenant_order_id_index',
                    'order_notes_order_id_id_index',
                ];

                foreach ($indexes as $index) {
                    if ($this->indexExists('order_notes', $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $index): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        return collect(Schema::getIndexes($table))
            ->contains(fn ($i) => $i['name'] === $index);
    }
};
