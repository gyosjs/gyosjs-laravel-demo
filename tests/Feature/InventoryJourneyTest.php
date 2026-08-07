<?php

use App\Models\DemoWorkspace;
use App\Models\Product;

test('a first visit creates an isolated seeded workspace', function () {
    $this->get('/products')->assertOk()->assertSee('Product catalog');

    $workspaceId = session('demo_workspace_id');

    expect($workspaceId)->not->toBeNull();
    expect(DemoWorkspace::find($workspaceId))->not->toBeNull();
    expect(Product::where('workspace_id', $workspaceId)->count())->toBe(36);
});

test('separate sessions cannot see each others data', function () {
    $this->get('/products');
    $firstWorkspace = session('demo_workspace_id');
    $foreignProduct = Product::where('workspace_id', $firstWorkspace)->firstOrFail();

    $this->flushSession();
    $this->get('/products');
    $secondWorkspace = session('demo_workspace_id');

    expect($secondWorkspace)->not->toBe($firstWorkspace);
    $this->get("/products/{$foreignProduct->id}")->assertNotFound();
});

test('catalog search and filters remain server owned query parameters', function () {
    $response = $this->get('/products?q=Arc&category=Audio&status=active&sort=name');

    $response->assertOk()->assertSee('Arc Headphones')->assertDontSee('Grid Keyboard');
});

test('quick view and load more return stable fragment targets', function () {
    $this->get('/products');
    $product = Product::where('workspace_id', session('demo_workspace_id'))->firstOrFail();

    $this->get("/products/{$product->id}/quick-view")
        ->assertOk()
        ->assertSee('id="modal-shell"', false)
        ->assertSee($product->name);

    $this->get('/products/load-more?page=2')
        ->assertOk()
        ->assertSee('id="product-grid"', false)
        ->assertSee('data-product-id=', false);
});

test('server validation redirects back with old input and errors', function () {
    $this->get('/products/create');

    $this->from('/products/create')->post('/products', [
        'name' => '',
        'sku' => 'INVALID-DEMO',
        'category' => 'Audio',
        'status' => 'active',
        'price' => -10,
        'stock' => -1,
    ])->assertRedirect('/products/create')
        ->assertSessionHasErrors(['name', 'price', 'stock'])
        ->assertSessionHasInput('sku', 'INVALID-DEMO');
});

test('a valid create follows the post redirect get contract', function () {
    $this->get('/products/create');

    $response = $this->post('/products', [
        'name' => 'Signal Work Lamp',
        'sku' => 'GY-TEST-001',
        'category' => 'Lighting',
        'status' => 'active',
        'price' => 119.90,
        'stock' => 14,
        'description' => 'Created by the feature test.',
    ]);

    $product = Product::where('workspace_id', session('demo_workspace_id'))->where('sku', 'GY-TEST-001')->firstOrFail();
    $response->assertRedirect(route('products.show', $product))->assertSessionHas('success');
    $this->get(route('products.show', $product))->assertOk()->assertSee('Signal Work Lamp');
});

test('sku uniqueness is limited to the current workspace', function () {
    $this->get('/products');
    $existing = Product::where('workspace_id', session('demo_workspace_id'))->firstOrFail();

    $this->post('/products', [
        'name' => 'Duplicate',
        'sku' => $existing->sku,
        'category' => 'Desk',
        'status' => 'draft',
        'price' => 10,
        'stock' => 1,
    ])->assertSessionHasErrors('sku');
});

test('delete removes only a product in the active workspace', function () {
    $this->get('/products');
    $workspaceId = session('demo_workspace_id');
    $product = Product::where('workspace_id', $workspaceId)->firstOrFail();

    $this->delete("/products/{$product->id}")
        ->assertRedirect('/products')
        ->assertSessionHas('success');

    expect(Product::whereKey($product->id)->exists())->toBeFalse();
    expect(Product::where('workspace_id', $workspaceId)->count())->toBe(35);
});

test('reset restores exactly the original dataset', function () {
    $this->get('/products');
    $workspaceId = session('demo_workspace_id');
    Product::where('workspace_id', $workspaceId)->limit(5)->delete();

    $this->post('/demo/reset')->assertRedirect('/products')->assertSessionHas('success');

    expect(Product::where('workspace_id', $workspaceId)->count())->toBe(36);
});
