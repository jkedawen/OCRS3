<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Imports\ClassRecordImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Subject;
use App\Models\SubjectType;
use App\Models\ImportedClasslist;
use App\Models\User;
use App\Models\EnrolledStudents;
use Auth;

class ClassRecordController extends Controller
{

    public function getFile($file)
   {

    return IOFactory::load($file->getRealPath());

    }
  
     //mehtod for getting the values from excel file only, 
    public function import(Request $request)
    {
             $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
             ]);
           // load the excel file and get the active sheets 
             $file = $this->getFile($request->file('file'));
            $classList = $file->getSheet(0);  
           
         // Fetch subject types from the subject_type_percentage table
    $subjectTypePercentages = SubjectType::pluck('subject_type')->toArray();


    $additionalSubjectTypes = [
        'Lec',
        'Lab',
    ];

    // combine additional subject types with the fetched subject types from the db table
    $subjectType = array_merge($additionalSubjectTypes, $subjectTypePercentages);

                         

    $cellB5Value = $classList->getCell('B5')->getValue();



    $term = $this->extractLine($cellB5Value, 3);

    $section = $this->extractInformation($cellB5Value, 'Section :');
   
    $subjectInfo = $this->extractInformation($cellB5Value, 'Subject:');
    list($subjectCode, $subjectDescription) = $this->extractSubjectInfo($subjectInfo);

    $scheduleInfo = $this->extractInformation($cellB5Value, 'Schedule:');
    list($days, $time, $room) = $this->extractScheduleInfo($scheduleInfo);

    $maleStartRow = 7;
    $maleEndRow = $maleStartRow;

    while (!empty($classList->getCell('C' . ($maleEndRow + 1))->getValue())) {
        $maleEndRow++;
    }

    
    $femaleStartRow = $maleEndRow + 2;  
    $femaleEndRow = $femaleStartRow;

    //dd($femaleStartRow, $femaleEndRow);

    while (!empty($classList->getCell('C' . ($femaleEndRow + 1))->getValue())) {
        $femaleEndRow++;
    }

    
    $maleStudentValues = $this->fetchMaleStudentValues($classList, $maleStartRow, $maleEndRow);
    $femaleStudentValues = $this->fetchFemaleStudentValues($classList, $femaleStartRow, $femaleEndRow);


    
    return view('teacher.list.imported-data', compact('term', 'section', 'subjectCode', 'subjectDescription', 'days', 'time', 'room', 'maleStudentValues', 'femaleStudentValues','subjectType'));
  
  }
private function fetchMaleStudentValues($classList, $startRow, $endRow)
   {
    $studentValues = [];

    for ($row = $startRow; $row <= $endRow; $row++) {
        $idNumber = $classList->getCell('C' . $row)->getValue();
        $nameCell = $classList->getCell('D' . $row)->getValue();
        $course = $classList->getCell('G' . $row)->getValue();

        ///// skip if cell is blank
        if (empty($idNumber) || empty($nameCell) || empty($course)) {
            continue;
        }
        // Check if $nameCell is a string
        $nameCellValue = is_string($nameCell) ? $nameCell : $nameCell->getValue();

        // Extract parts of the name
        $nameParts = explode(',', $nameCellValue);
        $lastName = trim($nameParts[0]);
        $firstAndMiddleName = trim($nameParts[1]);

        // Split first and middle names
        $firstAndMiddleNameParts = explode(' ', $firstAndMiddleName, 2);
        $firstName = trim($firstAndMiddleNameParts[0]);
        $middleName = isset($firstAndMiddleNameParts[1]) ? trim($firstAndMiddleNameParts[1]) : '';

        //// list of student array
        $studentValues[] = [
            'id_number' => $idNumber,
            'last_name' => $lastName,
            'name' => $firstName,
            'middle_name' => $middleName,
            'course' => $course,
        ];
    }

    return $studentValues;
}
private function fetchFemaleStudentValues($classList, $startRow, $endRow)
   {
    $studentValues = [];

    for ($row = $startRow; $row <= $endRow; $row++) {
        $idNumber = $classList->getCell('C' . $row)->getValue();
        $nameCell = $classList->getCell('D' . $row)->getValue();
        $course = $classList->getCell('H' . $row)->getValue();

         // skip if cell is blank
        if (empty($idNumber) || empty($nameCell) || empty($course)) {
            continue;
        }

         // Check if $nameCell is a string
        $nameCellValue = is_string($nameCell) ? $nameCell : $nameCell->getValue();

        // Extract parts of the name
        $nameParts = explode(',', $nameCellValue);
        $lastName = trim($nameParts[0]);
        $firstAndMiddleName = trim($nameParts[1]);

        // Split first and middle names
        $firstAndMiddleNameParts = explode(' ', $firstAndMiddleName, 2);
        $firstName = trim($firstAndMiddleNameParts[0]);
        $middleName = isset($firstAndMiddleNameParts[1]) ? trim($firstAndMiddleNameParts[1]) : '';

        //// list of student array
        $studentValues[] = [
            'id_number' => $idNumber,
            'last_name' => $lastName,
            'name' => $firstName,
            'middle_name' => $middleName,
            'course' => $course,
        ];
    }

    return $studentValues;
   }
private function extractScheduleInfo($scheduleInfo)
   {
    //// use string manipulation/regular expressions to extract days, time, and room
    $matches = [];
    preg_match_all('/\b([MTWFSUH]+(?:\/[MTWFSUH]+)?)\b\s*([\d:]+\s*(?:AM|PM)-[\d:]+\s*(?:AM|PM))\s*\(([^)]+)\)/i', $scheduleInfo, $matches);

    // checks if matches are found
    if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
        $days = implode(', ', array_map('trim', $matches[1]));
        $time = implode(', ', array_map('trim', $matches[2]));
        $room = implode(', ', array_map('trim', $matches[3]));
    } else {
        // if no matches are found, set default values
        $days = '';
        $time = '';
        $room = '';
    }

    return [$days, $time, $room];
   }

private function extractSubjectInfo($subjectInfo)
   {
   /////// use string manipulation or regular expressions to extract subject code and description assuming the subject code is a sequence of uppercase letters and numbers and the description is everything after the subject code
    $matches = [];
    preg_match('/^([A-Z0-9]+)\s*(.*)$/i', $subjectInfo, $matches);

    
    if (isset($matches[1]) && isset($matches[2])) {
        $subjectCode = trim($matches[1]);
        $subjectDescription = trim($matches[2], " \t\n\r\0\x0B()");
    } else {
       
        $subjectCode = '';
        $subjectDescription = '';
    }
    return [$subjectCode, $subjectDescription];
    }

private function extractLine($text, $lineNumber)
    {
    //// split the text into lines
    $lines = explode("\n", $text);

    ///// check the specified line number exists
    if (isset($lines[$lineNumber - 1])) {
        return trim($lines[$lineNumber - 1]);
    }

    ////// no line number found, return empty string
    return '';
    }

private function extractInformation($text, $searchString)
    {
   
    $startPos = strpos($text, $searchString);
    if ($startPos !== false) {
        $startPos += strlen($searchString);
        return trim(substr($text, $startPos, strpos($text, "\n", $startPos) - $startPos));
    }

    return '';
    }




   public function savedataexcel(Request $request)
   {
              /////the extracted vlaues vfrom the class list
                $maleStudentValues = json_decode($request->input('male_student_values'), true);
                $femaleStudentValues = json_decode($request->input('female_student_values'), true);
                $subjectCode = trim($request->input('subject_code'), '"');
                $subjectDescription = trim($request->input('description'), '"');
                $term = trim($request->input('term'), '"');
                $section = trim($request->input('section'), '"');
               //// dd($section);
               $days = stripslashes(trim($request->input('days'), '"'));
                $time = trim($request->input('time'), '"');
                $room = trim($request->input('room'), '"');

                  
             $subjectType = $request->input('subject_type');
             /////dd($subjectType);
                        
           
                foreach ($maleStudentValues as $student) {
                    $this->createOrRetrieveStudent($student, true);
                }

              
                foreach ($femaleStudentValues as $student) {
                    $this->createOrRetrieveStudent($student, false);
                }
              
                $subject = Subject::where('subject_code', $subjectCode)->where('section', $section)->first();


                             if (!$subject) {
                    $subject = Subject::create([
                        'subject_code' => $subjectCode,
                        'description' => $subjectDescription,
                        'term' => $term,
                        'section' => $section,
                        'subject_type' => $subjectType,
                    ]);
                }

            //// get the current logged-in instructor
            $currentInstructor = Auth::user();

            ///// check if the imported classlist for Lec already exists
           $importedClasslist = ImportedClasslist::firstOrCreate(
                [
                    'subjects_id' => $subject->id,
                    'instructor_id' => $currentInstructor->id,
                    'days' => $days,
                    'time' => $time,
                    'room' => $room,
                ]
            );

          
         
            foreach ($maleStudentValues as $studentInfo) {
                $student = $this->createOrRetrieveStudent($studentInfo);

                /////// check if the student is already enrolled in the specific imported classlist for Lec 
                EnrolledStudents::firstOrCreate([
                    'student_id' => $student->id,
                    'imported_classlist_id' => $importedClasslist->id,
                ]);
            }

            ////// loop through female student data for Lec
            foreach ($femaleStudentValues as $studentInfo) {
                $student = $this->createOrRetrieveStudent($studentInfo);

                ///// check if the student is already enrolled in the specific imported classlist for Lec
                EnrolledStudents::firstOrCreate([
                    'student_id' => $student->id,
                    'imported_classlist_id' => $importedClasslist->id,
                ]);
            }

        
                
             //// notify if the values in the exscel were saved ot the subjects and students already exist
            $subjectExists = $subject->wasRecentlyCreated ? 'saved successfully' : 'already exists';
            $importedClasslistExists = $importedClasslist->wasRecentlyCreated ? 'saved successfully' : 'already exists';

        

       
            return view('teacher.list.importexcel', compact('subjectExists', 'importedClasslistExists'));
        
     }

     
    private function createOrRetrieveStudent($studentInfo, $isMale = true)
    {
        
        $gender = $isMale ? 'Male' : 'Female';

        $student = User::where('id_number', $studentInfo['id_number'])->first();

        if (!$student) {
            ///// student doesn't exist, create new student accont
            $student = new User([
                'id_number' => $studentInfo['id_number'],
                'name' => $studentInfo['name'],
                'middle_name' => $studentInfo['middle_name'],
                'last_name' => $studentInfo['last_name'],
                'course' => $studentInfo['course'],
                'password' => bcrypt('12345'), 
                'role' => 3,
                'gender' => $gender,
            ]);
            $student->save();
        }

        return $student;
    }
}

   
