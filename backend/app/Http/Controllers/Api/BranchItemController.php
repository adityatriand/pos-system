<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BranchItem;
use App\Models\Branch;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $branchId = $request->get('branch_id');
        
        $query = BranchItem::with(['item', 'branch']);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $branchItems = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $branchItems
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'item_id' => 'required|exists:items,id',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'integer|min:0',
            'is_available' => 'boolean',
        ]);

        // Check if this branch-item combination already exists
        $existingBranchItem = BranchItem::where('branch_id', $validated['branch_id'])
            ->where('item_id', $validated['item_id'])
            ->first();

        if ($existingBranchItem) {
            return response()->json([
                'success' => false,
                'message' => 'This item is already assigned to this branch'
            ], 422);
        }

        $branchItem = BranchItem::create($validated);
        $branchItem->load(['item', 'branch']);

        return response()->json([
            'success' => true,
            'data' => $branchItem,
            'message' => 'Branch item created successfully'
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $branchItem = BranchItem::with(['item', 'branch'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $branchItem
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $branchItem = BranchItem::findOrFail($id);

        $validated = $request->validate([
            'stock_quantity' => 'integer|min:0',
            'min_stock_level' => 'integer|min:0',
            'is_available' => 'boolean',
        ]);

        $branchItem->update($validated);
        $branchItem->load(['item', 'branch']);

        return response()->json([
            'success' => true,
            'data' => $branchItem,
            'message' => 'Branch item updated successfully'
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $branchItem = BranchItem::findOrFail($id);
        $branchItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch item removed successfully'
        ]);
    }

    public function getBranchItems(int $branchId): JsonResponse
    {
        $branch = Branch::findOrFail($branchId);
        
        $branchItems = BranchItem::with('item')
            ->where('branch_id', $branchId)
            ->where('is_available', true)
            ->get()
            ->map(function ($branchItem) {
                return [
                    'id' => $branchItem->item->id,
                    'name' => $branchItem->item->name,
                    'description' => $branchItem->item->description,
                    'category' => $branchItem->item->category,
                    'price' => $branchItem->item->price,
                    'unit' => $branchItem->item->unit,
                    'image_url' => $branchItem->item->image_url,
                    'stock_quantity' => $branchItem->stock_quantity,
                    'min_stock_level' => $branchItem->min_stock_level,
                    'is_available' => $branchItem->is_available && $branchItem->stock_quantity > 0,
                    'is_low_stock' => $branchItem->isLowStock(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $branchItems
        ]);
    }

    public function addItemsToBranch(Request $request, int $branchId): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.stock_quantity' => 'required|integer|min:0',
            'items.*.min_stock_level' => 'integer|min:0',
        ]);

        $branch = Branch::findOrFail($branchId);
        $created = [];
        $errors = [];

        foreach ($validated['items'] as $itemData) {
            $existingBranchItem = BranchItem::where('branch_id', $branchId)
                ->where('item_id', $itemData['item_id'])
                ->first();

            if ($existingBranchItem) {
                $errors[] = "Item ID {$itemData['item_id']} is already assigned to this branch";
                continue;
            }

            $branchItem = BranchItem::create([
                'branch_id' => $branchId,
                'item_id' => $itemData['item_id'],
                'stock_quantity' => $itemData['stock_quantity'],
                'min_stock_level' => $itemData['min_stock_level'] ?? 0,
                'is_available' => true,
            ]);

            $branchItem->load('item');
            $created[] = $branchItem;
        }

        return response()->json([
            'success' => empty($errors),
            'data' => $created,
            'errors' => $errors,
            'message' => count($created) > 0 ? 'Items added to branch successfully' : 'No items were added'
        ]);
    }
}
