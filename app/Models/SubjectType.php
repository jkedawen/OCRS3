<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectType extends Model
{
    use HasFactory;
    protected $table = 'subject_type_percentage';

    protected $fillable = ['subject_type', 'lec_percentage', 'lab_percentage'];

}
