<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = !empty($validated['start_date']) 
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::today()->startOfDay();
            
        $endDate = !empty($validated['end_date']) 
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : Carbon::today()->endOfDay();

        $summary = Transaction::completed()
            ->byDateRange($startDate, $endDate)
            ->selectRaw('
                SUM(total_amount) as total_revenue,
                SUM(service_charge) as total_service_charge,
                COUNT(*) as total_transactions
            ')
            ->first();

        return response()->json([
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'total_revenue' => $summary->total_revenue ?? 0,
            'total_service_charge' => $summary->total_service_charge ?? 0,
            'total_transactions' => $summary->total_transactions ?? 0,
        ]);
    }

    public function topItems(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'integer|min:1|max:20',
        ]);

        $startDate = !empty($validated['start_date']) 
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::today()->startOfDay();
            
        $endDate = !empty($validated['end_date']) 
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : Carbon::today()->endOfDay();
            
        $limit = $validated['limit'] ?? 5;

        $topItems = DB::table('transaction_items')
            ->join('items', 'transaction_items.item_id', '=', 'items.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                'items.id',
                'items.name',
                'items.category',
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.total_price) as total_revenue')
            )
            ->groupBy('items.id', 'items.name', 'items.category')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        return response()->json($topItems);
    }

    public function branchRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = !empty($validated['start_date']) 
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::today()->startOfDay();
            
        $endDate = !empty($validated['end_date']) 
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : Carbon::today()->endOfDay();

        $branchRevenue = DB::table('branches')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('branches.id', '=', 'transactions.branch_id')
                     ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
                     ->where('transactions.status', 'completed');
            })
            ->select(
                'branches.id',
                'branches.name',
                DB::raw('COALESCE(SUM(transactions.total_amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(transactions.service_charge), 0) as total_service_charge'),
                DB::raw('COALESCE(COUNT(transactions.id), 0) as total_transactions')
            )
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total_revenue')
            ->get();

        return response()->json($branchRevenue);
    }

    public function inventoryStatus(): JsonResponse
    {
        // Get low stock items across all branches
        $lowStockItems = DB::table('branch_items')
            ->join('items', 'branch_items.item_id', '=', 'items.id')
            ->join('branches', 'branch_items.branch_id', '=', 'branches.id')
            ->whereRaw('branch_items.stock_quantity <= branch_items.min_stock_level')
            ->where('branch_items.is_available', true)
            ->select(
                'items.name as item_name',
                'items.category',
                'branches.name as branch_name',
                'branch_items.stock_quantity',
                'branch_items.min_stock_level'
            )
            ->get();

        // Get total items per branch
        $branchInventorySummary = DB::table('branches')
            ->leftJoin('branch_items', 'branches.id', '=', 'branch_items.branch_id')
            ->select(
                'branches.id',
                'branches.name',
                DB::raw('COALESCE(COUNT(branch_items.id), 0) as total_items'),
                DB::raw('COALESCE(SUM(branch_items.stock_quantity), 0) as total_stock'),
                DB::raw('COALESCE(SUM(CASE WHEN branch_items.stock_quantity <= branch_items.min_stock_level THEN 1 ELSE 0 END), 0) as low_stock_items')
            )
            ->where('branches.is_active', true)
            ->groupBy('branches.id', 'branches.name')
            ->get();

        return response()->json([
            'low_stock_items' => $lowStockItems,
            'branch_inventory_summary' => $branchInventorySummary
        ]);
    }
}