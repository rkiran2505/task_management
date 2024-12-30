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
            $tasks = Task::with(['user:id,name'])->orderBy('id', 'desc');
            
            if (Auth::user()->role !== 'admin') {
                $tasks->where('user_id', Auth::id());
            }
            if ($request->has('user_id')) {
                if (Auth::user()->role !== 'admin' && Auth::id() !== $request->user_id) {
                    return response()->json(['success' => false, 'message' => 'You can only view your own tasks.'], 403);
                }
                $tasks->where('user_id', $request->user_id);
            }
    
            if ($request->has('status')) {
                $tasks->where('status', $request->status);
            }
    
            return response()->json(['success' => true, 'message' => 'Tasks fetched successfully', 'data' => $tasks->get()], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching tasks: ' . $e->getMessage()], 400);
        }
    }

    // Store a new task
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date|after:today',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], 400);
        }

        try {
            $userId = Auth::user()->role === 'admin' ? $request->user_id : Auth::id();
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
                'message' => 'Error creating task: ' . $e->getMessage(),
            ], 400);
        }
    }

    // Show a specific task
    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        return response()->json(['success' => true, 'data' => $task], 200);
    }

    // Update a task
    public function update(Request $request, $task_id)
    {
        $task = Task::find($task_id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }

        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date|after:today',
            'status' => 'nullable|string|in:pending,completed,overdue',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 400);
        }

        $task->update($request->only(['title', 'description', 'due_date', 'status']));
        return response()->json(['success' => true, 'message' => 'Task updated successfully', 'data' => $task], 200);
    }

    public function destroy($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);  
            if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }
            $task->delete();
            return response()->json(['success' => true, 'message' => 'Task deleted successfully'], 200);
            
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting task: ' . $e->getMessage()], 400);
        }
    }
}