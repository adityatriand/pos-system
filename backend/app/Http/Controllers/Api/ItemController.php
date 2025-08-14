<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Item::all();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:food,beverage',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'unit' => 'required|string|max:50',
            'is_available' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        $item = Item::create($validated);
        return response()->json($item, 201);
    }

    public function show(Item $item): JsonResponse
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'category' => 'in:food,beverage',
            'price' => 'numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'unit' => 'string|max:50',
            'is_available' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy(Item $item): JsonResponse
    {
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }

    public function byCategory(string $category): JsonResponse
    {
        if (!in_array($category, ['food', 'beverage'])) {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        $items = Item::where('category', $category)->available()->get();
        return response()->json($items);
    }
}