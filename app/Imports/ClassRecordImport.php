<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ClassRecordImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

 //   public function model(array $row)
  //  {


        // Load the Excel file and get the active sheet
       // $file = $this->getFile();
       // $sheet = $file->getActiveSheet();

        // Access specific cells for data extraction
      //   $subjectcode = $sheet->getCell('C5')->getValue();
      //   $description = $sheet->getCell('C6')->getValue();
     //    $term = $sheet->getCell('C7')->getValue();
     //    $unit = $sheet->getCell('E5')->getValue();   


        // Map and validate your Excel columns here for Subjects table
      //  $subject = new Subject([
          //  'subject' => $subject_code,
         //   'description' =>  $description,   
         //   'term' => $term,   
         //   'units' => $unit,
           // 'days' => $row['days'],
            //'time' => $row['time'],
           // 'room' => $row['room'],
      //  ]);
   // }
}
