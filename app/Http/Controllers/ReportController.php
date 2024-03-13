<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnrolledStudents;
use App\Models\Assessment;
use App\Models\Subject;
use App\Exports\StudentReportExport;
use App\Exports\StudentGradeExport;
use App\Exports\StudentsSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportController extends Controller
{
   public function index($subjectId)
{
   
    $students = EnrolledStudents::with(['student', 'grades'])
        ->whereHas('importedClasses', function ($query) use ($subjectId) {
            $query->where('subjects_id', $subjectId);
        })
        ->get();

    $passPercentage = $students->filter(function ($student) {
        return $student->grades->avg('finals_grade') >= 75;
    })->count() / $students->count() * 100;

    $failPercentage = 100 - $passPercentage;


    $sortedStudents = collect($students)->groupBy('student.gender');

    return view('teacher.list.report', compact('students', 'passPercentage', 'failPercentage', 'subjectId', 'sortedStudents'));
}

 public function generatePdf($subjectId)
{
   $students = EnrolledStudents::with(['student', 'grades'])
        ->whereHas('importedClasses', function ($query) use ($subjectId) {
            $query->where('subjects_id', $subjectId);
        })
        ->get();

    $passingStudents = $students->filter(function ($student) {
        return $student->grades->avg('finals_grade') >= 75;
    });

    $failingStudents = $students->reject(function ($student) use ($passingStudents) {
        return $passingStudents->contains('id', $student->id);
    });

    $subject = Subject::findOrFail($subjectId);


    $assessments = Assessment::where('subject_id', $subjectId)
        ->where('grading_period', 'First Grading') 
        ->orderBy('type', 'desc') 
        ->get();


    $midtermAssessments = Assessment::where('subject_id', $subjectId)
        ->where('grading_period', 'Midterm') 
        ->orderBy('type', 'desc') 
        ->get();

    $finalsAssessments = Assessment::where('subject_id', $subjectId)
        ->where('grading_period', 'Finals') 
        ->orderBy('type', 'desc') 
        ->get();

    $hasFGAssessments = $assessments->isNotEmpty();
   
    $hasMidtermAssessments = $midtermAssessments->isNotEmpty();

  
    $hasFinalsAssessments = $finalsAssessments->isNotEmpty();


    $sortedStudents = collect($students)->groupBy('student.gender');

    $pdf = PDF::loadView('teacher.list.pdf', compact('passingStudents', 'failingStudents', 'subject', 'assessments', 'students', 'sortedStudents','midtermAssessments', 'finalsAssessments', 'hasFGAssessments', 'hasMidtermAssessments', 'hasFinalsAssessments'))->setPaper('legal', 'landscape');

    return $pdf->download('student_report.pdf');
}

 public function generateGradesList($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        $students = EnrolledStudents::with(['student', 'grades'])
            ->whereHas('importedClasses', function ($query) use ($subjectId) {
                $query->where('subjects_id', $subjectId);
            })
            ->get();

        // Fetch assessments for each grading period
        $fgAssessments = $this->fetchAssessmentsByGradingPeriod($subjectId, 'fg_grade');
        $midtermAssessments = $this->fetchAssessmentsByGradingPeriod($subjectId, 'midterms_grade');
        $finalsAssessments = $this->fetchAssessmentsByGradingPeriod($subjectId, 'finals_grade');

        $sortedStudents = collect($students)->groupBy('student.gender');

        $pdf = PDF::loadView('teacher.list.gradeslist', compact(
            'subject',
            'students',
            'sortedStudents',
            'fgAssessments',
            'midtermAssessments',
            'finalsAssessments'
        ))->setPaper('A4', 'portrait');

        return $pdf->download('gradeslist.pdf');
    }

    private function fetchAssessmentsByGradingPeriod($subjectId, $gradingPeriod)
    {
        return Assessment::where('subject_id', $subjectId)
            ->where('grading_period', $gradingPeriod)
            ->orderBy('type', 'desc')
            ->get();
    }

    public function generateExcelReport($subjectId)
    {
        $exportFileName = 'student_report.xlsx';

        return Excel::download(new StudentReportExport($subjectId), $exportFileName);
    }

    public function exportGradesList($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        $students = EnrolledStudents::with(['student', 'grades'])
            ->whereHas('importedClasses', function ($query) use ($subjectId) {
                $query->where('subjects_id', $subjectId);
            })
            ->get();

        $fgAssessments = $this->fetchAssessmentsByGradingPeriod($subjectId, 'fg_grade');
        $midtermAssessments = $this->fetchAssessmentsByGradingPeriod($subjectId, 'midterms_grade');
        $finalsAssessments = $this->fetchAssessmentsByGradingPeriod($subjectId, 'finals_grade');

        $sortedStudents = collect($students)->groupBy('student.gender');

        return Excel::download(new StudentGradeExport($subject, $students, $sortedStudents), 'gradeslist.xlsx');
    }

   public function generateSummaryReport($subjectId)
{

    return Excel::download(new StudentsSummaryExport($subjectId), 'summary_report.xlsx');
}


}
