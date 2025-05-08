<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTaskController extends Controller
{
    /**
     * Get all tasks for the employee
     */
    public function index(Request $request)
    {
        // In a real application, you'd fetch from database
        // For this example, we'll return mock data that matches the frontend format
        $tasks = [
            [
                'id' => 1,
                'title' => 'Process rental request #4582',
                'description' => 'Review and approve rental request from John Smith for equipment rental on May 10th.',
                'status' => 'pending',
                'priority' => 'high',
                'dueDate' => 'May 6, 2025',
                'customer' => 'John Smith'
            ],
            [
                'id' => 2,
                'title' => 'Update inventory items',
                'description' => 'Check physical inventory against system records and update any discrepancies.',
                'status' => 'completed',
                'priority' => 'medium',
                'dueDate' => 'May 4, 2025'
            ],
            [
                'id' => 3,
                'title' => 'Contact customer #12458',
                'description' => 'Follow up with Sarah Johnson about her rental experience and collect feedback.',
                'status' => 'pending',
                'priority' => 'medium',
                'dueDate' => 'May 6, 2025',
                'customer' => 'Sarah Johnson'
            ],
            [
                'id' => 4,
                'title' => 'Prepare monthly equipment report',
                'description' => 'Compile data on equipment usage, maintenance, and availability for the monthly report.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'dueDate' => 'May 7, 2025'
            ],
            [
                'id' => 5,
                'title' => 'Schedule maintenance for item #873',
                'description' => 'Contact maintenance team to schedule routine service for the portable generator.',
                'status' => 'pending',
                'priority' => 'low',
                'dueDate' => 'May 10, 2025'
            ]
        ];
        
        // Apply filters if provided
        if ($request->has('status') && $request->status !== 'all') {
            $tasks = array_filter($tasks, function($task) use ($request) {
                return $task['status'] === $request->status;
            });
        }
        
        if ($request->has('priority') && $request->priority !== 'all') {
            $tasks = array_filter($tasks, function($task) use ($request) {
                return $task['priority'] === $request->priority;
            });
        }
        
        if ($request->has('search')) {
            $search = strtolower($request->search);
            $tasks = array_filter($tasks, function($task) use ($search) {
                return strpos(strtolower($task['title']), $search) !== false || 
                       strpos(strtolower($task['description']), $search) !== false ||
                       (isset($task['customer']) && strpos(strtolower($task['customer']), $search) !== false);
            });
        }
        
        // Re-index array after filtering
        $tasks = array_values($tasks);
        
        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }
    
    /**
     * Get a specific task
     */
    public function show($id)
    {
        // In a real application, you'd fetch from database
        // This is just a placeholder implementation
        $tasks = $this->getMockTasks();
        
        $task = collect($tasks)->firstWhere('id', (int)$id);
        
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $task
        ]);
    }
    
    /**
     * Update task status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,completed',
        ]);
        
        // In a real application, you'd update the database
        // This is just acknowledging the request
        
        return response()->json([
            'status' => 'success',
            'message' => 'Task status updated successfully',
            'data' => [
                'id' => $id,
                'status' => $request->status
            ]
        ]);
    }
    
    /**
     * Mark a task as complete
     */
    public function complete($id)
    {
        // In a real application, you'd update the database
        // This is just acknowledging the request
        
        return response()->json([
            'status' => 'success',
            'message' => 'Task marked as completed',
            'data' => [
                'id' => $id,
                'status' => 'completed'
            ]
        ]);
    }
    
    /**
     * Helper method to get mock tasks (for show method)
     */
    private function getMockTasks()
    {
        return [
            [
                'id' => 1,
                'title' => 'Process rental request #4582',
                'description' => 'Review and approve rental request from John Smith for equipment rental on May 10th.',
                'status' => 'pending',
                'priority' => 'high',
                'dueDate' => 'May 6, 2025',
                'customer' => 'John Smith'
            ],
            [
                'id' => 2,
                'title' => 'Update inventory items',
                'description' => 'Check physical inventory against system records and update any discrepancies.',
                'status' => 'completed',
                'priority' => 'medium',
                'dueDate' => 'May 4, 2025'
            ],
            [
                'id' => 3,
                'title' => 'Contact customer #12458',
                'description' => 'Follow up with Sarah Johnson about her rental experience and collect feedback.',
                'status' => 'pending',
                'priority' => 'medium',
                'dueDate' => 'May 6, 2025',
                'customer' => 'Sarah Johnson'
            ],
            [
                'id' => 4,
                'title' => 'Prepare monthly equipment report',
                'description' => 'Compile data on equipment usage, maintenance, and availability for the monthly report.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'dueDate' => 'May 7, 2025'
            ],
            [
                'id' => 5,
                'title' => 'Schedule maintenance for item #873',
                'description' => 'Contact maintenance team to schedule routine service for the portable generator.',
                'status' => 'pending',
                'priority' => 'low',
                'dueDate' => 'May 10, 2025'
            ]
        ];
    }
}