<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;



class TaskController extends Controller
{
   
    public function index(Request $request)
    {
        try {
            if (Auth::user()->role === 'admin') {
                $tasks = Task::orderBy('id', 'desc');

                // Apply filters if provided in the request (optional)
                if ($request->has('status')) {
                    $tasks = $tasks->where('status', $request->status);
                }

                $tasks = $tasks->get();
            } else {
                // Regular user can only view their own tasks
                $tasks = Task::where('user_id', Auth::id())->orderBy('id', 'desc')->get();
            }

            // Return a simple response with tasks data
            return response()->json([
                'success' => true,
                'message' => 'Tasks fetched successfully',
                'data' => $tasks
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    // Store a new task in the database (Admin or User)
    public function store(Request $request)
    {
        // Validation for the task fields
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',  // Title should not exceed 255 characters
            'description' => 'required|string',    // Description is required
            'due_date' => 'nullable|date|after:today', // Due date must be a valid date and in the future
        ]);

        if ($validation->fails()) {
            $error = $validation->errors()->first();
            return response()->json([
                'success' => false,
                'message' => $error,
                'data' => []
            ], 400);
        }

        try {
            // Only Admin can assign tasks to other users, Regular users can only create their own tasks
            $userId = Auth::user()->role === 'admin' ? $request->user_id : Auth::id();

            // Create the task and associate it with the appropriate user
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    // Show a specific task (Only Admin can view any task, User can only view their own task)
    public function show(Task $task)
    {
        // Check if the authenticated user is the owner of the task or an admin
        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'data' => []
            ], 403);
        }

        // Return task details
        return response()->json([
            'success' => true,
            'message' => 'Task fetched successfully',
            'data' => $task
        ], 200);
    }

    // Update a task in the database (Only Admin or the user who owns the task can update)
    public function update(Request $request, Task $task)
    {
        try {
            // Ensure the user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please log in.',
                    'data' => []
                ], 401);
            }
    
            // Log the user id and task owner id for debugging
            Log::info('Authenticated User ID: ' . auth()->id());
            Log::info('Task User ID: ' . $task->user_id);
    
            // Check if the authenticated user is the owner of the task or an admin
            if ($task->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                    'data' => []
                ], 403);
            }
    
            // Validation for the task fields
            $validation = Validator::make($request->all(), [
                'title' => 'required|string|max:255',  // Title should not exceed 255 characters
                'description' => 'required|string',    // Description is required
                'due_date' => 'nullable|date|after:today', // Due date must be a valid date and in the future
            ]);
    
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $error,
                    'data' => []
                ], 400);
            }
    
            // Update the task
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
            ]);
    
            // Return the updated task
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ], 200);
        } catch (Exception $e) {
            // Log error for debugging purposes
            Log::error('Error updating task: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }
    
    // Delete a task (Only Admin or the user who owns the task can delete)
    public function destroy(Task $task)
    {
        // Check if the authenticated user is the owner of the task or an admin
        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'data' => []
            ], 403);
        }

        try {
            // Delete the task
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully.',
                'data' => []
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }
}