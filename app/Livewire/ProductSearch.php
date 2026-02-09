<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

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

    public function updatedQuery(): void
    {
        $this->resetPage();
    }

    public function updatedCategories(): void
    {
        $this->resetPage();
    }

    public function updatedBrands(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->categories = [];
        $this->brands = [];
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->sort = 'relevance';
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.product-search', [
            'products' => $this->getProducts(),
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
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Product>
     */
    private function getProducts(): LengthAwarePaginator
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

        return $query->paginate(12);
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
