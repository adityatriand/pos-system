<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\BranchItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $transactions = Transaction::with(['branch', 'employee', 'transactionItems.item'])
            ->orderBy('transaction_date', 'desc')
            ->get();
        return response()->json($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'payment_method' => 'required|in:cash,card,digital_wallet',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            
            // Check stock availability for all items first
            foreach ($validated['items'] as $item) {
                $branchItem = BranchItem::where('branch_id', $validated['branch_id'])
                    ->where('item_id', $item['item_id'])
                    ->first();
                
                if (!$branchItem) {
                    throw new \Exception("Item with ID {$item['item_id']} is not available at this branch");
                }
                
                if (!$branchItem->is_available || $branchItem->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for item with ID {$item['item_id']}. Available: {$branchItem->stock_quantity}, Required: {$item['quantity']}");
                }
                
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $serviceCharge = round($subtotal * 0.05, 2);
            $total = round($subtotal + $serviceCharge, 2);

            // Create transaction
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateTransactionNumber(),
                'branch_id' => $validated['branch_id'],
                'employee_id' => $validated['employee_id'],
                'subtotal' => $subtotal,
                'service_charge' => $serviceCharge,
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'transaction_date' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create transaction items and update stock
            foreach ($validated['items'] as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                
                // Create transaction item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                    'notes' => $item['notes'] ?? null,
                ]);
                
                // Reduce stock in branch inventory
                $branchItem = BranchItem::where('branch_id', $validated['branch_id'])
                    ->where('item_id', $item['item_id'])
                    ->first();
                
                $branchItem->decreaseStock($item['quantity']);
            }

            $transaction->load(['branch', 'employee', 'transactionItems.item']);
            return response()->json($transaction, 201);
        });
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['branch', 'employee', 'transactionItems.item']);
        return response()->json($transaction);
    }

    public function filter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Transaction::with(['branch', 'employee', 'transactionItems.item']);

        if (!empty($validated['branch_id'])) {
            $query->where('branch_id', $validated['branch_id']);
        }

        if (!empty($validated['start_date']) && !empty($validated['end_date'])) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay()
            ]);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        return response()->json($transactions);
    }

    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Transaction::with(['branch', 'employee', 'transactionItems.item']);

        if (!empty($validated['branch_id'])) {
            $query->where('branch_id', $validated['branch_id']);
        }

        if (!empty($validated['start_date']) && !empty($validated['end_date'])) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay()
            ]);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        $pdf = Pdf::loadView('transactions.pdf', compact('transactions'));
        
        $filename = 'sales-history-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}