<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
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
        // Get the users (assuming we have users already created)
        $user1 = User::where('email', 'admin@example.com')->first();
        $user2 = User::where('email', 'user@example.com')->first();

        // Create tasks for Admin User
        Task::create([
            'title' => 'Admin Task 1',
            'description' => 'This is the first task for the admin.',
            'due_date' => Carbon::now()->addDays(3),
            'user_id' => $user1->id, // Admin user
        ]);

        Task::create([
            'title' => 'Admin Task 2',
            'description' => 'This is the second task for the admin.',
            'due_date' => Carbon::now()->addDays(5),
            'user_id' => $user1->id, // Admin user
        ]);

        // Create tasks for Regular User
        Task::create([
            'title' => 'User Task 1',
            'description' => 'This is the first task for a regular user.',
            'due_date' => Carbon::now()->addDays(2),
            'user_id' => $user2->id, // Regular user
        ]);

        Task::create([
            'title' => 'User Task 2',
            'description' => 'This is the second task for a regular user.',
            'due_date' => Carbon::now()->addDays(4),
            'user_id' => $user2->id, // Regular user
        ]);
    }
}
