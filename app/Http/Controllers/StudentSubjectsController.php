<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EnrolledStudents;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Semester;
use Auth;



class StudentSubjectsController extends Controller
{
    public function studentsubjects()
    { 
    $student = Auth::user();

    if ($student && $student->role === 3) {
        $currentSemester = Semester::where('is_current', true)->first();
        
        $enrolledStudentSubjects = $student->enrolledStudentSubjects
            ->load('importedclasses.instructor')
            ->filter(function ($enrolledSubject) use ($currentSemester) {
                return $enrolledSubject->importedclasses->subject->term === $currentSemester->semester_name . ', ' . $currentSemester->school_year;
            });

        return view('student.subjectlist', compact('enrolledStudentSubjects'));
    } else {
        return redirect()->route('login')->with('error', 'Access denied.');
    }
}
 public function studentpastsubjects()
    { 
    $student = Auth::user();

    if ($student && $student->role === 3) {

         $currentSemester = Semester::where('is_current', true)->first();
         $pastStudentSubjects = $student->enrolledStudentSubjects
            ->load('importedclasses.instructor')
            ->reject(function ($enrolledSubject) use ($currentSemester) {
                return $enrolledSubject->importedclasses->subject->term === $currentSemester->semester_name . ', ' . $currentSemester->school_year;
            });

        return view('student.past_subjectlist', compact('pastStudentSubjects'));
    } else {
        return redirect()->route('login')->with('error', 'Access denied.');
    }
}

}