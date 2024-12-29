<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    // If you're using mass-assignment, you should define the fillable or guarded property
    use HasFactory;

    // Make sure the fields you're trying to fill are listed in 'fillable'
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'status',
    ];

    // Define relationships, if necessary
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
