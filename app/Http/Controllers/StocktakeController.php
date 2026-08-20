<?php

namespace App\Http\Controllers;

use App\Http\Requests\StocktakeRequest;
use App\Models\DemoWorkspace;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StocktakeController extends Controller
{
    public function index(Request $request): View
    {
        $products = $this->products($request);

        return view('stocktake.index', [
            'title' => 'Stocktake',
            'products' => $products,
            'expected' => $products->mapWithKeys(fn (Product $product) => [$product->id => $product->stock])->all(),
            'counts' => old('counts', $products->mapWithKeys(fn (Product $product) => [$product->id => $product->stock])->all()),
        ]);
    }

    public function update(StocktakeRequest $request): RedirectResponse
    {
        /** @var DemoWorkspace $workspace */
        $workspace = $request->attributes->get('demoWorkspace');
        $counts = collect($request->validated('counts'));

        DB::transaction(function () use ($workspace, $counts): void {
            $products = $workspace->products()
                ->whereKey($counts->keys()->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $counts->each(function (int|string $count, string|int $id) use ($products): void {
                $products->get($id)->update(['stock' => (int) $count]);
            });
        });

        return redirect()->route('stocktake.index')->with('success', 'Stocktake saved. Inventory totals are now server-confirmed.');
    }

    private function products(Request $request)
    {
        /** @var DemoWorkspace $workspace */
        $workspace = $request->attributes->get('demoWorkspace');

        return $workspace->products()->orderBy('stock')->orderBy('name')->limit(12)->get();
    }
}
