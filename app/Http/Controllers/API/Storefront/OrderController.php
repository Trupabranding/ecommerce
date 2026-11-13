<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Storefront;

use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'shipping' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'discount_code' => 'nullable|string',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'email' => $validated['email'],
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'],
            'payment_method' => $validated['payment_method'],
            'subtotal' => $validated['subtotal'],
            'tax' => $validated['tax'],
            'shipping' => $validated['shipping'],
            'discount' => $validated['discount'] ?? 0,
            'discount_code' => $validated['discount_code'],
            'total' => $validated['subtotal'] + $validated['tax'] + $validated['shipping'] - ($validated['discount'] ?? 0),
            'status' => 'pending',
            'tracking_info' => [],
        ]);

        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_price' => $item['product_price'],
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
                'quantity' => $item['quantity'],
                'total' => $item['total'],
            ]);
        }

        return response()->json([
            'data' => $order,
            'order_number' => $order->order_number,
        ], 201);
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::with(['items'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'data' => $order,
            'items' => $order->items,
        ]);
    }
}
