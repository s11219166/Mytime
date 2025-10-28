<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'course_id',
        'actual_percentage',
        'target_percentage',
        'achieved_percentage'
    ];

    protected $appends = ['target_gpa', 'achieved_gpa'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
