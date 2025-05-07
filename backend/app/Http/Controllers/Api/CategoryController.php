<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json($categories);
        } catch (Exception $e) {
            Log::error('Failed to fetch categories: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch categories'], 500);
        }
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description ?? null,
            ]);

            return response()->json($category, 201);
        } catch (Exception $e) {
            Log::error('Failed to create category: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create category: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            return response()->json($category);
        } catch (Exception $e) {
            Log::error('Failed to fetch category: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch category'], 500);
        }
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $category->update([
                'name' => $request->name,
                'description' => $request->description ?? $category->description,
            ]);

            return response()->json($category);
        } catch (Exception $e) {
            Log::error('Failed to update category: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update category'], 500);
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Log::info('Attempting to delete category with ID: ' . $id);
            
            // First check if the category exists
            $category = Category::find($id);
            
            if (!$category) {
                Log::warning('Category not found for deletion: ID ' . $id);
                return response()->json(['message' => 'Category not found'], 404);
            }
            
            // Store the name for logging
            $name = $category->name;
            $categoryId = $category->id;
            
            // Explicitly check for inventory items relationship
            try {
                if (method_exists($category, 'inventoryItems')) {
                    $inventoryItems = $category->inventoryItems;
                    if ($inventoryItems && $inventoryItems->count() > 0) {
                        Log::warning("Cannot delete category $name (ID: $id) - it has {$inventoryItems->count()} inventory items");
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Cannot delete this category because it is being used by inventory items.',
                            'items_count' => $inventoryItems->count()
                        ], 409);
                    }
                }
            } catch (Exception $e) {
                Log::info('Error checking inventory items: ' . $e->getMessage());
                // Continue with deletion attempt even if relationship check fails
            }
            
            // Force delete to ensure it's removed
            $deleted = $category->delete();
            
            if (!$deleted) {
                Log::error('Failed to delete category: ' . $name . ' (ID: ' . $id . ')');
                DB::rollBack();
                return response()->json(['message' => 'Failed to delete category'], 500);
            }
            
            DB::commit();
            Log::info('Category deleted successfully: ' . $name . ' (ID: ' . $categoryId . ')');
            
            return response()->json([
                'message' => 'Category deleted successfully',
                'id' => $id,
                'name' => $name
            ]);
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error while deleting category: ' . $e->getMessage());
            
            // Check for foreign key constraint violation
            if ($e->errorInfo[1] == 1451) { // MySQL error code for foreign key constraint violation
                return response()->json([
                    'message' => 'Cannot delete this category because it is referenced by other items in the system.',
                    'error' => $e->getMessage()
                ], 409);
            }
            
            return response()->json([
                'message' => 'Database error occurred while deleting the category',
                'error' => $e->getMessage()
            ], 500);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Exception while deleting category: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}