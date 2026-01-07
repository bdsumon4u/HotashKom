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
        // Products table indexes
        Schema::table('products', function (Blueprint $table): void {
            // Composite index for common query: is_active + parent_id
            if (! $this->indexExists('products', 'products_is_active_parent_id_index')) {
                $table->index(['is_active', 'parent_id'], 'products_is_active_parent_id_index');
            }

            // Index for parent_id lookups (variations)
            if (! $this->indexExists('products', 'products_parent_id_index')) {
                $table->index('parent_id', 'products_parent_id_index');
            }

            // Index for sorting queries
            if (! $this->indexExists('products', 'products_updated_at_index')) {
                $table->index('updated_at', 'products_updated_at_index');
            }

            // Index for selling_price sorting
            if (! $this->indexExists('products', 'products_selling_price_index')) {
                $table->index('selling_price', 'products_selling_price_index');
            }

            // Composite index for hot_sale and new_arrival
            if (! $this->indexExists('products', 'products_featured_index')) {
                $table->index(['hot_sale', 'new_arrival'], 'products_featured_index');
            }
        });

        // Category_product pivot table indexes
        Schema::table('category_product', function (Blueprint $table): void {
            // Index on category_id for filtering
            if (! $this->indexExists('category_product', 'category_product_category_id_index')) {
                $table->index('category_id', 'category_product_category_id_index');
            }

            // Index on product_id for reverse lookups
            if (! $this->indexExists('category_product', 'category_product_product_id_index')) {
                $table->index('product_id', 'category_product_product_id_index');
            }
        });

        // Option_product pivot table indexes
        if (Schema::hasTable('option_product')) {
            Schema::table('option_product', function (Blueprint $table): void {
                // Index on option_id for filtering
                if (! $this->indexExists('option_product', 'option_product_option_id_index')) {
                    $table->index('option_id', 'option_product_option_id_index');
                }

                // Index on product_id for reverse lookups
                if (! $this->indexExists('option_product', 'option_product_product_id_index')) {
                    $table->index('product_id', 'option_product_product_id_index');
                }
            });
        }

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table): void {
            // Index on parent_id for nested queries
            if (! $this->indexExists('categories', 'categories_parent_id_index')) {
                $table->index('parent_id', 'categories_parent_id_index');
            }

            // Index on slug for lookups
            if (! $this->indexExists('categories', 'categories_slug_index')) {
                $table->index('slug', 'categories_slug_index');
            }
        });

        // Attributes table indexes (if exists)
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table): void {
                // Index on name for lookups
                if (! $this->indexExists('attributes', 'attributes_name_index')) {
                    $table->index('name', 'attributes_name_index');
                }
            });
        }

        // Options table indexes (if exists)
        if (Schema::hasTable('options')) {
            Schema::table('options', function (Blueprint $table): void {
                // Index on attribute_id for filtering
                if (! $this->indexExists('options', 'options_attribute_id_index')) {
                    $table->index('attribute_id', 'options_attribute_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex('products_is_active_parent_id_index');
            $table->dropIndex('products_parent_id_index');
            $table->dropIndex('products_updated_at_index');
            $table->dropIndex('products_selling_price_index');
            $table->dropIndex('products_featured_index');
        });

        Schema::table('category_product', function (Blueprint $table): void {
            $table->dropIndex('category_product_category_id_index');
            $table->dropIndex('category_product_product_id_index');
        });

        if (Schema::hasTable('option_product')) {
            Schema::table('option_product', function (Blueprint $table): void {
                $table->dropIndex('option_product_option_id_index');
                $table->dropIndex('option_product_product_id_index');
            });
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex('categories_parent_id_index');
            $table->dropIndex('categories_slug_index');
        });

        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table): void {
                $table->dropIndex('attributes_name_index');
            });
        }

        if (Schema::hasTable('options')) {
            Schema::table('options', function (Blueprint $table): void {
                $table->dropIndex('options_attribute_id_index');
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        $tableName = $connection->getTablePrefix().$table;

        $result = $connection->select(
            'SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $tableName, $index]
        );

        return (int) $result[0]->count > 0;
    }
};
