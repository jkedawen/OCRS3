<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_number',
        'name',
        'middle_name', 
        'last_name',
        'course',
        'gender',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
     /**function for getting list of users(admin side) from db*/
    static public function getAdminList()
    {  
            /**('users.*') as in from users table in db*/
       return self::select('users.*')
        ->whereIn('role', [1, 2, 4]) // Include roles 1, 2, and 4
        ->orderBy('id', 'desc')
        ->get();
    }
    /**function for getting a single id from users(admin) in db to update*/
    static public function getSingleList($id)
     {
         return self::find($id);
     }
    static public function getStudentList()
    {
      return self::select('users.*')
                   ->where('role', '=', 3)
                   ->orderBy('id','desc')
                  ->get();
    }
    static public function student()
    {
    return $this->hasOne(Student::class, 'id_number', 'id');
    }

    public function enrolledSubjects()
{
    return $this->belongsToMany(Subject::class, 'enrolled_students', 'student_id', 'imported_classlist_id');
}

    public function taughtSubjects()
     {
    return $this->hasMany(ImportedClasslist::class, 'instructor_id');
}

    public function enrolledStudentSubjects() {
    return $this->hasMany(EnrolledStudents::class, 'student_id');
    }

    
}

