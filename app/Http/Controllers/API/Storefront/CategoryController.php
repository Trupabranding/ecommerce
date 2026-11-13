<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Storefront;

use Domain\Shop\Category\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController
{
    public function index(): JsonResponse
    {
        $categories = Category::with(['media', 'products'])
            ->where('is_visible', true)
            ->whereNull('parent_uuid')
            ->orderBy('order_column')
            ->get()
            ->map(function (Category $category) {
                return [
                    'uuid' => $category->uuid,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $category->getFirstMediaUrl('image'),
                    'product_count' => $category->products()->count(),
                ];
            });

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function show(string $uuid): JsonResponse
    {
        $category = Category::with(['media', 'products'])
            ->findOrFail($uuid);

        return response()->json([
            'data' => [
                'uuid' => $category->uuid,
                'name' => $category->name,
                'description' => $category->description,
                'image' => $category->getFirstMediaUrl('image'),
                'product_count' => $category->products()->count(),
            ],
        ]);
    }
}
