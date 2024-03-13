<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\EnrolledStudents;
use App\Models\Assessment;
use App\Models\Subject;
use App\Models\ImportedClasslist;

class StudentGradeExport implements FromCollection, WithHeadings, WithStyles
{
    protected $subject;
    protected $students;
    protected $sortedStudents;

    public function __construct($subject, $students, $sortedStudents)
    {
        $this->subject = $subject;
        $this->students = $students;
        $this->sortedStudents = $sortedStudents;
    }

    public function collection()
    {
        
  
        $data = [];

     
        $data[] = ['Subject Code:', $this->subject->subject_code];
        $data[] = ['Description:', $this->subject->description];
        $data[] = ['Term:', $this->subject->term];
        $data[] = ['Instructor:', $this->subject->importedClasses->first()->instructor->name . ' ' .  $this->subject->importedClasses->first()->instructor->middle_name . ' ' . $this->subject->importedClasses->first()->instructor->last_name];

        
        $data[] = ['ID Number', 'Student Name', 'Course', 'First Grading', 'Midterms', 'Finals'];

       
        foreach ($this->sortedStudents as $gender => $students) {
            $data[] = [$gender, '', '', '', '', '']; 
            foreach ($students as $student) {
                $rowData = [
                    $student->student->id_number,
                    $student->student->last_name . ', ' . $student->student->name . ' ' . $student->student->middle_name,
                    $student->student->course,
                    $this->getGradeValue($student, 'fg_grade'),
                    $this->getGradeValue($student, 'midterms_grade'),
                    $this->getGradeValue($student, 'finals_grade'),
                ];
                $data[] = $rowData;
            }
        }

        return collect($data);
}

  private function getGradeValue($student, $gradeType)
    {
        foreach ($student->grades as $grade) {
            if ($grade->$gradeType !== null) {
                return $grade->$gradeType;
            }
        }
        return '';
    }

    public function headings(): array
    {
        return [
            
        ];
    }

    public function styles(Worksheet $sheet)
    {
    $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Arial, sans-serif',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'], // Background color
            ],
        ]);

       
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }


      
        $boldHeaders = [2, 3, 4, 5];
        foreach ($boldHeaders as $headerRow) {
            $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
        }

        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}