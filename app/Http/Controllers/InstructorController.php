<?php

namespace App\Http\Controllers;
use App\Models\Subject;
use App\Models\EnrolledStudents;
use App\Models\ImportedClasslist;
use App\Models\Assessment;
use App\Models\AssessmentDescription;
use App\Models\User;
use App\Models\Grades;
use App\Models\SubjectType;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Auth;
use Illuminate\Http\Request;

class InstructorController extends Controller
{

    public function listSubjects()
    {
    // get subjects taught by the current loggedin instructor
       $instructorId = Auth::user()->id;

        $currentSemester = Semester::where('is_current', true)->first();
      $subjects = Subject::where('term', $currentSemester->semester_name . ', ' . $currentSemester->school_year)
        ->whereHas('importedclasses', function ($query) use ($instructorId) {
            $query->where('instructor_id', $instructorId);
        })
        ->get();
        $subjectTypePercentages = SubjectType::pluck('subject_type')->toArray();


    $additionalSubjectTypes = [
        'Lec',
        'Lab',
    ];

    // combine additional subject types with the fetched subject types from the db table
    $subjectTypes = array_merge($additionalSubjectTypes, $subjectTypePercentages);

      return view('teacher.list.classlist', compact('subjects','subjectTypes'));
      }

    public function pastlistSubjects()
    {
    // get subjects taught by the current loggedin instructor
       $instructorId = Auth::user()->id;

        $currentSemester = Semester::where('is_current', true)->first();
      $subjects = Subject::where('term', '!=', $currentSemester->semester_name . ', ' . $currentSemester->school_year)
        ->whereHas('importedclasses', function ($query) use ($instructorId) {
            $query->where('instructor_id', $instructorId);
        })
        ->get();
        $subjectTypePercentages = SubjectType::pluck('subject_type')->toArray();


    $additionalSubjectTypes = [
        'Lec',
        'Lab',
    ];

    // combine additional subject types with the fetched subject types from the db table
    $subjectTypes = array_merge($additionalSubjectTypes, $subjectTypePercentages);

      return view('teacher.list.past_classlist', compact('subjects','subjectTypes'));
      }


    public function viewEnrolledStudents($subjectId)
    {
         

      // $assessments = Assessment::all(); 
        $assessments = Assessment::where('subject_id', $subjectId)
            ->orderByRaw("CASE
                WHEN grading_period = 'First Grading' THEN 1
                WHEN grading_period = 'Midterm' THEN 2
                WHEN grading_period = 'Finals' THEN 3
                ELSE 4
            END")
            ->orderByRaw("CASE
                WHEN type = 'Quiz' THEN 1
                WHEN type = 'OtherActivity' THEN 2
                WHEN type = 'Exam' THEN 3
                WHEN type = 'Lab Activity' THEN 4
                WHEN type = 'Lab Exam' THEN 5
                ELSE 6
            END")
            ->get();

      
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
      
         ////// array for exempted types from bieng numbered
        $exemptedTypes = ['Exam', 'Lab Exam','Additional Points Quiz','Additional Points OT','Additional Points Exam','Additional Points Lab','Direct Bonus Grade'];

        ////// keep track of the count for eachtype
        $typeCounts = [];

       //// apply abbreviation to each assessment type
       foreach ($assessments as $assessment) {
        $type = $assessment->type;
        $gradingPeriod = $assessment->grading_period;

        //  checks the array $exemptedTypes for thetype that is exempted
        if (in_array($type, $exemptedTypes)) {
            $assessment->abbreviation = $typeAbbreviations[$type];
        } else {
            ///// satart the count for the current grading period if not set
            $typeCounts[$gradingPeriod][$type] = $typeCounts[$gradingPeriod][$type] ?? 0;

           ///// auto increment the count for the new assessment addedin the current grading period
            $count = ++$typeCounts[$gradingPeriod][$type];

            //////include the increment count in the abbreviation
            $assessment->abbreviation = $typeAbbreviations[$type] . $count;
          }
        }
        
        $typeMapping = [
            'Additional Points Quiz' => 'Quiz',
            'Additional Points OT' => 'OtherActivity',
            'Additional Points Exam' => 'Exam',
            'Additional Points Lab' => 'Lab Activity',
        ];

        //// set the assessments to use the mapped types  $typeMapping
        $assessments = $assessments->map(function ($assessment) use ($typeMapping) {
            $assessment->type = $typeMapping[$assessment->type] ?? $assessment->type;
            return $assessment;
        });
            
      
       $subject = Subject::findOrFail($subjectId);
           $hasAssessment = true;
      
       $currentInstructor = Auth::user();
             
    
       $enrolledStudents = [];

   //lop for getting the list of students enrolled in specific subject taught by current logged-in instructr
       foreach ($subject->importedClasses as $class) {
          if ($class->instructor_id == $currentInstructor->id) {
            foreach ($class->enrolledStudents as $enrolledStudent) {
                $enrolledStudents[] = $enrolledStudent;
            }
        }
    }

    $sortedStudents = collect($enrolledStudents)->groupBy('student.gender');

    $descriptions = AssessmentDescription::pluck('description', 'id');

    $totalMaxPoints = Assessment::select('grading_period', 'type', DB::raw('SUM(max_points) as total_max_points'))
    ->groupBy('grading_period', 'type')
    ->get();

    $students = EnrolledStudents::with(['student', 'grades'])
        ->whereHas('importedClasses', function ($query) use ($subjectId) {
            $query->where('subjects_id', $subjectId);
        })
        ->get();



     return view('teacher.list.studentlist', compact('subject', 'assessments', 'enrolledStudents', 'hasAssessment', 'sortedStudents', 'descriptions', 'totalMaxPoints', 'students'));
     }


       public function viewStudentsRemove($subjectId)
    {
         

       $subject = Subject::findOrFail($subjectId);
        
      
       $currentInstructor = Auth::user();
             
    
       $enrolledStudents = [];

   //lop for getting the list of students enrolled in specific subject taught by current logged-in instructr
       foreach ($subject->importedClasses as $class) {
          if ($class->instructor_id == $currentInstructor->id) {
            foreach ($class->enrolledStudents as $enrolledStudent) {
                $enrolledStudents[] = $enrolledStudent;
            }
        }
    }

    
    $sortedStudents = collect($enrolledStudents)->groupBy('student.gender');

   

     return view('teacher.list.studentlistremove', compact('subject', 'enrolledStudents', 'sortedStudents'));
     }


    public function removeStudent($enrolledStudentId)
    {
         
       $enrolledStudent = EnrolledStudents::find($enrolledStudentId);

        if ($enrolledStudent) {
            $enrolledStudent->delete();
            return redirect()->back()->with('success', 'Student removed successfully.');
        }

        return redirect()->back()->with('error', 'Student not found.');
    }

    public function editAssessments($subjectId)
    {
        $subject = Subject::find($subjectId);
        $assessments = Assessment::where('subject_id', $subjectId)->get();

        return view('teacher.list.edit_assessments', compact('subject', 'assessments'));
    }

    public function editSingleAssessment($assessmentId)
    {
          $assessment = Assessment::find($assessmentId);

        return view('teacher.list.edit_single_assessment', compact('assessment'));
    }

    public function updateAssessment($assessmentId, Request $request)
    {
         
       $assessment = Assessment::find($assessmentId);

        
        $assessment->update([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'max_points' => $request->input('max_points'),
            'activity_date' => $request->input('activity_date'),
        ]);

        
        return redirect()->route('instructor.editAssessments', ['subjectId' => $assessment->subject_id])->with('success', 'Assessment updated successfully');
    }

    public function getAssessmentDescriptions($type)
        {

            $type = $request->input('type');
            $descriptions = AssessmentDescription::where('type', $type)->get();

            return response()->json(['descriptions' => $descriptions]);
        }

    public function updatePublishStatus(Request $request)
{
      $assessmentId = $request->input('assessmentId');
        $isPublished = $request->input('isPublished');

        $assessment = Assessment::findOrFail($assessmentId);

       
        $assessment->update([
            'published' => $isPublished,
             'published_at' => $isPublished ? Carbon::now() : null,
               ]);

        return response()->json(['success' => true, 'message' => 'Publish status updated successfully']);
    }

 public function updatePublishGradesStatus(Request $request)
{
    try {
        $gradingPeriod = $request->input('gradingPeriod');
        $isPublished = $request->input('isPublished');
        $subjectId = $request->input('subjectId');

        $columnToUpdate = 'published';
        if ($gradingPeriod == 'Midterm') {
            $columnToUpdate = 'published_midterms';
        } elseif ($gradingPeriod == 'Finals') {
            $columnToUpdate = 'published_finals';
        }

        $subjectId = ImportedClasslist::findOrFail($subjectId)->subject->id;

    
        $grades = Grades::where('points', null)
            ->where('fg_grade', '!=', null)
            ->where(function ($query) {
                $query->where('midterms_grade', null)->orWhereNotNull('midterms_grade');
            })
            ->where(function ($query) {
                $query->where('finals_grade', null)->orWhereNotNull('finals_grade');
            })
            ->where('assessment_id', null)
            ->whereHas('enrolledStudents', function ($query) use ($subjectId) {
                $query->whereHas('importedclasses', function ($query) use ($subjectId) {
                    $query->where('subjects_id', $subjectId);
                });
            })
            ->update([$columnToUpdate => $isPublished]);

        return response()->json(['success' => true, 'message' => 'Grades updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error updating grades', 'error' => $e->getMessage()], 500);
    }
}

  public function updateStatus(Request $request)
    {
      $gradeId = $request->input('gradeId');
    $status = $request->input('status');
    $gradeType = $request->input('gradeType');

   
    $grade = Grades::find($gradeId);

    if ($grade) {
     
        switch ($gradeType) {
            case 'midterm':
                $grade->midterms_status = $status;
                
                $actualGrade = $grade->midterms_grade;
                break;
            case 'final':
                $grade->finals_status = $status;
                
                $actualGrade = $grade->finals_grade;
                break;
            default:
                $grade->status = $status;
               
                $actualGrade = $grade->fg_grade;
                break;
        }

        $grade->save();

        
        return response()->json(['success' => true, 'actualGrade' => $actualGrade]);
    } else {
        
        return response()->json(['error' => 'grade not found'], 404);
    }
    }


}
