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
            ->orderByRaw("FIELD(type, 'Quiz', 'Additional Points Quiz', 'OtherActivity', 'Additional Points OT', 'Exam', 'Additional Points Exam', 'Lab Activity', 'Additional Points Lab', 'Lab Exam', 'Direct Bonus Grade')")
            ->orderBy('type', 'desc') 
            ->get();

        $midtermAssessments = Assessment::where('subject_id', $this->subjectId)
            ->where('grading_period', 'Midterm') 
            ->orderByRaw("FIELD(type, 'Quiz', 'Additional Points Quiz', 'OtherActivity', 'Additional Points OT', 'Exam', 'Additional Points Exam', 'Lab Activity', 'Additional Points Lab', 'Lab Exam', 'Direct Bonus Grade')")
            ->orderBy('type', 'desc') 
            ->get();

        $finalsAssessments = Assessment::where('subject_id', $this->subjectId)
            ->where('grading_period', 'Finals')
            ->orderByRaw("FIELD(type, 'Quiz', 'Additional Points Quiz', 'OtherActivity', 'Additional Points OT', 'Exam', 'Additional Points Exam', 'Lab Activity', 'Additional Points Lab', 'Lab Exam', 'Direct Bonus Grade')")
            ->orderBy('type', 'desc') 
            ->get();

        $assessments = $this->getAssessmentAbbreviations($assessments, 'First Grading');
        $midtermAssessments = $this->getAssessmentAbbreviations($midtermAssessments, 'Midterm');
        $finalsAssessments = $this->getAssessmentAbbreviations($finalsAssessments, 'Finals');
       
        $assessments = $assessments->groupBy('type')->map(function ($group) {
            return $group->sortBy(function ($assessment) {
                return $assessment->activity_date ?? PHP_INT_MAX;
            })->values();
        })->flatten();

        $midtermAssessments = $midtermAssessments->groupBy('type')->map(function ($group) {
            return $group->sortBy(function ($assessment) {
                return $assessment->activity_date ?? PHP_INT_MAX;
            })->values();
        })->flatten();

        $finalsAssessments = $finalsAssessments->groupBy('type')->map(function ($group) {
            return $group->sortBy(function ($assessment) {
                return $assessment->activity_date ?? PHP_INT_MAX;
            })->values();
        })->flatten();


        $hasFGAssessments = $assessments->isNotEmpty();
       
        $hasMidtermAssessments = $midtermAssessments->isNotEmpty();

      
        $hasFinalsAssessments = $finalsAssessments->isNotEmpty();


        $sortedStudents = collect($students)->groupBy('student.gender');
        
        $subjectInfoRows = $this->getSubjectInfoRows($subject);

      

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

         
    $assessmentTypeTotals = [];

        foreach ($assessments as $assessment) {
            if ($previousAssessmentType !== $assessment->type) {
                
                if ($previousAssessmentType) {
                    $assessmentHeaderRow[] = 'T';
                    $assessmentMaxPointsRow[] = $assessmentTypeTotals[$previousAssessmentType];
                    $assessmentTypeHeaderRow[] = ''; 
                    $gradingPeriodHeaderRow[] = ''; 
                }

                
                $assessmentTypeTotals[$assessment->type] = 0;

                
                $assessmentHeaderRow[] = $assessment->abbreviation;
                $assessmentMaxPointsRow[] = $assessment->max_points;
                $assessmentTypeHeaderRow[] = $assessment->type;

                
                if (!in_array($assessment->grading_period, $uniqueGradingPeriods)) {
                    $gradingPeriodHeaderRow[] = $assessment->grading_period;
                    $uniqueGradingPeriods[] = $assessment->grading_period;
                } else {
                    $gradingPeriodHeaderRow[] = ''; 
                }

                $previousAssessmentType = $assessment->type;
            } else {
              
                $assessmentHeaderRow[] = $assessment->abbreviation;
                $assessmentMaxPointsRow[] = $assessment->max_points;
                $assessmentTypeHeaderRow[] = ''; 

               
                if (!in_array($assessment->grading_period, $uniqueGradingPeriods)) {
                    $gradingPeriodHeaderRow[] = $assessment->grading_period;
                    $uniqueGradingPeriods[] = $assessment->grading_period;
                } else {
                    $gradingPeriodHeaderRow[] = '';
                }
            }

          
            $assessmentTypeTotals[$assessment->type] += $assessment->max_points;
        }


            if ($previousAssessmentType) {
                $assessmentHeaderRow[] = 'T';
                $assessmentMaxPointsRow[] = $assessmentTypeTotals[$previousAssessmentType];
                $assessmentTypeHeaderRow[] = ''; 
                $gradingPeriodHeaderRow[] = ''; 
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

$midtermAssessmentTypeTotals = [];

foreach ($midtermAssessments as $midtermAssessment) {
    if ($previousMidtermAssessmentType !== $midtermAssessment->type) {
        if ($previousMidtermAssessmentType) {
            $assessmentHeaderRow[] = 'T';
            $assessmentMaxPointsRow[] = $midtermAssessmentTypeTotals[$previousMidtermAssessmentType];
            $assessmentTypeHeaderRow[] = '';
            $gradingPeriodHeaderRow[] = '';
        }

        $midtermAssessmentTypeTotals[$midtermAssessment->type] = 0;

        $assessmentHeaderRow[] = $midtermAssessment->abbreviation;
        $assessmentMaxPointsRow[] = $midtermAssessment->max_points;
        $assessmentTypeHeaderRow[] = $midtermAssessment->type;

        if (!in_array($midtermAssessment->grading_period, $uniqueGradingPeriods)) {
            $gradingPeriodHeaderRow[] = $midtermAssessment->grading_period;
            $uniqueGradingPeriods[] = $midtermAssessment->grading_period;
        } else {
            $gradingPeriodHeaderRow[] = '';
        }

        $previousMidtermAssessmentType = $midtermAssessment->type;
    } else {
        $assessmentHeaderRow[] = $midtermAssessment->abbreviation;
        $assessmentMaxPointsRow[] = $midtermAssessment->max_points;
        $assessmentTypeHeaderRow[] = '';

        if (!in_array($midtermAssessment->grading_period, $uniqueGradingPeriods)) {
            $gradingPeriodHeaderRow[] = $midtermAssessment->grading_period;
            $uniqueGradingPeriods[] = $midtermAssessment->grading_period;
        } else {
            $gradingPeriodHeaderRow[] = '';
        }
    }

    $midtermAssessmentTypeTotals[$midtermAssessment->type] += $midtermAssessment->max_points;
}

if ($previousMidtermAssessmentType) {
    $assessmentHeaderRow[] = 'T';
    $assessmentMaxPointsRow[] = $midtermAssessmentTypeTotals[$previousMidtermAssessmentType];
    $assessmentTypeHeaderRow[] = '';
    $gradingPeriodHeaderRow[] = '';
}

$hasMidtermAssessments = count($midtermAssessments) > 0;

if ($hasMidtermAssessments) {
    $assessmentHeaderRow[] = 'MD Grade';
    $assessmentMaxPointsRow[] = '';
    $assessmentDateRow[] = '';
    $assessmentTypeHeaderRow[] = '';
    $gradingPeriodHeaderRow[] = '';
}

       $previousFinalsAssessmentType = null;

$finalsAssessmentTypeTotals = [];

foreach ($finalsAssessments as $finalsAssessment) {
    if ($previousFinalsAssessmentType !== $finalsAssessment->type) {
        if ($previousFinalsAssessmentType) {
            $assessmentHeaderRow[] = 'T';
            $assessmentMaxPointsRow[] = $finalsAssessmentTypeTotals[$previousFinalsAssessmentType] ?? 0;
            $assessmentTypeHeaderRow[] = '';
            $gradingPeriodHeaderRow[] = '';
        }

        // Initialize the total for the new assessment type
        $finalsAssessmentTypeTotals[$finalsAssessment->type] = 0;

        $assessmentHeaderRow[] = $finalsAssessment->abbreviation;
        $assessmentMaxPointsRow[] = $finalsAssessment->max_points;
        $assessmentDateRow[] = $finalsAssessment->activity_date;
        $assessmentTypeHeaderRow[] = $finalsAssessment->type;

        if (!in_array($finalsAssessment->grading_period, $uniqueGradingPeriods)) {
            $gradingPeriodHeaderRow[] = $finalsAssessment->grading_period;
            $uniqueGradingPeriods[] = $finalsAssessment->grading_period;
        } else {
            $gradingPeriodHeaderRow[] = ''; 
        }

        $previousFinalsAssessmentType = $finalsAssessment->type;
    } else {
        $assessmentHeaderRow[] = $finalsAssessment->abbreviation;
        $assessmentMaxPointsRow[] = $finalsAssessment->max_points;
        $assessmentDateRow[] = $finalsAssessment->activity_date;
        $assessmentTypeHeaderRow[] = '';

        if (!in_array($finalsAssessment->grading_period, $uniqueGradingPeriods)) {
            $gradingPeriodHeaderRow[] = $finalsAssessment->grading_period;
            $uniqueGradingPeriods[] = $finalsAssessment->grading_period;
        } else {
            $gradingPeriodHeaderRow[] = ''; 
        }
    }

    $finalsAssessmentTypeTotals[$finalsAssessment->type] += $finalsAssessment->max_points;
}

// Add total for the last assessment type
if ($previousFinalsAssessmentType) {
    $assessmentHeaderRow[] = 'T';
    $assessmentMaxPointsRow[] = $finalsAssessmentTypeTotals[$previousFinalsAssessmentType] ?? 0;
    $assessmentTypeHeaderRow[] = '';
    $gradingPeriodHeaderRow[] = '';
}

$hasFinalsAssessments = count($finalsAssessments) > 0;

if ($hasFinalsAssessments) {
    $assessmentHeaderRow[] = 'FN Grade';
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

        foreach ($sortedStudents as $gender => $students) {
            $assessmentRows[] = ['colspan' => count($assessmentHeaderRow), 'value' => $gender];

            foreach ($students as $student) {
                $assessmentRow = [
                    $student->student->id_number,
                    $student->student->last_name . ', ' . $student->student->name . ' ' . $student->student->middle_name,
                    $student->student->course,
                ];

                $assessmentTypeTotals = []; 
                $lastAssessmentType = null;
        foreach ($assessments as $index => $assessment) {
            $score = $student->getScore($assessment->id) ?: 'A';
            $assessmentRow[] = $score;

            
            if (is_numeric($score)) {
                $assessmentTypeTotals[$assessment->type] = ($assessmentTypeTotals[$assessment->type] ?? 0) + $score;
            }

            $isLastColumn = ($index === (count($assessments) - 1));
            $isLastColumnOfType = ($isLastColumn || $assessment->type !== $assessments[$index + 1]->type);

            // Add the total score for each assessment type beside the last column of the same type
            if ($isLastColumnOfType) {
                $assessmentRow[] = $assessmentTypeTotals[$assessment->type] ?? ''; // Handle the case when there is no numeric score
            }

            $lastAssessmentType = $assessment->type;
        }
                    if ($hasFGAssessments) {
                        $assessmentRow[] = $student->grades->avg('fg_grade');
                    }

                 $midtermAssessmentTypeTotals = [];
                $lastMidtermAssessmentType = null;

            foreach ($midtermAssessments as $index => $midtermAssessment) {
            $score = $student->getScore($midtermAssessment->id) ?: 'A';
            $assessmentRow[] = $score;

          
            if (is_numeric($score)) {
                $midtermAssessmentTypeTotals[$midtermAssessment->type] = ($midtermAssessmentTypeTotals[$midtermAssessment->type] ?? 0) + $score;
            }

            $isLastColumn = ($index === (count($midtermAssessments) - 1));
            $isLastColumnOfType = ($isLastColumn || $midtermAssessment->type !== $midtermAssessments[$index + 1]->type);

          
            if ($isLastColumnOfType) {
                $assessmentRow[] = $midtermAssessmentTypeTotals[$midtermAssessment->type] ?? ''; // Handle the case when there is no numeric score
            }

            $lastMidtermAssessmentType = $midtermAssessment->type;
        }

                    if ($hasMidtermAssessments) {
                        $assessmentRow[] = $student->grades->avg('midterms_grade');
                    }

            $finalsAssessmentTypeTotals = [];
                $lastFinalsAssessmentType = null;

             foreach ($finalsAssessments as $index => $finalsAssessment) {
            $score = $student->getScore($finalsAssessment->id) ?: 'A';
            $assessmentRow[] = $score;

          
            if (is_numeric($score)) {
                $finalsAssessmentTypeTotals[$finalsAssessment->type] = ($finalsAssessmentTypeTotals[$finalsAssessment->type] ?? 0) + $score;
            }

            $isLastColumn = ($index === (count($finalsAssessments) - 1));
            $isLastColumnOfType = ($isLastColumn || $finalsAssessment->type !== $finalsAssessments[$index + 1]->type);

            
            if ($isLastColumnOfType) {
                $assessmentRow[] = $finalsAssessmentTypeTotals[$finalsAssessment->type] ?? ''; // Handle the case when there is no numeric score
            }

            $lastFinalsAssessmentType = $finalsAssessment->type;
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


foreach ($assessments as $index => $assessment) {
    $assessmentDateRow[] = $assessment->activity_date;


    $isLastColumn = ($index === (count($assessments) - 1));
    $isLastColumnOfType = ($isLastColumn || $assessment->type !== $assessments[$index + 1]->type);

    if ($isLastColumnOfType) {
        $assessmentDateRow[] = '';
    }
}


if ($hasFGAssessments) {
    $assessmentDateRow[] = '';
}



foreach ($midtermAssessments as $index => $midtermAssessment) {
    $assessmentDateRow[] = $midtermAssessment->activity_date;

   
    $isLastColumn = ($index === (count($midtermAssessments) - 1));
    $isLastColumnOfType = ($isLastColumn || $midtermAssessment->type !== $midtermAssessments[$index + 1]->type);

    
    if ($isLastColumnOfType) {
        $assessmentDateRow[] = '';
    }
}


if ($hasMidtermAssessments) {
    $assessmentDateRow[] = '';
}
   
foreach ($finalsAssessments as $index => $finalsAssessment) {
    $assessmentDateRow[] = $finalsAssessment->activity_date;

   
    $isLastColumn = ($index === (count($finalsAssessments) - 1));
    $isLastColumnOfType = ($isLastColumn || $finalsAssessment->type !== $finalsAssessments[$index + 1]->type);

   
    if ($isLastColumnOfType) {
        $assessmentDateRow[] = '';
    }
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
               
                $event->sheet->mergeCells('A1:Z4');

              
                $event->sheet->setCellValue('A1', "Subject Code: {$subject->subject_code}                                                                            Days: {$subject->importedClasses->first()->days}\nDescription: {$subject->description}                        Time: {$subject->importedClasses->first()->time}\nTerm: {$subject->term}                                                         Section: {$subject->section}\nInstructor: {$subject->importedClasses->first()->instructor->name} {$subject->importedClasses->first()->instructor->middle_name} {$subject->importedClasses->first()->instructor->last_name}                                                                 Room: {$subject->importedClasses->first()->room}");


               
                $event->sheet->getStyle('A1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                ]);

            $lastColumn = 'ZZ';

            $lastDataRow = $event->sheet->getHighestDataRow();

          
            $event->sheet->getStyle("D{$lastDataRow}:{$lastColumn}{$lastDataRow}")->applyFromArray([
                'alignment' => [
                    'textRotation' => 90,
                ],
            ]);

            $event->sheet->getRowDimension($lastDataRow)->setRowHeight(70); 


            $worksheet = $event->sheet->getDelegate();
          
            
            $rowNumbers = [5,6,7,8]; 

            
            $exemptColumns = ['A', 'B', 'C']; 

            
            foreach ($rowNumbers as $rowNumber) {
                $highestColumn = $worksheet->getHighestColumn($rowNumber);
                $columns = range('A', $highestColumn);

                foreach ($columns as $column) {
                  
                    if (!in_array($column, $exemptColumns)) {
                        $worksheet->getColumnDimension($column)->setAutoSize(false);
                    }
                }
            }
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
            
        ];
    }
}
