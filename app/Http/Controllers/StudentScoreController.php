<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnrolledStudents;
use App\Models\Subject;
use App\Models\ImportedClasslist;
use App\Models\Assessment;
use App\Models\AssessmentDescription;
use App\Models\User;
use App\Models\Grades;
use App\Models\SubjectType;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Auth;


class StudentScoreController extends Controller
{
   public function showScores($enrolledStudentId)
{
   $enrolledStudent = EnrolledStudents::find($enrolledStudentId);

    $gradingPeriods = DB::table('assessments')->select('grading_period')->distinct()->pluck('grading_period');
    
    // Define the assessment types to be excluded
    $excludedAssessmentTypes = [
        'Additional Points Quiz',
        'Additional Points OT',
        'Additional Points Exam',
        'Additional Points Lab',
        'Direct Bonus Grade',
    ];

    $assessmentTypes = DB::table('assessments')
        ->select('type')
        ->distinct()
        ->whereNotIn('type', $excludedAssessmentTypes)
        ->pluck('type');

    if ($enrolledStudent && $enrolledStudent->student_id === Auth::user()->id) {
        $enrolledStudent->load('studentgrades.assessment');

        $scores = $enrolledStudent->studentgrades;

        return view('student.scores.showscores', compact('scores', 'gradingPeriods', 'assessmentTypes'));
    } else {
        return redirect()->route('login')->with('error', 'Access denied.');
    }
}
}
