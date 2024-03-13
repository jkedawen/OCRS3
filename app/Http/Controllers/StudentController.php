<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Users;

class StudentController extends Controller
{
   
 public function studentsBySubject(Subject $subject)
   {
      $students = $subject->students;
      //$student = Student::find($Id);
       return view('teacher.list.studentlist', compact('students', 'subject'));
    }
 public function getname()
{

    $students = Student::with('user')->get();

    return view('students', compact('students'));
}

public function showNotifications()
{
    $notifications = auth()->user()->unreadNotifications;
 //  dd($notifications);
    return view('student.subjectlist', compact('notifications'));
}
public function markNotificationsAsRead()
{
    auth()->user()->unreadNotifications->markAsRead();

    
    Session::flash('notification', 'You have successfully marked the notifications as read.');

    return redirect()->back();
}
}
