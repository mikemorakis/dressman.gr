<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductSearch extends Component
{
    #[Url(as: 'q', except: '')]
    public string $query = '';

    /** @var array<int, string> */
    #[Url(as: 'cat', except: [])]
    public array $categories = [];

    /** @var array<int, string> */
    #[Url(as: 'brand', except: [])]
    public array $brands = [];

    #[Url(as: 'min', except: '')]
    public string $minPrice = '';

    #[Url(as: 'max', except: '')]
    public string $maxPrice = '';

    #[Url(as: 'sort', except: 'relevance')]
    public string $sort = 'relevance';

    public int $perPage = 12;

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    public function updatedQuery(): void
    {
        $this->perPage = 12;
    }

    public function updatedCategories(): void
    {
        $this->perPage = 12;
    }

    public function updatedBrands(): void
    {
        $this->perPage = 12;
    }

    public function updatedMinPrice(): void
    {
        $this->perPage = 12;
    }

    public function updatedMaxPrice(): void
    {
        $this->perPage = 12;
    }

    public function updatedSort(): void
    {
        $this->perPage = 12;
    }

    public function clearFilters(): void
    {
        $this->categories = [];
        $this->brands = [];
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->sort = 'relevance';
        $this->perPage = 12;
    }

    public function render(): View
    {
        $query = $this->buildQuery();
        $totalProducts = (clone $query)->count();
        $products = $query->take($this->perPage)->get();

        return view('livewire.product-search', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'hasMore' => $totalProducts > $this->perPage,
            'allCategories' => Category::visible()->orderBy('name')->get(),
            'allBrands' => Brand::visible()->orderBy('name')->get(),
            'hasActiveFilters' => ! empty($this->categories) || ! empty($this->brands)
                || $this->minPrice !== '' || $this->maxPrice !== '',
        ]);
    }

    public function getHasNonQueryFilters(): bool
    {
        return ! empty($this->categories) || ! empty($this->brands)
            || $this->minPrice !== '' || $this->maxPrice !== ''
            || $this->sort !== 'relevance';
    }

    /**
     * @return Builder<Product>
     */
    private function buildQuery(): Builder
    {
        /** @var Builder<Product> $query */
        $query = Product::active()
            ->withAvailableStock()
            ->with('images', 'labels');

        // Search
        if ($this->query !== '') {
            $this->applySearch($query, $this->query);
        }

        // Category filter
        if (! empty($this->categories)) {
            $ids = array_map('intval', $this->categories);
            $query->whereHas('categories', fn (Builder $q) => $q->whereIn('categories.id', $ids));
        }

        // Brand filter
        if (! empty($this->brands)) {
            $ids = array_map('intval', $this->brands);
            $query->whereIn('brand_id', $ids);
        }

        // Price range
        if ($this->minPrice !== '') {
            $query->where('price', '>=', (float) $this->minPrice);
        }

        if ($this->maxPrice !== '') {
            $query->where('price', '<=', (float) $this->maxPrice);
        }

        // Prevent duplicates from joins (e.g. whereHas on pivot tables)
        $query->distinct();

        // Sort
        match ($this->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            'newest' => $query->latest('published_at'),
            default => $this->query === '' ? $query->latest('published_at') : $query,
        };

        return $query;
    }

    private const MAX_SEARCH_TOKENS = 10;

    /**
     * @param  Builder<Product>  $query
     */
    private function applySearch(Builder $query, string $search): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            // Sanitize FULLTEXT special characters
            /** @var string $sanitized */
            $sanitized = preg_replace('/[+\-><(~*"@]+/', ' ', $search);
            $sanitized = trim($sanitized);

            if ($sanitized !== '') {
                /** @var list<string> $terms */
                $terms = preg_split('/\s+/', $sanitized);
                // Normalize: lowercase, remove short tokens (< 2 chars), cap count
                $terms = array_values(array_filter(
                    array_map('mb_strtolower', $terms),
                    fn (string $t): bool => mb_strlen($t) >= 2
                ));
                $terms = array_slice($terms, 0, self::MAX_SEARCH_TOKENS);

                if ($terms !== []) {
                    $boolean = implode(' ', array_map(fn (string $t): string => '+'.$t.'*', $terms));

                    $query->whereRaw(
                        'MATCH(name, short_description, description) AGAINST(? IN BOOLEAN MODE)',
                        [$boolean]
                    );
                }
            }
        } else {
            // SQLite fallback: prefix LIKE then contains LIKE
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'LIKE', $search.'%')
                    ->orWhere('name', 'LIKE', '%'.$search.'%');
            });
        }
    }
}
