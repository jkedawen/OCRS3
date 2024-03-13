<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grades extends Model
{
     protected $table = 'grades';
      use HasFactory;
   public function enrolledStudents()
    {
        return $this->belongsTo(EnrolledStudents::class, 'enrolled_student_id');

     }

   public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');

     }


     protected $fillable = [
            'enrolled_student_id',
            'assessment_id',
            'points',
            'fg_grade',
            'midterms_grade',
            'finals_grade',
            'published',
            'published_midterms',
            'published_finals',
            'status',
            'midterm_status',
            'finals_status',

    ];
    

   
  // Define a scope to filter grades by assessment type
    public function scopeByType($query, $type)
    {
        return $query->whereHas('assessments', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }

    // Calculate total points for a specific grading period and assessment type
    public function getTotalPoints($gradingPeriod, $assessmentType)
    {
        return $this->grades
            ->whereHas('assessments', function ($q) use ($gradingPeriod, $assessmentType) {
                $q->where('grading_period', $gradingPeriod)->where('type', $assessmentType);
            })
            ->sum('points');
    }
    
}
