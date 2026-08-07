<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\DemoWorkspace;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = $this->filtered($request)->paginate(12)->withQueryString();

        return view('products.index', [
            'title' => 'Product catalog',
            'products' => $products,
            'categories' => Product::CATEGORIES,
            'statuses' => Product::STATUSES,
        ]);
    }

    public function loadMore(Request $request): View
    {
        return view('products.partials.grid', [
            'products' => $this->filtered($request)->paginate(12)->withQueryString(),
            'appendResponse' => true,
        ]);
    }

    public function show(Request $request, int $product): View
    {
        $product = $this->product($request, $product);

        return view('products.show', ['title' => $product->name, 'product' => $product]);
    }

    public function quickView(Request $request, int $product): View
    {
        return view('products.partials.quick-view', [
            'product' => $this->product($request, $product),
        ]);
    }

    public function create(): View
    {
        return view('products.form', [
            'title' => 'Add product',
            'product' => new Product(['status' => 'draft', 'stock' => 0]),
            'categories' => Product::CATEGORIES,
            'statuses' => Product::STATUSES,
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = $this->workspace($request)->products()->create($request->validated() + [
            'image_tone' => 'amber',
        ]);

        return redirect()->route('products.show', $product)->with('success', 'Product created. The server redirected to its detail page.');
    }

    public function edit(Request $request, int $product): View
    {
        return view('products.form', [
            'title' => 'Edit product',
            'product' => $this->product($request, $product),
            'categories' => Product::CATEGORIES,
            'statuses' => Product::STATUSES,
        ]);
    }

    public function update(ProductRequest $request, int $product): RedirectResponse
    {
        $product = $this->product($request, $product);
        $product->update($request->validated());

        return redirect()->route('products.show', $product)->with('success', 'Product saved through POST/Redirect/GET.');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $this->product($request, $product)->delete();

        return redirect()->route('products.index')->with('success', 'Product removed from your demo workspace.');
    }

    private function filtered(Request $request): Builder
    {
        $query = Product::query()->forWorkspace($this->workspace($request));
        $search = trim((string) $request->query('q'));

        $query->when($search !== '', function (Builder $query) use ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
            });
        });
        $query->when($request->filled('category'), fn (Builder $query) => $query->where('category', $request->string('category')));
        $query->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')));
        $query->when($request->boolean('low_stock'), fn (Builder $query) => $query->where('stock', '<', 12));

        return match ($request->query('sort')) {
            'name' => $query->orderBy('name'),
            'price_high' => $query->orderByDesc('price'),
            'stock_low' => $query->orderBy('stock'),
            default => $query->latest('updated_at'),
        };
    }

    private function product(Request $request, int $id): Product
    {
        return $this->workspace($request)->products()->findOrFail($id);
    }

    private function workspace(Request $request): DemoWorkspace
    {
        return $request->attributes->get('demoWorkspace');
    }
}
