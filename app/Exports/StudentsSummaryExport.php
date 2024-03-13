<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Subject;
use App\Models\EnrolledStudents;
use App\Models\ImportedClasslist;
use App\Models\Assessment;
use App\Models\AssessmentDescription;
use App\Models\User;
use App\Models\Grades;
use App\Models\SubjectType;
use App\Models\Semester;


class StudentsSummaryExport implements FromCollection, WithEvents
{
      use Exportable;

    protected $subjectId;

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function collection()
    {
        $data = [];

         $students = EnrolledStudents::with(['student', 'grades'])
        ->whereHas('importedClasses', function ($query) {
            $query->where('subjects_id', $this->subjectId);
        })
        ->get();

  //  dd($students->toArray());

        
        $data[] = $this->generateCourseSummary($students, 'BSIT');
        $data[] = $this->generateCourseSummary($students, 'BSCS');
        $data[] = $this->generateCourseSummary($students, 'BSCpE');
        $data[] = $this->generateNonSITSummary($students);

        return collect($data)->flatten(1); 
    }

 

    protected function generateCourseSummary($students, $course)
    {
    
     $courseSummary = [];


        $courseStudents = $students->where('student.course', $course);

     
        $courseSummary[] = ["Course: $course"];
        $courseSummary[] = ['Particulars:', 'No. of '];

       
        $countStudents = function ($condition) use ($courseStudents) {
            return $courseStudents->filter($condition)->count();
        };

        $courseSummary[] = ['Students with grades of 80 and above', $countStudents(function ($student) {
            return $student->grades->where('finals_grade', '>=', 80)->isNotEmpty();
        })];
        $courseSummary[] = ['Students with grades of 75 to 79', $countStudents(function ($student) {
            return $student->grades->whereBetween('finals_grade', [75, 79])->isNotEmpty();
        })];
        $courseSummary[] = ['Students with grades below 75 but completed the semester', $countStudents(function ($student) {
            return $student->grades->where('finals_grade', '<', 75)->where(function ($grade) {
                return $grade->finals_status === 'DEFAULT' || $grade->finals_status === '';
            })->isNotEmpty();
        })];
        $courseSummary[] = ['Students with grades below 75 and stopped attending', $countStudents(function ($student) {
            return $student->grades->where('finals_status', 'WITHDRAW')->isNotEmpty();
        })];
        $courseSummary[] = ['Students with INC grades', $countStudents(function ($student) {
            return $student->grades->where('finals_status', 'INC')->isNotEmpty();
        })];
        $courseSummary[] = ['Students with NFE grades', $countStudents(function ($student) {
            return $student->grades->where('finals_status', 'NFE')->isNotEmpty();
        })];
        $courseSummary[] = ['Students with DRP grades (never attended the class)', $countStudents(function ($student) {
            return $student->grades->where('finals_status', 'DRP')->isNotEmpty();
        })];
        $courseSummary[] = ['TOTAL', $courseStudents->count()];

        $courseSummary[] = ['', '']; 

        return $courseSummary;
    }

    protected function generateNonSITSummary($students)
    {
        $nonSITSummary = [];


        $nonSITStudents = $students->whereNotIn('student.course', ['BSIT', 'BSCS', 'BSCpE']);

        $nonSITSummary[] = ['Course: Non-SIT'];
        $nonSITSummary[] = ['Particulars:', 'No. of '];

        $countNonSITStudents = function ($condition) use ($nonSITStudents) {
            return $nonSITStudents->filter($condition)->count();
        };

        $nonSITSummary[] = ['Students with grades of 80 and above', $countNonSITStudents(function ($student) {
            return $student->grades->where('finals_grade', '>=', 80)->isNotEmpty();
        })];

        $nonSITSummary[] = ['Students with grades of 75 to 79', $countNonSITStudents(function ($student) {
            return $student->grades->whereBetween('finals_grade', [75, 79])->isNotEmpty();
        })];

        $nonSITSummary[] = ['Students with grades below 75 but completed the semester', $countNonSITStudents(function ($student) {
            return $student->grades->where('finals_grade', '<', 75)->where(function ($grade) {
                return $grade->finals_status === 'DEFAULT' || $grade->finals_status === '';
            })->isNotEmpty();
        })];

        $nonSITSummary[] = ['Students with grades below 75 and stopped attending', $countNonSITStudents(function ($student) {
            return $student->grades->where('finals_status', 'WITHDRAW')->isNotEmpty();
        })];

        $nonSITSummary[] = ['Students with INC grades', $countNonSITStudents(function ($student) {
            return $student->grades->where('finals_status', 'INC')->isNotEmpty();
        })];

        $nonSITSummary[] = ['Students with NFE grades', $countNonSITStudents(function ($student) {
            return $student->grades->where('finals_status', 'NFE')->isNotEmpty();
        })];

        $nonSITSummary[] = ['Students with DRP grades (never attended the class)', $countNonSITStudents(function ($student) {
            return $student->grades->where('finals_status', 'DRP')->isNotEmpty();
        })];

        $nonSITSummary[] = ['TOTAL', $nonSITStudents->count()];

        $nonSITSummary[] = ['', ''];

        return $nonSITSummary;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                
                $alphabet = range('A', 'Z'); 
                foreach ($alphabet as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}