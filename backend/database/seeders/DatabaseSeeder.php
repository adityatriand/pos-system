<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create branches
        $branch1 = Branch::create([
            'name' => 'Main Branch',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'phone' => '021-12345678',
            'email' => 'main@possystem.com',
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'name' => 'Branch 2',
            'address' => 'Jl. Thamrin No. 456, Jakarta Pusat',
            'phone' => '021-87654321',
            'email' => 'branch2@possystem.com',
            'is_active' => true,
        ]);

        // Create employees
        Employee::create([
            'name' => 'John Doe',
            'email' => 'john@possystem.com',
            'phone' => '081234567890',
            'position' => 'Manager',
            'branch_id' => $branch1->id,
            'salary' => 8000000,
            'hire_date' => '2024-01-01',
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'Jane Smith',
            'email' => 'jane@possystem.com',
            'phone' => '081234567891',
            'position' => 'Cashier',
            'branch_id' => $branch1->id,
            'salary' => 5000000,
            'hire_date' => '2024-02-01',
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'Bob Wilson',
            'email' => 'bob@possystem.com',
            'phone' => '081234567892',
            'position' => 'Manager',
            'branch_id' => $branch2->id,
            'salary' => 8000000,
            'hire_date' => '2024-01-15',
            'is_active' => true,
        ]);

        // Create food items
        $foods = [
            ['name' => 'Nasi Gudeg', 'description' => 'Traditional Javanese dish', 'price' => 25000, 'stock' => 50, 'unit' => 'portion'],
            ['name' => 'Gado-Gado', 'description' => 'Indonesian salad with peanut sauce', 'price' => 20000, 'stock' => 30, 'unit' => 'portion'],
            ['name' => 'Rendang', 'description' => 'Spicy beef curry', 'price' => 35000, 'stock' => 25, 'unit' => 'portion'],
            ['name' => 'Sate Ayam', 'description' => 'Grilled chicken skewers', 'price' => 30000, 'stock' => 40, 'unit' => 'portion'],
            ['name' => 'Nasi Fried Rice', 'description' => 'Indonesian fried rice', 'price' => 22000, 'stock' => 35, 'unit' => 'portion'],
        ];

        foreach ($foods as $food) {
            Item::create([
                'name' => $food['name'],
                'description' => $food['description'],
                'category' => 'food',
                'price' => $food['price'],
                'stock_quantity' => $food['stock'],
                'unit' => $food['unit'],
                'is_available' => true,
            ]);
        }

        // Create beverage items
        $beverages = [
            ['name' => 'Es Teh Manis', 'description' => 'Sweet iced tea', 'price' => 8000, 'stock' => 100, 'unit' => 'glass'],
            ['name' => 'Es Jeruk', 'description' => 'Fresh orange juice', 'price' => 12000, 'stock' => 60, 'unit' => 'glass'],
            ['name' => 'Kopi Tubruk', 'description' => 'Traditional Indonesian coffee', 'price' => 10000, 'stock' => 80, 'unit' => 'cup'],
            ['name' => 'Jus Alpukat', 'description' => 'Avocado juice', 'price' => 15000, 'stock' => 40, 'unit' => 'glass'],
            ['name' => 'Air Mineral', 'description' => 'Mineral water', 'price' => 5000, 'stock' => 200, 'unit' => 'bottle'],
        ];

        foreach ($beverages as $beverage) {
            Item::create([
                'name' => $beverage['name'],
                'description' => $beverage['description'],
                'category' => 'beverage',
                'price' => $beverage['price'],
                'stock_quantity' => $beverage['stock'],
                'unit' => $beverage['unit'],
                'is_available' => true,
            ]);
        }

        // Seed branch items
        $this->call(BranchItemSeeder::class);
    }
}
