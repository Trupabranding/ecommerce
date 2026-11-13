<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Storefront;

use Domain\Shop\Product\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'skus.attributeOptions', 'media'])
            ->where('status', 'active');

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->get('category'));
            });
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->has('sort')) {
            match ($request->get('sort')) {
                'price_asc' => $query->orderByPrice('asc'),
                'price_desc' => $query->orderByPrice('desc'),
                'newest' => $query->latest(),
                'popular' => $query->orderBy('order_column'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $products = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with([
            'category',
            'brand',
            'skus.attributeOptions',
            'media',
        ])->findOrFail($id);

        $related = Product::with(['category', 'brand', 'skus', 'media'])
            ->where('category_uuid', $product->category_uuid)
            ->where('uuid', '!=', $product->uuid)
            ->limit(4)
            ->get();

        return response()->json([
            'data' => $product,
            'related' => $related,
        ]);
    }

    public function byCategory(string $category): JsonResponse
    {
        $categoryModel = \Domain\Shop\Category\Models\Category::where('name', $category)
            ->orWhere('uuid', $category)
            ->firstOrFail();

        $products = Product::with(['category', 'brand', 'skus.attributeOptions', 'media'])
            ->where('category_uuid', $categoryModel->uuid)
            ->where('status', 'active')
            ->paginate(15);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }
}
