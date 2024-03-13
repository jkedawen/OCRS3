<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedClasslist extends Model
{
    use HasFactory;
    protected $table = 'imported_classlist';
    
    public function instructor()
    {
    return $this->belongsTo(User::class, 'instructor_id', 'id')->where('role', 2);
    }

    public function subject()
     {
      return $this->belongsTo(Subject::class, 'subjects_id');
       }

    public function enrolledStudents()
     {
    return $this->hasMany(EnrolledStudents::class, 'imported_classlist_id');
      }
    
   
    protected $fillable = [
    'subjects_id',
    'instructor_id',
    'days',
    'time',
    'room',
     ];
}
