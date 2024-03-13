<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory;
     use Notifiable;
    //protected $table = 'student';

   // public function subject()
    //{
    //return $this->belongsTo(Subject::class);
    //}
     
    //  public function user()
    //{
      //  return $this->belongsTo(User::class, 'id_number', 'id');
   // }

     public function enrolledstudents()
    {
        return $this->hasMany(EnrolledStudents::class);
    }

    public function enrolledSubjects()
{
    return $this->hasManyThrough(Subject::class, ImportedClasslist::class, 'student_id', 'id', 'id', 'subjects_id');
}

  public function importedclasses()
  {
      return $this->hasMany(ImportedClasslist::class, 'student_id');
  }
}
