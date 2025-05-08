<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Parse query parameters for pagination
            $perPage = $request->input('per_page', 8);
            $page = $request->input('page', 1);
            $search = $request->input('search');
            $category = $request->input('category');
            $status = $request->input('status');
            $sort = $request->input('sort', 'name,asc');

            // Parse sort parameter
            $sortParts = explode(',', $sort);
            $sortColumn = $sortParts[0] ?? 'name';
            $sortDirection = $sortParts[1] ?? 'asc';

            // Build query
            $query = Inventory::query();

            // Apply search
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                          ->orWhere('name', 'like', '%' . $search . '%')
                          ->orWhere('category', 'like', '%' . $search . '%');
                });
            }

            // Apply category filter
            if (!empty($category) && $category !== 'all') {
                $query->where('category', $category);
            }

            // Apply status filter
            if (!empty($status) && $status !== 'all') {
                $query->where('status', $status);
            }

            // Apply sorting
            if (in_array($sortColumn, ['id', 'name', 'category', 'status', 'rate', 'acquisition_date'])) {
                $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('name', 'asc');
            }

            // Get paginated results
            $inventoryItems = $query->paginate($perPage, ['*'], 'page', $page);

            // Return results with pagination metadata
            return response()->json([
                'data' => $inventoryItems->items(),
                'meta' => [
                    'current_page' => $inventoryItems->currentPage(),
                    'per_page' => $inventoryItems->perPage(),
                    'total' => $inventoryItems->total(),
                    'last_page' => $inventoryItems->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching inventory items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'status' => 'required|string|in:Available,Rented,Maintenance',
                'rate' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'acquisition_date' => 'nullable|date',
            ]);

            // Generate a unique ID
            $id = 'INV-' . strtoupper(Str::random(6));

            // Create new item
            $item = Inventory::create([
                'id' => $id,
                'name' => $request->name,
                'category' => $request->category,
                'status' => $request->status,
                'rate' => $request->rate,
                'description' => $request->description,
                'features' => $request->features,
                'acquisition_date' => $request->acquisition_date,
                'rental_history' => [],
                'maintenance_records' => []
            ]);

            return response()->json([
                'message' => 'Inventory item created successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified inventory item.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $item = Inventory::findOrFail($id);
            
            return response()->json([
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving inventory item: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $item = Inventory::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'category' => 'sometimes|string|max:255',
                'status' => 'sometimes|string|in:Available,Rented,Maintenance',
                'rate' => 'sometimes|numeric|min:0',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'acquisition_date' => 'nullable|date',
                'rental_history' => 'nullable|array',
                'maintenance_records' => 'nullable|array'
            ]);

            // Update item
            $item->update($request->all());

            return response()->json([
                'message' => 'Inventory item updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating inventory item: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Remove the specified inventory item.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $item = Inventory::findOrFail($id);
            $item->delete();

            return response()->json([
                'message' => 'Inventory item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting inventory item: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Update the status of an inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:Available,Rented,Maintenance',
            ]);

            $item = Inventory::findOrFail($id);
            $item->status = $request->status;
            $item->save();

            return response()->json([
                'message' => 'Inventory status updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating inventory status: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Display a listing of inventory items assigned to employees.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function employeeInventory(Request $request)
    {
        try {
            // Parse query parameters for pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $search = $request->input('search');
            $employeeId = $request->input('employee_id');
            $sort = $request->input('sort', 'name,asc');

            // Parse sort parameter
            $sortParts = explode(',', $sort);
            $sortColumn = $sortParts[0] ?? 'name';
            $sortDirection = $sortParts[1] ?? 'asc';

            // Build query
            $query = Inventory::query();
            
            // Filter by assigned to employee status
            $query->where('status', 'Assigned');
            
            // Filter by specific employee if provided
            if (!empty($employeeId)) {
                $query->where('assigned_to', $employeeId);
            }

            // Apply search
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                          ->orWhere('name', 'like', '%' . $search . '%')
                          ->orWhere('category', 'like', '%' . $search . '%')
                          ->orWhere('assigned_to', 'like', '%' . $search . '%');
                });
            }

            // Apply sorting
            if (in_array($sortColumn, ['id', 'name', 'category', 'status', 'assigned_to', 'assigned_date'])) {
                $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('name', 'asc');
            }

            // Get paginated results
            $inventoryItems = $query->paginate($perPage, ['*'], 'page', $page);

            // Return results with pagination metadata
            return response()->json([
                'data' => $inventoryItems->items(),
                'meta' => [
                    'current_page' => $inventoryItems->currentPage(),
                    'per_page' => $inventoryItems->perPage(),
                    'total' => $inventoryItems->total(),
                    'last_page' => $inventoryItems->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching employee inventory items: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Assign an inventory item to an employee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function assignToEmployee(Request $request, $id)
    {
        try {
            $request->validate([
                'employee_id' => 'required|string',
                'employee_name' => 'required|string',
                'assigned_date' => 'required|date',
                'expected_return_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $item = Inventory::findOrFail($id);
            
            // Update item with assignment details
            $item->status = 'Assigned';
            $item->assigned_to = $request->employee_id;
            $item->assigned_to_name = $request->employee_name;
            $item->assigned_date = $request->assigned_date;
            $item->expected_return_date = $request->expected_return_date;
            $item->assignment_notes = $request->notes;
            $item->save();

            return response()->json([
                'message' => 'Inventory item assigned to employee successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error assigning inventory item: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }
    
    /**
     * Return an inventory item from an employee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function returnFromEmployee(Request $request, $id)
    {
        try {
            $request->validate([
                'return_date' => 'required|date',
                'condition' => 'required|string|in:Excellent,Good,Fair,Poor,Damaged',
                'notes' => 'nullable|string',
            ]);

            $item = Inventory::findOrFail($id);
            
            // Store the assignment history before clearing assignment
            $history = $item->assignment_history ?? [];
            $history[] = [
                'employee_id' => $item->assigned_to,
                'employee_name' => $item->assigned_to_name,
                'assigned_date' => $item->assigned_date,
                'return_date' => $request->return_date,
                'condition' => $request->condition,
                'notes' => $request->notes,
            ];
            
            // Update item to available status
            $item->status = $request->condition === 'Damaged' ? 'Maintenance' : 'Available';
            $item->assignment_history = $history;
            $item->assigned_to = null;
            $item->assigned_to_name = null;
            $item->assigned_date = null;
            $item->expected_return_date = null;
            $item->assignment_notes = null;
            $item->save();

            return response()->json([
                'message' => 'Inventory item returned successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error returning inventory item: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }
}