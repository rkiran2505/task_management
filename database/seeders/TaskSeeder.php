<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the user to associate the tasks with
        $user = User::where('role', 'user')->first();

        // Create tasks for the user
        Task::create([
            'title' => 'Task 1',
            'description' => 'This is the first task.',
            'due_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);

        Task::create([
            'title' => 'Task 2',
            'description' => 'This is the second task.',
            'due_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);
    }
}
