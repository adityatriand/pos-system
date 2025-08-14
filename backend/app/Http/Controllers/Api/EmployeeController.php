<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        $employees = Employee::with('branch')->get();
        return response()->json($employees);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'branch_id' => 'required|exists:branches,id',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $employee = Employee::create($validated);
        $employee->load('branch');
        return response()->json($employee, 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        $employee->load(['branch', 'transactions']);
        return response()->json($employee);
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'string|max:100',
            'branch_id' => 'exists:branches,id',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'date',
            'is_active' => 'boolean',
        ]);

        $employee->update($validated);
        $employee->load('branch');
        return response()->json($employee);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}