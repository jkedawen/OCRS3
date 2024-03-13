<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrolledStudents extends Model
{
    use HasFactory;
    public function student()
    {
      return $this->belongsTo(User::class, 'student_id');
     }

    public function importedclasses()
    {
         return $this->belongsTo(ImportedClasslist::class, 'imported_classlist_id');
    }
//////////for instructor side - inserting scores

    public function grades()
    {
        return $this->hasMany(Grades::class, 'enrolled_student_id', 'id');
    }

   public function getScore($assessmentId)
{
     return $this->grades()->where('assessment_id', $assessmentId)->value('points');

   // $score = $this->grades()->where('assessment_id', $assessmentId)->value('points');

   // return is_null($score) ? 'A' : $score;
}

   // public function scoreData()
  // {
  //   return $this->hasOne(ScoreData::class, 'enrolled_student_id', 'id');
 // }
/////////////////for student side - viewing scores
   public function studentgrades()
{
    return $this->hasMany(Grades::class, 'enrolled_student_id');
}

   public function subject()
    {
    return $this->belongsTo(Subject::class, 'imported_classlist_id', 'id');
      }

      
    protected $fillable = [
   'student_id',
   'imported_classlist_id',
     ];
}
