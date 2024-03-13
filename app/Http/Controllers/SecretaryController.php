<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\EnrolledStudents;
use App\Models\Instructor;
use App\Models\ImportedClasslist;
use App\Models\Grades;
use App\Models\Semester;
use App\Models\Assessment;

class SecretaryController extends Controller
{
    public function showInstructors()
{
    $instructors = User::where('role', 2)->get();

    return view('secretary.teacher_list.instructor_list', compact('instructors'));
}

public function showInstructorSubjects($instructorId)
{
    $instructor = User::findOrFail($instructorId);
    $subjects = $instructor->taughtSubjects;
    // dd($subjects);

    return view('secretary.teacher_list.subjects', compact('instructor', 'subjects'));
}

public function showEnrolledStudents($subjectId)
{
  $subject = ImportedClasslist::findOrFail($subjectId)->subject;
    $enrolledStudents = EnrolledStudents::where('imported_classlist_id', $subjectId)
        ->with('student')
        ->get();

    return view('secretary.teacher_list.enrolled_students', compact('subject', 'enrolledStudents'));
}

public function viewStudentPoints($studentId, $subjectId)
{
     $student = User::findOrFail($studentId);
    $subject = Subject::findOrFail($subjectId);

    
    $importedClass = $subject->importedClasses->first();

   
    $enrolledStudent = EnrolledStudents::where('student_id', $studentId)
        ->where('imported_classlist_id', $importedClass->id)
        ->first();


    $grades = Grades::where('enrolled_student_id', $enrolledStudent->id)
        ->whereHas('assessment', function ($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })
        ->with('assessment')
        ->get();

       // Fetch the student's grades for each assessment
    $enrolledStudent->load('studentgrades.assessment');

    $studentGrades = $enrolledStudent->studentgrades;

    return view('secretary.teacher_list.view_scores', compact('student', 'grades', 'subject', 'studentGrades'));
}
}

