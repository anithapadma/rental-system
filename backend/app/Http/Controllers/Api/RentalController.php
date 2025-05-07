<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RentalController extends Controller
{
    /**
     * Display a listing of rentals with pagination, search, and sorting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Basic error logging
            \Log::info('Rentals API called', ['params' => $request->all()]);
            
            // Parse query parameters for pagination
            $perPage = $request->input('per_page', 5);
            $page = $request->input('page', 1);
            $search = $request->input('search');
            $status = $request->input('status');
            $sort = $request->input('sort', 'start_date,desc');

            // Parse sort parameter
            $sortParts = explode(',', $sort);
            $sortColumn = $sortParts[0] ?? 'start_date';
            $sortDirection = $sortParts[1] ?? 'desc';

            // Build query
            $query = Rental::query();

            // Apply search
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                          ->orWhere('customer', 'like', '%' . $search . '%')
                          ->orWhere('items', 'like', '%' . $search . '%');
                });
            }

            // Apply status filter
            if (!empty($status)) {
                $query->where('status', $status);
            }

            // Apply sorting (with validation to prevent SQL injection)
            if (in_array($sortColumn, ['id', 'customer', 'items', 'start_date', 'due_date', 'status', 'daily_rate'])) {
                $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('start_date', 'desc');
            }

            // Get paginated results
            $rentals = $query->paginate($perPage, ['*'], 'page', $page);

            // Return results with pagination metadata
            return response()->json([
                'data' => $rentals->items(),
                'meta' => [
                    'current_page' => $rentals->currentPage(),
                    'per_page' => $rentals->perPage(),
                    'total' => $rentals->total(),
                    'last_page' => $rentals->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in rental index method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error fetching rentals: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created rental in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer' => 'required|string|max:255',
                'items' => 'required|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|string|in:Active,Overdue,Returned,Maintenance',
                'daily_rate' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            // Generate a UUID for the rental ID
            $id = 'RT-' . strtoupper(Str::random(8));

            // Create new rental
            $rental = Rental::create([
                'id' => $id,
                'customer' => $request->customer,
                'items' => $request->items,
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'daily_rate' => $request->daily_rate ?? 40.00, // Default rate if not provided
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => 'Rental created successfully',
                'data' => $rental
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating rental', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'message' => 'Error creating rental: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified rental.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $rental = Rental::findOrFail($id);
            
            return response()->json([
                'data' => $rental
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving rental: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified rental in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $rental = Rental::findOrFail($id);

            $request->validate([
                'customer' => 'sometimes|string|max:255',
                'items' => 'sometimes|string',
                'start_date' => 'sometimes|date',
                'due_date' => 'sometimes|date|after_or_equal:start_date',
                'status' => 'sometimes|string|in:Active,Overdue,Returned,Maintenance',
                'daily_rate' => 'sometimes|numeric',
                'notes' => 'sometimes|string',
            ]);

            // Update rental
            $rental->update($request->all());

            return response()->json([
                'message' => 'Rental updated successfully',
                'data' => $rental
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating rental', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error updating rental: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Remove the specified rental from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $rental = Rental::findOrFail($id);
            $rental->delete();

            return response()->json([
                'message' => 'Rental deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting rental: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Update the status of a rental.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:Active,Overdue,Returned,Maintenance',
            ]);

            $rental = Rental::findOrFail($id);
            $rental->status = $request->status;
            $rental->save();

            return response()->json([
                'message' => 'Rental status updated successfully',
                'data' => $rental
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating rental status: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Mark a rental as returned.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function returnRental($id)
    {
        try {
            $rental = Rental::findOrFail($id);
            $rental->status = 'Returned';
            $rental->save();

            return response()->json([
                'message' => 'Rental marked as returned successfully',
                'data' => $rental
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error marking rental as returned: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }
}