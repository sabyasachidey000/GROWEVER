<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index()
    {
        $productTypes = ProductType::orderBy('code')->paginate(25);
        return view('product-types.index', compact('productTypes'));
    }

    public function create()
    {
        return view('product-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:product_types',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $productType = ProductType::create($validated);

        $this->activityLog()->log(
            "Product Type {$productType->name} ({$productType->code}) created",
            $productType,
            'created',
            'product_types'
        );

        return redirect()->route('product-types.index')
            ->with('success', 'Product Type created successfully.');
    }

    public function show(ProductType $productType)
    {
        $productType->load('products');
        return view('product-types.show', compact('productType'));
    }

    public function edit(ProductType $productType)
    {
        return view('product-types.edit', compact('productType'));
    }

    public function update(Request $request, ProductType $productType)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:product_types,code,' . $productType->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $productType->update($validated);

        $this->activityLog()->log(
            "Product Type {$productType->name} updated",
            $productType,
            'updated',
            'product_types'
        );

        return redirect()->route('product-types.index')
            ->with('success', 'Product Type updated successfully.');
    }

    public function destroy(ProductType $productType)
    {
        $productType->delete();

        $this->activityLog()->log(
            "Product Type {$productType->name} deleted",
            $productType,
            'deleted',
            'product_types'
        );

        return redirect()->route('product-types.index')
            ->with('success', 'Product Type deleted successfully.');
    }
}
