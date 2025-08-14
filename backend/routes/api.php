<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\BranchItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // Branches
    Route::apiResource('branches', BranchController::class);
    Route::get('branches/{branch}/employees', [BranchController::class, 'employees']);
    Route::get('branches/{branch}/items', [BranchItemController::class, 'getBranchItems']);
    Route::post('branches/{branch}/items', [BranchItemController::class, 'addItemsToBranch']);
    
    // Branch Items
    Route::apiResource('branch-items', BranchItemController::class);
    
    // Employees
    Route::apiResource('employees', EmployeeController::class);
    
    // Items
    Route::apiResource('items', ItemController::class);
    Route::get('items/category/{category}', [ItemController::class, 'byCategory']);
    
    // Transactions
    Route::apiResource('transactions', TransactionController::class);
    Route::get('transactions/history/export-pdf', [TransactionController::class, 'exportPdf']);
    Route::get('transactions/history/filter', [TransactionController::class, 'filter']);
    
    // Dashboard
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('dashboard/top-items', [DashboardController::class, 'topItems']);
    Route::get('dashboard/branch-revenue', [DashboardController::class, 'branchRevenue']);
    Route::get('dashboard/inventory-status', [DashboardController::class, 'inventoryStatus']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');