<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
   use HasFactory;

       public function importedClasses()
       {
          return $this->hasMany(ImportedClasslist::class, 'subjects_id');
         }

       public function enrolledStudents()
       {
             return $this->belongsToMany(User::class, 'enrolled_students', 'imported_classlist_id', 'student_id');
           }

   // public function students( ) {
   
  //   return $this->hasMany(Student::class);
    
  //  }

  //  public function getStudentForeignKeyName() {
    
    //    return $this->getConnection()->getForeignKeyName('subject', 'id_number');

    //}

    protected $fillable = [
    'subject_code',
    'description',
    'section',
    'term',
    'subject_type',
     ];
}
