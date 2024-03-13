  @extends('layouts.app')
   
  @section('content')

    <!-- Content Wrapper. Contains page content -->
  <body>
    ///
 
</body>
    <!-- /.content-header -->

    <!-- Main content -->
   
class StudentsSummaryExport implements FromCollection
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
    // Dump and die to check the fetched students
  //  dd($students->toArray());

        // Add data for each course
        $data[] = $this->generateCourseSummary($students, 'BSIT');
        $data[] = $this->generateCourseSummary($students, 'BSCS');
        $data[] = $this->generateCourseSummary($students, 'BSCpE');
        $data[] = $this->generateNonSITSummary($students);

        return collect($data)->flatten(1); // Flatten the array to remove nested arrays
    }

 

    protected function generateCourseSummary($students, $course)
    {
       $courseSummary = [];

    // Filter students based on the course
    $courseStudents = $students->filter(function ($student) use ($course) {
        return optional($student->student)->course === $course;
    });

        // Add categories based on the courseStudents...
        $courseSummary[] = ["Course: $course"];
        $courseSummary[] = ['Particulars', 'No. of Students'];
        $courseSummary[] = ['Students with grades of 80 and above', $courseStudents->where('grades.fg_grade', '>=', 80)->count()];
        $courseSummary[] = ['Students with grades of 75 to 79', $courseStudents->whereBetween('grades.fg_grade', [75, 79])->count()];
        $courseSummary[] = ['Students with grades below 75 but completed the semester', $courseStudents->where('grades.status', 'Completed')->count()];
        $courseSummary[] = ['Students with grades below 75 and stopped attending', $courseStudents->where('grades.status', 'Stopped Attending')->count()];
        $courseSummary[] = ['Students with INC grades', $courseStudents->where('grades.status', 'INC')->count()];
        $courseSummary[] = ['Students with NFE grades', $courseStudents->where('grades.status', 'NFE')->count()];
        $courseSummary[] = ['Students with DRP grades (never attended the class)', $courseStudents->where('grades.status', 'DRP')->count()];
      
        $courseSummary[] = ['TOTAL', $courseStudents->count()];

        $courseSummary[] = ['', '']; // Empty row for spacing

        return $courseSummary;
    }

    protected function generateNonSITSummary($students)
    {
        $nonSITSummary = [];

    // Filter students based on Non-SIT
    $nonSITStudents = $students->reject(function ($student) {
        return in_array(optional($student->student)->course, ['BSIT', 'BSCS', 'BSCpE']);
    });

        // Add categories based on the nonSITStudents...
        $nonSITSummary[] = ['Course: Non-SIT'];
        $nonSITSummary[] = ['Particulars', 'No. of Students'];
        $nonSITSummary[] = ['Students with grades of 80 and above', $nonSITStudents->where('grades.fg_grade', '>=', 80)->count()];
        $nonSITSummary[] = ['Students with grades of 75 to 79', $nonSITStudents->whereBetween('grades.fg_grade', [75, 79])->count()];
        $nonSITSummary[] = ['Students with grades below 75 but completed the semester', $nonSITStudents->where('grades.status', 'Completed')->count()];
        $nonSITSummary[] = ['Students with grades below 75 and stopped attending', $nonSITStudents->where('grades.status', 'Stopped Attending')->count()];
        $nonSITSummary[] = ['Students with INC grades', $nonSITStudents->where('grades.status', 'INC')->count()];
        $nonSITSummary[] = ['Students with NFE grades', $nonSITStudents->where('grades.status', 'NFE')->count()];
        $nonSITSummary[] = ['Students with DRP grades (never attended the class)', $nonSITStudents->where('grades.status', 'DRP')->count()];
      
        $nonSITSummary[] = ['TOTAL', $nonSITStudents->count()];

        $nonSITSummary[] = ['', '']; // Empty row for spacing

        return $nonSITSummary;
    }
}
    protected function processNonSit()
    {
        // Fetch students for Non-SIT
        $nonSitStudents = EnrolledStudents::with(['student', 'grades'])
            ->whereDoesntHave('importedClasses', function ($query) {
                $query->whereHas('subject', function ($query) {
                    $query->whereHas('importedClasses', function ($query) {
                        $query->join('users', 'users.id', '=', 'imported_classlist.instructor_id')
                            ->whereIn('users.course', ['BSIT', 'BSCS', 'BSCpE']);
                   });
            })->where('subjects_id', $this->subjectId); 
        })
        ->get();

        // Initialize counters for Non-SIT
        $count80Above = 0;
        $count75To79 = 0;
        $countBelow75 = 0;
        $countINC = 0;
        $countNFE = 0;
        $countDRP = 0;

        // Loop through Non-SIT students and count based on conditions
        foreach ($nonSitStudents as $student) {
            foreach ($student->grades as $grade) {
                $finalsGrade = $grade->finals_grade;
                $finalsStatus = $grade->finals_status;

                if ($finalsGrade >= 80) {
                    $count80Above++;
                } elseif ($finalsGrade >= 75 && $finalsGrade <= 79) {
                    $count75To79++;
                } elseif ($finalsGrade < 75) {
                    $countBelow75++;
                }

                if ($finalsStatus === 'INC') {
                    $countINC++;
                } elseif ($finalsStatus === 'NFE') {
                    $countNFE++;
                } elseif ($finalsStatus === 'DRP') {
                    $countDRP++;
                }
            }
        }

        // Return data for Non-SIT
        return [
            ['Course' => 'Non-SIT'],
            ['Particulars'],
            ['Students with grades of 80 and above', $count80Above],
            ['Students with grades of 75 to 79', $count75To79],
            ['Students with grades below 75 but completed the semester', $countBelow75],
            ['Students with grades below 75 and stopped attending', $countINC],
            ['Students with INC grades', $countNFE],
            ['Students with NFE grades', $countDRP],
            ['Students with DRP grades (never attended the class)', $countDRP],
            ['TOTAL', $count80Above + $count75To79 + $countBelow75 + $countINC + $countNFE + $countDRP],
            [''], // Add an empty row for spacing
        ];
    }
  <!-- /.content-w

<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\EnrolledStudents;
use App\Models\Assessment;
use App\Models\Subject;
use App\Models\ImportedClasslist;

class StudentReportExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
{
   

    protected $subjectId;

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

   protected function getAssessmentAbbreviations($assessments, $gradingPeriod)
{
    $typeAbbreviations = [
        'Quiz' => 'Q',
        'OtherActivity' => 'OT',
        'Exam' => 'E',
        'Lab Activity' => 'L',
        'Lab Exam' => 'E',
        'Additional Points Quiz' => 'APQ',
        'Additional Points OT' => 'APOAT',
        'Additional Points Exam' => 'APE',
        'Additional Points Lab' => 'APL',
        'Direct Bonus Grade' => 'FG',
    ];

    $exemptedTypes = ['Exam', 'Lab Exam', 'Additional Points Quiz', 'Additional Points OT', 'Additional Points Exam', 'Additional Points Lab', 'Direct Bonus Grade'];

    $typeCounts = [];

    $assessments = $assessments->map(function ($assessment) use ($typeAbbreviations, $exemptedTypes, &$typeCounts) {
        $type = $assessment->type;
        $gradingPeriod = $assessment->grading_period;

        if (in_array($type, $exemptedTypes)) {
            $assessment->abbreviation = $typeAbbreviations[$type];
        } else {
            $typeCounts[$gradingPeriod][$type] = $typeCounts[$gradingPeriod][$type] ?? 0;
            $count = ++$typeCounts[$gradingPeriod][$type];
            $assessment->abbreviation = $typeAbbreviations[$type] . $count;
        }

        return $assessment;
    });

    $typeMapping = [
        'Additional Points Quiz' => 'Quiz',
        'Additional Points OT' => 'OtherActivity',
        'Additional Points Exam' => 'Exam',
        'Additional Points Lab' => 'Lab Activity',
    ];

    $assessments = $assessments->map(function ($assessment) use ($typeMapping) {
        $assessment->type = $typeMapping[$assessment->type] ?? $assessment->type;
        return $assessment;
    });

    return $assessments;
}

    public function collection()
    {
          $students = EnrolledStudents::with(['student', 'grades'])
        ->whereHas('importedClasses', function ($query) {
            $query->where('subjects_id', $this->subjectId);
        })
        ->get();
        $passingStudents = $students->filter(function ($student) {
            return $student->grades->avg('finals_grade') >= 75;
        });

        $failingStudents = $students->reject(function ($student) use ($passingStudents) {
            return $passingStudents->contains('id', $student->id);
        });
        $subject = Subject::findOrFail($this->subjectId);

        $assessments = Assessment::where('subject_id', $this->subjectId)
            ->where('grading_period', 'First Grading') 
            ->orderByRaw("FIELD(type, 'Quiz', 'Additional Points Quiz' , 'OtherActivity', 'Additional Points OT','Exam', 'Additional Points Exam','Lab Activity','Additional Points Lab', 'Lab Exam','Direct Bonus Grade')")
            ->orderBy('type', 'desc') 
            ->get();

        $midtermAssessments = Assessment::where('subject_id', $this->subjectId)
            ->where('grading_period', 'Midterm') 
             ->orderByRaw("FIELD(type, 'Quiz', 'Additional Points Quiz' , 'OtherActivity', 'Additional Points OT','Exam', 'Additional Points Exam','Lab Activity','Additional Points Lab', 'Lab Exam','Direct Bonus Grade')")
            ->orderBy('type', 'desc') 
            ->get();

        $finalsAssessments = Assessment::where('subject_id', $this->subjectId)
            ->where('grading_period', 'Finals')
             ->orderByRaw("FIELD(type, 'Quiz', 'Additional Points Quiz' , 'OtherActivity', 'Additional Points OT','Exam', 'Additional Points Exam','Lab Activity','Additional Points Lab', 'Lab Exam','Direct Bonus Grade')")
            ->orderBy('type', 'desc') 
            ->get();
        $hasFGAssessments = $assessments->isNotEmpty();
       
        $hasMidtermAssessments = $midtermAssessments->isNotEmpty();

      
        $hasFinalsAssessments = $finalsAssessments->isNotEmpty();


        $sortedStudents = collect($students)->groupBy('student.gender');
        
        $subjectInfoRows = $this->getSubjectInfoRows($subject);

         $assessments = $this->getAssessmentAbbreviations($assessments, 'First Grading');
        $midtermAssessments = $this->getAssessmentAbbreviations($midtermAssessments, 'Midterm');
        $finalsAssessments = $this->getAssessmentAbbreviations($finalsAssessments, 'Finals');

         $assessmentRows = $this->getAssessmentRows($students, $assessments, $midtermAssessments, $finalsAssessments);



        return collect([...$subjectInfoRows, [], $this->getStudentHeaderRow(), ...$assessmentRows]);
    }



    
    private function getAssessmentRows($students, $assessments, $midtermAssessments, $finalsAssessments)
{
    $assessmentHeaderRow = [
      '', '', ''
      
    ];

    $assessmentMaxPointsRow = [
       'ID', 'Student Name', 'Course',
        
    ];

   
    $assessmentTypeHeaderRow = ['', '', ''];
    $gradingPeriodHeaderRow = ['', '', ''];

    
    $uniqueGradingPeriods = [];
     $previousAssessmentType = null;

  // Initialize an array to store total max_points for each assessment type
$assessmentTypeTotals = [];

// Build header rows for assessments
foreach ($assessments as $assessment) {
    if ($previousAssessmentType !== $assessment->type) {
        // Add the "Total" header for the previous assessment type (if any)
        if ($previousAssessmentType) {
            $assessmentHeaderRow[] = 'Total';
            $assessmentMaxPointsRow[] = $assessmentTypeTotals[$previousAssessmentType];
            $assessmentTypeHeaderRow[] = ''; // Empty cell
            $gradingPeriodHeaderRow[] = ''; // Empty cell
        }

        // Reset the total for the new assessment type
        $assessmentTypeTotals[$assessment->type] = 0;

        // Continue building header rows for the new assessment type
        $assessmentHeaderRow[] = $assessment->abbreviation;
        $assessmentMaxPointsRow[] = $assessment->max_points;
        $assessmentTypeHeaderRow[] = $assessment->type;

        // Check if grading period needs to be added
        if (!in_array($assessment->grading_period, $uniqueGradingPeriods)) {
            $gradingPeriodHeaderRow[] = $assessment->grading_period;
            $uniqueGradingPeriods[] = $assessment->grading_period;
        } else {
            $gradingPeriodHeaderRow[] = ''; // Empty cell
        }

        $previousAssessmentType = $assessment->type;
    } else {
        // Continue building header rows for the same assessment type
        $assessmentHeaderRow[] = $assessment->abbreviation;
        $assessmentMaxPointsRow[] = $assessment->max_points;
        $assessmentTypeHeaderRow[] = ''; // Empty cell

        // Check if grading period needs to be added
        if (!in_array($assessment->grading_period, $uniqueGradingPeriods)) {
            $gradingPeriodHeaderRow[] = $assessment->grading_period;
            $uniqueGradingPeriods[] = $assessment->grading_period;
        } else {
            $gradingPeriodHeaderRow[] = ''; // Empty cell
        }
    }

    // Add max_points to the total for the current assessment type
    $assessmentTypeTotals[$assessment->type] += $assessment->max_points;
}

// Add the "Total" column for the last assessment type (if any)
if ($previousAssessmentType) {
    $assessmentHeaderRow[] = 'Total';
    $assessmentMaxPointsRow[] = $assessmentTypeTotals[$previousAssessmentType];
    $assessmentTypeHeaderRow[] = ''; // Empty cell
    $gradingPeriodHeaderRow[] = ''; // Empty cell
}

    $hasFGAssessments = count($assessments) > 0;

    if ($hasFGAssessments) {
        $assessmentHeaderRow[] = 'FG Grade';
        $assessmentMaxPointsRow[] = '';
        $assessmentDateRow[] = '';
        $assessmentTypeHeaderRow[] = '';
         $gradingPeriodHeaderRow[] = ''; 
    }


        $previousMidtermAssessmentType = null;

        // Build header rows for midterm assessments
        foreach ($midtermAssessments as $midtermAssessment) {
            if ($previousMidtermAssessmentType !== $midtermAssessment->type) {
                $assessmentHeaderRow[] = $midtermAssessment->abbreviation;
                $assessmentMaxPointsRow[] = $midtermAssessment->max_points;
          
                $assessmentTypeHeaderRow[] = $midtermAssessment->type;

                // Check if grading period needs to be added
                if (!in_array($midtermAssessment->grading_period, $uniqueGradingPeriods)) {
                    $gradingPeriodHeaderRow[] = $midtermAssessment->grading_period;
                    $uniqueGradingPeriods[] = $midtermAssessment->grading_period;
                } else {
                    $gradingPeriodHeaderRow[] = ''; // Empty cell
                }

                $previousMidtermAssessmentType = $midtermAssessment->type;
            } else {
                // Skip adding the header if the type is the same as the previous midterm assessment
                $assessmentHeaderRow[] = $midtermAssessment->abbreviation;
                $assessmentMaxPointsRow[] = $midtermAssessment->max_points;
           
                $assessmentTypeHeaderRow[] = ''; // Empty cell

                // Check if grading period needs to be added
                if (!in_array($midtermAssessment->grading_period, $uniqueGradingPeriods)) {
                    $gradingPeriodHeaderRow[] = $midtermAssessment->grading_period;
                    $uniqueGradingPeriods[] = $midtermAssessment->grading_period;
                } else {
                    $gradingPeriodHeaderRow[] = ''; // Empty cell
                }
            }
        }

            $hasMidtermAssessments = count($midtermAssessments) > 0;

            if ($hasMidtermAssessments) {
                $assessmentHeaderRow[] = 'Midterm Grade';
                $assessmentMaxPointsRow[] = '';
                $assessmentDateRow[] = '';
                $assessmentTypeHeaderRow[] = '';
                 $gradingPeriodHeaderRow[] = ''; 
            }

           $previousFinalsAssessmentType = null;

        // Build header rows for finals assessments
        foreach ($finalsAssessments as $finalsAssessment) {
            if ($previousFinalsAssessmentType !== $finalsAssessment->type) {
                $assessmentHeaderRow[] = $finalsAssessment->abbreviation;
                $assessmentMaxPointsRow[] = $finalsAssessment->max_points;
                $assessmentDateRow[] = $finalsAssessment->activity_date;
                $assessmentTypeHeaderRow[] = $finalsAssessment->type;

                // Check if grading period needs to be added
                if (!in_array($finalsAssessment->grading_period, $uniqueGradingPeriods)) {
                    $gradingPeriodHeaderRow[] = $finalsAssessment->grading_period;
                    $uniqueGradingPeriods[] = $finalsAssessment->grading_period;
                } else {
                    $gradingPeriodHeaderRow[] = ''; // Empty cell
                }

                $previousFinalsAssessmentType = $finalsAssessment->type;
            } else {
                // Skip adding the header if the type is the same as the previous finals assessment
                $assessmentHeaderRow[] = $finalsAssessment->abbreviation;
                $assessmentMaxPointsRow[] = $finalsAssessment->max_points;
                $assessmentDateRow[] = $finalsAssessment->activity_date;
                $assessmentTypeHeaderRow[] = ''; // Empty cell

                // Check if grading period needs to be added
                if (!in_array($finalsAssessment->grading_period, $uniqueGradingPeriods)) {
                    $gradingPeriodHeaderRow[] = $finalsAssessment->grading_period;
                    $uniqueGradingPeriods[] = $finalsAssessment->grading_period;
                } else {
                    $gradingPeriodHeaderRow[] = ''; // Empty cell
                }
            }
        }

            $hasFinalsAssessments = count($finalsAssessments) > 0;

            if ($hasFinalsAssessments) {
                $assessmentHeaderRow[] = 'Final Grade';
                $assessmentMaxPointsRow[] = '';
                $assessmentDateRow[] = '';
                $assessmentTypeHeaderRow[] = '';
                 $gradingPeriodHeaderRow[] = ''; 
            }

            $assessmentRows = [
                 $gradingPeriodHeaderRow,
                $assessmentTypeHeaderRow,
                $assessmentHeaderRow,
                $assessmentMaxPointsRow,
               
            ];


    $sortedStudents = collect($students)->groupBy('student.gender');

    // Build student rows for assessments
    foreach ($sortedStudents as $gender => $students) {
        $assessmentRows[] = ['colspan' => count($assessmentHeaderRow), 'value' => $gender];

        foreach ($students as $student) {
            $assessmentRow = [
                $student->student->id_number,
                $student->student->last_name . ', ' . $student->student->name . ' ' . $student->student->middle_name,
                $student->student->course,
            ];

            foreach ($assessments as $assessment) {
                $assessmentRow[] = $student->getScore($assessment->id) ?: 'A';
            }

            if ($hasFGAssessments) {
                $assessmentRow[] = $student->grades->avg('fg_grade');
            }

            foreach ($midtermAssessments as $midtermAssessment) {
                $assessmentRow[] = $student->getScore($midtermAssessment->id) ?: 'A';
            }

            if ($hasMidtermAssessments) {
                $assessmentRow[] = $student->grades->avg('midterms_grade');
            }

            foreach ($finalsAssessments as $finalsAssessment) {
                $assessmentRow[] = $student->getScore($finalsAssessment->id) ?: 'A';
            }

            if ($hasFinalsAssessments) {
                $assessmentRow[] = $student->grades->avg('finals_grade');
            }

            $assessmentRows[] = $assessmentRow;
        }
    }

     
    $assessmentRows[] = [];


    $assessmentDateRow = [
        '', '', '',
    ];

    foreach ($assessments as $assessment) {
        $assessmentDateRow[] = $assessment->activity_date;
    }

    if ($hasFGAssessments) {
        $assessmentDateRow[] = '';
    }

    foreach ($midtermAssessments as $midtermAssessment) {
        $assessmentDateRow[] = $midtermAssessment->activity_date;
    }

    if ($hasMidtermAssessments) {
        $assessmentDateRow[] = '';
    }

    foreach ($finalsAssessments as $finalsAssessment) {
        $assessmentDateRow[] = $finalsAssessment->activity_date;
    }

    if ($hasFinalsAssessments) {
        $assessmentDateRow[] = '';
    }

    $assessmentRows = array_merge($assessmentRows, [$assessmentDateRow]);

    return $assessmentRows;
}

    protected function getSubjectInfoRows(Subject $subject): array
    {
        return [
           ['Subject Code:', $subject->subject_code, 'Days:', $subject->importedClasses->first()->days],
            ['Description:', $subject->description, 'Time:',  $subject->importedClasses->first()->time],
            ['Term:', $subject->term, 'Section:', $subject->section],
            ['Instructor:', $subject->importedClasses->first()->instructor->name . ' ' .$subject->importedClasses->first()->instructor->middle_name . ' ' .$subject->importedClasses->first()->instructor->last_name, 'Room:', $subject->importedClasses->first()->room],
           
        ];
    }

    public function registerEvents(): array
    {

            $subject = Subject::findOrFail($this->subjectId);
           return [
            AfterSheet::class => function (AfterSheet $event) use ($subject) {
                // Merge cells for Subject Details (A1:F4)
                $event->sheet->mergeCells('A1:Z4');

              
                $event->sheet->setCellValue('A1', "Subject Code: {$subject->subject_code}                                                                            Days: {$subject->importedClasses->first()->days}\nDescription: {$subject->description}                        Time: {$subject->importedClasses->first()->time}\nTerm: {$subject->term}                                                         Section: {$subject->section}\nInstructor: {$subject->importedClasses->first()->instructor->name} {$subject->importedClasses->first()->instructor->middle_name} {$subject->importedClasses->first()->instructor->last_name}                                                                 Room: {$subject->importedClasses->first()->room}");


                // Set alignment and text wrapping for the merged cell
                $event->sheet->getStyle('A1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                ]);
            },
        ];
    }

    public function headings(): array
    {
        return [
         
        ];
    }

    protected function getStudentHeaderRow(): array
    {
        return [];
    }
     public function columnFormats(): array
    {
        return [
            // ... Define column formats here if needed ...
        ];
    }
}




    

       <tr>

{{ number_format($score->assessment->max_points, $score->assessment->max_points == intval($score->assessment->max_points) ? 0 : 2) }}
         @foreach($scores as $score)
                    @if ($score->fg_grade !== null || $score->midterms_grade !== null || $score->finals_grade !== null)
                        <tr>
                            <td>Overall Grades</td>
                            <td>
                                @if ($score->fg_grade !== null && $score->published)
                                    First Grading Grade: {{ $score->fg_grade }}<br>
                                @endif

                                @if ($score->midterms_grade !== null && $score->published_midterms)
                                    Midterm Grade: {{ $score->midterms_grade }}<br>
                                @endif

                                @if ($score->finals_grade !== null && $score->published_finals)
                                    Finals Grade: {{ $score->finals_grade }}
                                @endif
                            </td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                                <th class="fixed-column"></th> 
                                <th class="fixed-column"></th> 
                                 <th class="fixed-column"></th> 
                                <th class="fixed-column"></th> 

                              @foreach ($gradingPeriods as $gradingPeriod)
                                    @php
                                     // Define the assessment types to be grouped together
                                        $groupedAssessmentTypes = [
                                            'Quiz' => ['Quiz', 'Additional Points Quiz'],
                                            // Add other types as needed
                                        ];


                            @endphp

                              @foreach ($groupedAssessmentTypes as $groupedType => $subTypes)
                                        <th colspan="{{ $assessments->where('grading_period', $gradingPeriod)->whereIn('type', $subTypes)->count() }}" class="text-center assessment-type-header">
                                            {{ $groupedType }}
                                        </th>
                                       @if ($groupedType == 'Quiz') <!-- Include Total header only for 'Quiz' type -->
                                                <th class="text-center">Total</th>
                                            @endif
                                    @endforeach
          
                                    @if ($gradingPeriod == "First Grading")
                                        <th class="text-center">FG Grade</th>
                                    @endif

                                      
                                    @if ($gradingPeriod == "Midterm")
                                        <th class="text-center">Midterm Grade</th>
                                    @endif

                                    @if ($gradingPeriod == "Finals")
                                        <th class="text-center">Finals Grade</th>
                                    @endif
                                @endforeach

                            </tr>

                    <tr>
                        <th class="fixed-column"></th> 
                         <th class="fixed-column"></th> 
                        <th class="fixed-column"></th> 
                        <th class="fixed-column"></th> 
  @foreach ($gradingPeriods as $gradingPeriod)
        @php
            $quizMaxPointsTotal = 0; // Initialize total max points for Quiz assessments
        @endphp

        @foreach ($groupedAssessmentTypes as $groupedType => $subTypes)
            @foreach ($subTypes as $assessmentType)
                @php
                    $gradingPeriodAssessments = $assessments
                        ->where('grading_period', $gradingPeriod)
                        ->where('type', $assessmentType)
                        ->sortBy(function ($assessment) {
                            $typeOrder = [
                                'Quiz' => 1,
                                'Additional Points Quiz' => 1,
                                'OtherActivity' => 3,
                                'Additional Points OT' => 4,
                                'Exam' => 5,
                                'Additional Points Exam' => 6,
                                'Lab Activity' => 7,
                                'Lab Exam' => 8,
                                'Additional Points Lab' => 9,
                                'Direct Bonus Grade' => 10,
                            ];

                            return [
                                'type_order' => $typeOrder[$assessment->type] ?? 999,
                                'activity_date' => $assessment->activity_date ?? '',
                            ];
                        });

                    $maxPointsTotal = $gradingPeriodAssessments->sum('max_points');
                    $hasAssessments = $gradingPeriodAssessments->isNotEmpty();
                @endphp

                <!-- Loop for assessment details -->
                @foreach ($gradingPeriodAssessments as $assessment)
                    <th class="assessment-column">
                        <p class="assessment-description"
                            data-grading-period="{{ $assessment->grading_period }}"
                            data-type="{{ $assessment->type }}"
                            data-description="{{ $assessment->description }}">
                            {{ $assessment->abbreviation }} <br> {{ number_format($assessment->max_points, $assessment->max_points == intval($assessment->max_points) ? 0 : 2) }}
                        </p>
                    </th>
                @endforeach

                <!-- Additional Points Quiz and Total column -->
                @if ($groupedType == 'Quiz' && $assessmentType == 'Additional Points Quiz')
                    @if ($hasAssessments)
                        <td class="assessment-column">
                            <p class="assessment-description"
                                data-grading-period="{{ $gradingPeriod }}"
                                data-type="{{ $assessmentType }}"
                                data-description="Total Max Points">
                                {{ $maxPointsTotal }}
                            </p>
                        </td>
                    @endif
                @endif

                <!-- Update Quiz assessments' total max points -->
                @if ($groupedType == 'Quiz')
                    @php
                        $quizMaxPointsTotal += $maxPointsTotal;
                    @endphp
                @endif
            @endforeach
        @endforeach

        <!-- Display total max points for Quiz assessments -->
        <td class="assessment-column">
            <p class="assessment-description"
                data-grading-period="{{ $gradingPeriod }}"
                data-type="Quiz"
                data-description="Total Max Points">
                {{ $quizMaxPointsTotal }}
            </p>
        </td>

             
      

                                   
                                    @if ($gradingPeriod == "First Grading")
                                        <th class="text-center"></th>
                                    @endif
                                    
                                    @if ($gradingPeriod == "Midterm")
                                        <th class="text-center"></th>
                                    @endif

                                   
                                    @if ($gradingPeriod == "Finals")
                                        <th class="text-center"></th>
                                    @endif

                                         @endforeach
                                  
                                        
                                 </tr>


    <button class="btn btn-sm btn-publish-grades btn-primary" data-grading-period="' . $gradingPeriod . '" data-published="' . ($gradingPeriod === 'First Grading' ? 'true' : 'false') . '">
                                        ' . ($gradingPeriod === 'First Grading' ? 'Hide' : 'Publish') . ' Grades
                                    </button>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Attach a click event to the publish buttons for grades
    document.querySelectorAll('.btn-publish-grade').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default form submission

            var studentId = this.getAttribute('data-student-id');
            var gradeType = this.getAttribute('data-grade-type');
            var assessmentId = this.getAttribute('data-assessment-id');
            var isPublished = this.getAttribute('data-published') === 'true';

            // Log the current data for debugging
            console.log('Student ID:', studentId);
            console.log('Grade Type:', gradeType);
            console.log('Assessment ID:', assessmentId);
            console.log('Is Published:', isPublished);

            // You can show a confirmation dialog here if needed
            var confirmPublish = confirm('Do you want to ' + (isPublished ? 'hide' : 'publish') + ' ' + gradeType.toUpperCase() + ' for this student?');

            if (confirmPublish) {
                // Log that the user confirmed
                console.log('User confirmed to publish/hide.');

                // Perform an AJAX request to update the publishing status
                // Make sure to provide the correct URL for the route
                fetch('/update-publish-status-for-grades', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ studentId: studentId, gradeType: gradeType, assessmentId: assessmentId, isPublished: !isPublished })
                })
                .then(response => response.json())
                .then(data => {
                    // Log the response data
                    console.log('Response Data:', data);

                    // Handle the response if needed
                    // For example, you can update the button text
                    btn.innerText = isPublished ? 'Publish ' + gradeType.toUpperCase() : 'Hide ' + gradeType.toUpperCase();
                    btn.setAttribute('data-published', isPublished ? 'false' : 'true');
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Log that there was an error
                    console.error('An error occurred during the AJAX request.');
                    // Handle errors if needed
                });
            } else {
                // Log that the user canceled
                console.log('User canceled the publish/hide operation.');
            }
        });
    });
});
</script>


             ///// Empty th for grades column under 
                               echo '<th class="grade-column">';
    
                                // Add buttons for publishing grades
                                foreach ($enrolledStudent->grades as $grade) {
                                    if ($grade->fg_grade !== null) {
                                        $gradeType = 'fg_grade';
                                        $isPublished = $grade->published ? 'true' : 'false';
                                        $assessmentId = $grade->assessment_id;
                                        
                                        echo '<button class="btn btn-sm btn-publish-grade" ' .
                                                'data-grade-type="' . $gradeType . '" ' .
                                                'data-student-id="' . $enrolledStudent->id . '" ' .
                                                'data-assessment-id="' . $assessmentId . '" ' .
                                                'data-published="' . $isPublished . '">' .
                                                ($grade->published ? 'Hide' : 'Publish') . ' FG Grade' .
                                              '</button>';
                                    } elseif ($grade->midterm_grade !== null) {
                                        $gradeType = 'midterm_grade';
                                        $isPublished = $grade->published ? 'true' : 'false';
                                        $assessmentId = $grade->assessment_id;
                                        
                                        echo '<button class="btn btn-sm btn-publish-grade" ' .
                                                'data-grade-type="' . $gradeType . '" ' .
                                                'data-student-id="' . $enrolledStudent->id . '" ' .
                                                'data-assessment-id="' . $assessmentId . '" ' .
                                                'data-published="' . $isPublished . '">' .
                                                ($grade->published ? 'Hide' : 'Publish') . ' Midterm Grade' .
                                              '</button>';
                                    } elseif ($grade->finals_grade !== null) {
                                        $gradeType = 'finals_grade';
                                        $isPublished = $grade->published ? 'true' : 'false';
                                        $assessmentId = $grade->assessment_id;
                                        
                                        echo '<button class="btn btn-sm btn-publish-grade" ' .
                                                'data-grade-type="' . $gradeType . '" ' .
                                                'data-student-id="' . $enrolledStudent->id . '" ' .
                                                'data-assessment-id="' . $assessmentId . '" ' .
                                                'data-published="' . $isPublished . '">' .
                                                ($grade->published ? 'Hide' : 'Publish') . ' Finals Grade' .
                                              '</button>';
                                    }
                                }
                                
                                // Close the empty th
                                echo '</th>';
                                $currentColIndex++;


     /* Adjust the font size for the entire table */
    table {
        font-size: 12px; /* You can adjust this value as needed */
    }

    /* Adjust the font size for th and td elements */
    th, td {
        font-size: 12px; /* You can adjust this value as needed */
    }

    /* Adjust the padding for th and td elements */
    th, td {
        padding: 8px; /* You can adjust this value as needed */
    }

    /* Adjust the height of the table rows */
    tr {
        height: 30px; /* You can adjust this value as needed */
    }

    /* Add a max-width to the table to control its overall width */
    table {
        max-width: 800px; /* You can adjust this value as needed */
        width: 100%;
    }

    /* Optionally, you can add styles for other elements such as input fields */
    input {
        font-size: 12px; /* You can adjust this value as needed */
        height: 20px; /* You can adjust this value as needed */
    }


   <th class="assessment-column">
                <p>Total Max Points</p>
            </th>
            <th class="assessment-column">
                <p>Total Points</p>
            </th>



    
 <tr>
                                <th class="fixed-column"></th> 
                                <th class="fixed-column"></th> 
                                 <th class="fixed-column"></th> 
                                <th class="fixed-column"></th> 

                              @foreach ($gradingPeriods as $gradingPeriod)
                                    @php
                                        $gradingPeriodAssessmentTypes = $assessments
                                            ->where('grading_period', $gradingPeriod)
                                            ->pluck('type')
                                            ->unique();
                                    @endphp

                                    @foreach ($gradingPeriodAssessmentTypes as $assessmentType)
                                        <th colspan="{{ $assessments->where('grading_period', $gradingPeriod)->where('type', $assessmentType)->count() }}" class="text-center assessment-type-header">
                                            {{ $assessmentType }}
                                        </th>
                                         <th class="text-center assessment-type-header">Total Max Points</th>
                                    @endforeach
                                @endforeach
                            </tr>

                    <tr>
                        <th class="fixed-column"></th> 
                         <th class="fixed-column"></th> 
                        <th class="fixed-column"></th> 
                        <th class="fixed-column"></th> 

                        @foreach ($gradingPeriods as $gradingPeriod)
                            @foreach ($assessmentTypes as $assessmentType)
                                @php
                                    $gradingPeriodAssessments = $assessments
                                        ->where('grading_period', $gradingPeriod)
                                        ->where('type', $assessmentType)
                                        ->sortBy(function ($assessment) {
                                            $typeOrder = [
                                                        'Quiz' => 1,
                                                        'Additional Points Quiz' => 2,
                                                        'OtherActivity' => 3,
                                                        'Additional Points OT' => 4,
                                                        'Exam' => 5,
                                                        'Additional Points Exam' => 6,
                                                        'Lab Activity' => 7,
                                                        'Lab Exam' => 8,
                                                        'Additional Points Lab' => 9,
                                                        'Direct Bonus Grade' => 10,
                                                        ];

                                                        
                                                            return $typeOrder[$assessment->type] ?? 999;
                                                        });


             
                                                $maxPointsTotal = $gradingPeriodAssessments->sum('max_points');
                                                    @endphp



                                            @foreach ($gradingPeriodAssessments as $assessment)
                                                        <th class="assessment-column">
                                                            <p class="assessment-description"
                                                                data-grading-period="{{ $assessment->grading_period }}"
                                                                data-type="{{ $assessment->type }}"
                                                                data-description="{{ $assessment->description }}">
                                                                {{ $assessment->abbreviation }} <br>{{ $assessment->max_points }} 
                                                            </p>
                                                        </th>    
                                                @endforeach


                                        <td class="assessment-column">
                                            <p class="assessment-description"
                                             data-grading-period="{{ $assessment->grading_period }}"
                                            data-type="{{ $assessment->type }}"
                                             data-description="Total Max Points">
                                                {{ $maxPointsTotal }}
                                            </p>
                                        </td>

           
                                              @endforeach
                                         @endforeach
                                 </tr>

                            <tr>
                                <th class="fixed-column">No.</th> 
                                <th class="fixed-column">ID</th> 
                                <th class="fixed-column">Name</th> 
                                <th class="fixed-column">Course</th> 
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $studentNumberMale = 1;
                                $studentNumberFemale = 1;
                            @endphp
                            @foreach ($sortedStudents as $gender => $students)
                                <tr>
                                    <td colspan="{{ count($gradingPeriods) + 99 }}" class="gender-header">
                                        {{ $gender }}
                                    </td>
                                </tr>

                                @foreach ($students as $enrolledStudent)
                                    <tr>
                                         <td class="fixed-column">
                                                {{ $gender == 'Male' ? $studentNumberMale++ : $studentNumberFemale++ }}
                                            </td>
                                        <td class="fixed-column">{{ $enrolledStudent->student->id_number }}</td>
                                        <td class="fixed-column">{{ $enrolledStudent->student->last_name }}, {{ $enrolledStudent->student->name }} {{ $enrolledStudent->student->middle_name }}</td>
                                        <td class="fixed-column">{{ $enrolledStudent->student->course }}</td>

                                        @foreach ($gradingPeriods as $gradingPeriod)
                                            @php
                                                $gradingPeriodAssessments = $assessments
                                                    ->where('grading_period', $gradingPeriod)
                                                     ->sortBy(function ($assessment) {
                                            
                                            $typeOrder = [
                                                'Quiz' => 1,
                                                'Additional Points Quiz' => 2,
                                                'OtherActivity' => 3,
                                                'Additional Points OT' => 4,
                                                'Exam' => 5,
                                                'Additional Points Exam' => 6,
                                                'Lab Activity' => 7,
                                                'Lab Exam' => 8,
                                                'Additional Points Lab' => 9,
                                                'Direct Bonus Grade' => 10,
                                            ];

                                           
                                            return $typeOrder[$assessment->type] ?? 999;
                                        });

                                            @endphp

                                            @foreach ($gradingPeriodAssessments as $assessment)
                                                <td class="assessment-column">
                                                    <input type="number"
                                                        name="points[{{ $enrolledStudent->id }}][{{ $assessment->id }}]"
                                                        class="form-control assessment-input"
                                                        data-grading-period="{{ $assessment->grading_period }}"
                                                        data-type="{{ $assessment->type }}"
                                                        value="{{ is_null($enrolledStudent->getScore($assessment->id)) ? 'A' : $enrolledStudent->getScore($assessment->id) }}"
                                                        style="width: 80px; text-align: center;">
                                                </td>
                                                  <td class="assessment-column">
                                        <p class="assessment-description" data-grading-period="{{ $gradingPeriod }}" data-type="{{ $assessmentType }}" data-description="Total Points">
                                            0 
                                        </p>
                                    </td>

                                            @endforeach
                                          
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach

      





 public function fetchAssessments(Request $request)
{
    

    $gradingPeriod = $request->input('grading_period');
    $type = $request->input('type');

    
    $subjectType = $request->input('subject_type');

  
     $assessments = Assessment::where('grading_period', $gradingPeriod);

     $subjectId = $request->input('subject_id'); // Add this line

  if ($type !== 'All') {
        $assessments->where('type', $type);
    }

    $assessments->where('subject_id', $subjectId);
   
    $assessments->where('subject_type', $subjectType);

    $assessments = $assessments->get()->map(function ($assessment) {
        return [
            'type' => $assessment->type,
            'description' => $assessment->description,
            'maxPoints' => (float) $assessment->max_points,
            'activity_date' => $assessment->activity_date,
        ];
    });

   
    return response()->json(['assessments' => $assessments]);
}

<tr>
    <th class="fixed-column"></th>
    <th class="fixed-column"></th>
    <th class="fixed-column"></th>
    <th class="fixed-column"></th>

    @php
        $gradingPeriods = $assessments->pluck('grading_period')->unique()->sort();
        $assessmentTypes = $assessments->pluck('type')->unique();
    @endphp

    @foreach ($gradingPeriods as $gradingPeriod)
        @php
            $gradingPeriodAssessmentTypes = $assessments
                ->where('grading_period', $gradingPeriod)
                ->pluck('type')
                ->unique();
        @endphp

        <th colspan="{{ count($gradingPeriodAssessmentTypes) }}" class="text-center grading-period-header">
            {{ $gradingPeriod }}
        </th>
    @endforeach
</tr>
<style>
                                    .table-scroll-container {
                                        overflow-x: auto;
                                        max-width: 100%;
                                    }

                                   .table-container table {
                                        width: auto;
                                        border-collapse: collapse; /* Add this line for border-collapse */
                                    }

                                    .fixed-column {
                                        position: sticky;
                                        left: 0;
                                        z-index: 1;
                                        border: 4px solid #ddd; /* Add this line for a border */
                                    }

                                    .assessment-column {
                                        text-align: center;
                                        width: 80px;
                                        border: 4px solid #ddd; /* Add this line for a border */
                                    }

                                    .assessment-type-header,
                                    .grading-period-header,
                                    .gender-header {
                                        background-color: #f2f2f2;
                                        border: 4px solid #ddd; /* Add this line for a border */
                                    }
                                </style>


 
 <style>
                                    .table-scroll-container {
                                        overflow-x: auto;
                                        max-width: 100%;
                                    }

                                    .table-container table {
                                        width: auto;
                                    }

                                    .fixed-column {
                                        position: sticky;
                                        left: 0;
                                        z-index: 1;
                                        background-color: #fff; /* Adjust the background color as needed */
                                    }

                                    .assessment-column {
                                        text-align: center;
                                        width: 80px; /* Adjust the width as needed */
                                    }

                                    .grading-period-header,
                                    .gender-header {
                                        background-color: #f2f2f2; /* Adjust the background color as needed */
                                    }
                                </style>




 thead>


                         <tr>
        <th class="fixed-column"></th>
        <th class="fixed-column"></th>
        <th class="fixed-column"></th>

        @php
            $gradingPeriods = $assessments->pluck('grading_period')->unique();
            $assessmentTypes = $assessments->pluck('type')->unique();
        @endphp

        @foreach ($gradingPeriods as $gradingPeriod)
            <th colspan="{{ count($assessmentTypes) }}" class="text-center">
                {{ $gradingPeriod }}
            </th>
        @endforeach
    </tr>

    <tr>
        <th class="fixed-column">ID</th> 
        <th class="fixed-column">Name</th> 
        <th class="fixed-column">Course</th> 

        @foreach ($gradingPeriods as $gradingPeriod)
            @foreach ($assessmentTypes as $assessmentType)
                <th colspan="{{ $assessments->where('grading_period', $gradingPeriod)->where('type', $assessmentType)->count() }}" class="text-center">
                    {{ $assessmentType }}
                </th>
            @endforeach
        @endforeach
    </tr>

    <tr>
        <th class="fixed-column"></th> 
        <th class="fixed-column"></th> 
        <th class="fixed-column"></th> 

        @foreach ($gradingPeriods as $gradingPeriod)
            @foreach ($assessmentTypes as $assessmentType)
                @php
                    $gradingPeriodAssessments = $assessments
                        ->where('grading_period', $gradingPeriod)
                        ->where('type', $assessmentType)
                        ->sortBy(function ($assessment) {
                            $typeOrder = [
                                                'Quiz' => 1,
                                                'Additional Points Quiz' => 2,
                                                'OtherActivity' => 3,
                                                'Additional Points OT' => 4,
                                                'Exam' => 5,
                                                'Additional Points Exam' => 6,
                                                'Lab Activity' => 7,
                                                'Additional Points Lab' => 8,
                                                'Direct Bonus Grade' => 9,
                                            ];

                                           
                                            return $typeOrder[$assessment->type] ?? 999;
                                        });
                                    @endphp



                                    @foreach ($gradingPeriodAssessments as $assessment)
                                        <th class="assessment-column">
                                            <p class="assessment-description"
                                                data-grading-period="{{ $assessment->grading_period }}"
                                                data-type="{{ $assessment->type }}"
                                                data-description="{{ $assessment->description }}">
                                                {{ $assessment->abbreviation }} <br>{{ $assessment->max_points }}</br>
                                                <br>{{ $assessment->activity_date }}</br>
                                            </p>
                                        </th>

                                        
                                    @endforeach
                                @endforeach
                                  @endforeach
                            </tr>



                        </thead>


    rapper -->

  @endsection