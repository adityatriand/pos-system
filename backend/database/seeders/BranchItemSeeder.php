<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Item;
use App\Models\BranchItem;

class BranchItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        $items = Item::all();

        // Assign all items to all branches with different stock levels
        foreach ($branches as $branch) {
            foreach ($items as $item) {
                // Vary stock levels per branch for demonstration
                $stockQuantity = $branch->id === 1 ? rand(20, 100) : rand(10, 50);
                
                BranchItem::create([
                    'branch_id' => $branch->id,
                    'item_id' => $item->id,
                    'stock_quantity' => $stockQuantity,
                    'min_stock_level' => 5,
                    'is_available' => true,
                ]);
            }
        }
    }
}
