<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--product-tags-per-sitemap=20000 : Max number of product URLs per generated sitemap file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml (index) and supporting sitemap files for public pages.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        if ($baseUrl === '') {
            $this->error('APP_URL is not set. Please set APP_URL in your .env so absolute sitemap URLs can be generated.');

            return self::FAILURE;
        }

        $productTagsPerSitemap = (int) $this->option('product-tags-per-sitemap');

        if ($productTagsPerSitemap < 1) {
            $this->error('Option --product-tags-per-sitemap must be at least 1.');

            return self::FAILURE;
        }

        $publicSitemapIndexPath = public_path('sitemap.xml');
        $publicSitemapsDir = public_path('sitemaps');

        File::ensureDirectoryExists($publicSitemapsDir);

        $index = SitemapIndex::create();

        $this->generateStaticSitemap($publicSitemapsDir, $index);
        $this->generateCategorySitemap($publicSitemapsDir, $index);
        $this->generateBrandSitemap($publicSitemapsDir, $index);
        $this->generatePageSitemap($publicSitemapsDir, $index);
        $this->generateProductSitemaps($publicSitemapsDir, $index, $productTagsPerSitemap);

        $index->writeToFile($publicSitemapIndexPath);

        $this->info("Sitemap index generated: {$publicSitemapIndexPath}");

        return self::SUCCESS;
    }

    private function generateStaticSitemap(string $publicSitemapsDir, SitemapIndex $index): void
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(url('/'))->setPriority(1.0))
            ->add(Url::create(url('/shop'))->setPriority(0.9))
            ->add(Url::create(url('/categories'))->setPriority(0.7))
            ->add(Url::create(url('/brands'))->setPriority(0.7))
            ->add(Url::create(url('/lead-form'))->setPriority(0.3))
            ->add(Url::create(url('/feed/catalog'))->setPriority(0.2))
            ->add(Url::create(url('/feed/catalog-simple'))->setPriority(0.2));

        $filename = 'static.xml';
        $sitemap->writeToFile($publicSitemapsDir.DIRECTORY_SEPARATOR.$filename);
        $index->add(url('sitemaps/'.$filename));
    }

    private function generateCategorySitemap(string $publicSitemapsDir, SitemapIndex $index): void
    {
        if (! Schema::hasTable((new Category)->getTable())) {
            return;
        }

        $sitemap = Sitemap::create();

        Category::query()
            ->where('is_enabled', true)
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(2000, function ($categories) use ($sitemap): void {
                foreach ($categories as $category) {
                    $sitemap->add(
                        Url::create(url('/category/'.$category->slug))
                            ->setLastModificationDate($category->updated_at)
                            ->setPriority(0.6)
                    );
                }
            });

        $filename = 'categories.xml';
        $sitemap->writeToFile($publicSitemapsDir.DIRECTORY_SEPARATOR.$filename);
        $index->add(url('sitemaps/'.$filename));
    }

    private function generateBrandSitemap(string $publicSitemapsDir, SitemapIndex $index): void
    {
        if (! Schema::hasTable((new Brand)->getTable())) {
            return;
        }

        $sitemap = Sitemap::create();

        Brand::query()
            ->where('is_enabled', true)
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(2000, function ($brands) use ($sitemap): void {
                foreach ($brands as $brand) {
                    $sitemap->add(
                        Url::create(url('/brand/'.$brand->slug))
                            ->setLastModificationDate($brand->updated_at)
                            ->setPriority(0.6)
                    );
                }
            });

        $filename = 'brands.xml';
        $sitemap->writeToFile($publicSitemapsDir.DIRECTORY_SEPARATOR.$filename);
        $index->add(url('sitemaps/'.$filename));
    }

    private function generatePageSitemap(string $publicSitemapsDir, SitemapIndex $index): void
    {
        if (! Schema::hasTable((new Page)->getTable())) {
            return;
        }

        $sitemap = Sitemap::create();

        Page::query()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(2000, function ($pages) use ($sitemap): void {
                foreach ($pages as $page) {
                    $sitemap->add(
                        Url::create(url('/'.$page->slug))
                            ->setLastModificationDate($page->updated_at)
                            ->setPriority(0.5)
                    );
                }
            });

        $filename = 'pages.xml';
        $sitemap->writeToFile($publicSitemapsDir.DIRECTORY_SEPARATOR.$filename);
        $index->add(url('sitemaps/'.$filename));
    }

    private function generateProductSitemaps(string $publicSitemapsDir, SitemapIndex $index, int $productTagsPerSitemap): void
    {
        if (! Schema::hasTable((new Product)->getTable())) {
            return;
        }

        $currentSitemap = Sitemap::create();
        $currentCount = 0;
        $fileIndex = 1;

        $flushCurrent = function () use (&$currentSitemap, &$currentCount, &$fileIndex, $publicSitemapsDir, $index): void {
            if ($currentCount < 1) {
                return;
            }

            $filename = "products-{$fileIndex}.xml";
            $currentSitemap->writeToFile($publicSitemapsDir.DIRECTORY_SEPARATOR.$filename);
            $index->add(url('sitemaps/'.$filename));

            $fileIndex++;
            $currentSitemap = Sitemap::create();
            $currentCount = 0;
        };

        Product::query()
            ->withoutGlobalScope('latest')
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(2000, function ($products) use (&$currentSitemap, &$currentCount, $productTagsPerSitemap, $flushCurrent): void {
                foreach ($products as $product) {
                    if ($currentCount >= $productTagsPerSitemap) {
                        $flushCurrent();
                    }

                    $currentSitemap->add(
                        Url::create(url('/products/'.$product->slug))
                            ->setLastModificationDate($product->updated_at)
                            ->setPriority(0.8)
                    );
                    $currentCount++;
                }
            });

        $flushCurrent();
    }
}
