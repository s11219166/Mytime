<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function getAverageTargetGpa()
    {
        return $this->assessments()->avg('target_gpa');
    }

    public function getAverageAchievedGpa()
    {
        return $this->assessments()->avg('achieved_gpa');
    }
}
