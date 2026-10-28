<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('productType')->orderBy('product_code')->paginate(25);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $productTypes = ProductType::active()->orderBy('name')->get();
        return view('products.create', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products',
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'hsn_code' => 'nullable|string|max:20',
            'product_category_master_base' => 'nullable|string|max:100',
            'product_type_master_base' => 'nullable|string|max:100',
            'product_type_id' => 'required|exists:product_types,id',
            'unit_of_measure' => 'required|string|max:20',
            'target_cycle_time' => 'nullable|numeric',
            'standard_production_rate' => 'nullable|numeric',
            'specifications' => 'nullable|string',
            'drawing_reference' => 'nullable|string',
            'material_grade' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|string|max:200',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);

        $this->activityLog()->log(
            "Product {$product->name} ({$product->product_code}) created",
            $product,
            'created',
            'products'
        );

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['productionRuns' => fn($q) => $q->latest()->limit(10)]);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('productType');
        $productTypes = ProductType::active()->orderBy('name')->get();
        return view('products.edit', compact('product', 'productTypes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $product->id,
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'hsn_code' => 'nullable|string|max:20',
            'product_category_master_base' => 'nullable|string|max:100',
            'product_type_master_base' => 'nullable|string|max:100',
            'product_type_id' => 'required|exists:product_types,id',
            'unit_of_measure' => 'required|string|max:20',
            'target_cycle_time' => 'nullable|numeric',
            'standard_production_rate' => 'nullable|numeric',
            'specifications' => 'nullable|string',
            'drawing_reference' => 'nullable|string',
            'material_grade' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|string|max:200',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        $this->activityLog()->log(
            "Product {$product->name} updated",
            $product,
            'updated',
            'products'
        );

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        $this->activityLog()->log(
            "Product {$product->name} deleted",
            $product,
            'deleted',
            'products'
        );

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function apiIndex(Request $request)
    {
        $query = Product::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        return response()->json($query->active()->orderBy('product_code')->paginate(25));
    }
}
