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
}