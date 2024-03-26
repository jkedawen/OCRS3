<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
//use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
//use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CalculateNumberController;
use App\Http\Controllers\ClassRecordController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentSubjectsController;
use App\Http\Controllers\StudentScoreController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectTypeController;
use App\Http\Controllers\AssessmentDescriptionController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\SemesterController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', [AuthController::class, 'login']);
Route::post('login', [AuthController::class, 'AuthLogin']);
Route::get('logout', [AuthController::class, 'logout']);




//admin side
Route::group(['middleware' => 'admin'], function () {
    
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('admin/admin/list', [AdminController::class, 'list']);
    Route::get('admin/admin/add', [AdminController::class, 'add']);
    Route::post('admin/admin/add', [AdminController::class, 'insert']);
    Route::get('admin/admin/edit/{id}', [AdminController::class, 'edit']);
    Route::post('admin/admin/edit/{id}', [AdminController::class, 'update']);
    Route::get('admin/admin/delete/{id}', [AdminController::class, 'delete']);

  /////password auth for editing
    Route::get('admin/admin/confirm-password/{id}', [AdminController::class, 'showPasswordConfirmation'])
    ->name('admin.confirm-password')
    ->middleware('auth'); 
    Route::post('admin/admin/confirm-password/{id}', [AdminController::class, 'confirmPassword'])
    ->middleware('auth');
    Route::get('admin/admin/edit/{id}', [AdminController::class, 'edit'])
    ->name('admin.edit')
    ->middleware('auth'); 
   
    Route::get('admin/subject_types/viewtypes', [SubjectTypeController::class, 'viewTypes']);
    Route::get('admin/subject_types/createtypes', [SubjectTypeController::class, 'create'])->name('subject_types.create');
    Route::post('admin/subject_types/createtypes', [SubjectTypeController::class, 'store'])->name('subject_types.store');
    Route::get('admin/subject_types/edittypes/{id}', [SubjectTypeController::class, 'edit'])->name('subject_types.edit');
    Route::put('admin/subject_types/edittypes/{id}', [SubjectTypeController::class, 'update'])->name('subject_types.update');
    Route::delete('admin/subject_types/{id}', [SubjectTypeController::class, 'destroy'])->name('subject_types.destroy');


    Route::get('admin/assessment_description/view_desc', [AssessmentDescriptionController::class, 'viewDesc']);
    Route::get('assessment-descriptions/create', [AssessmentDescriptionController::class, 'create'])->name('assessment-descriptions.create');
    Route::post('admin/assessment_description/view_desc', [AssessmentDescriptionController::class, 'store'])->name('assessment-descriptions.store');
    Route::get('assessment-descriptions/{assessment_description}/edit', [AssessmentDescriptionController::class, 'edit'])->name('assessment-descriptions.edit');
    Route::put('assessment-descriptions/{assessment_description}', [AssessmentDescriptionController::class, 'update'])->name('assessment-descriptions.update');
    Route::delete('assessment-descriptions/{assessment_description}', [AssessmentDescriptionController::class, 'destroy'])->name('assessment-descriptions.destroy');
    

    Route::get('admin/set_semester/view_semesters', [SemesterController::class, 'viewSemester'])->name('semesters.view_semesters');  
    Route::get('admin/semesters/create', [SemesterController::class, 'create'])->name('semesters.create');
    Route::post('admin/set_semester/view_semesters', [SemesterController::class, 'store'])->name('semesters.store');
    Route::get('admin/semesters/{id}/edit', [SemesterController::class, 'edit'])->name('semesters.edit');
    Route::put('admin/semesters/{id}', [SemesterController::class, 'update'])->name('semesters.update');
    Route::delete('admin/semesters/{id}', [SemesterController::class, 'destroy'])->name('semesters.destroy');

    Route::get('admin/set_semester/set_current', [SemesterController::class, 'setupCurrentSemesterView'])->name('semesters.setupCurrentView');
    Route::post('admin/set-current-semester', [SemesterController::class, 'setupCurrentSemester'])->name('semesters.setupCurrent');
       
    Route::get('/admin/student_list/view_students', [AdminController::class, 'viewAllStudents'])->name('admin.viewAllStudents');
    Route::get('/admin/student_list/view-enrolled-subjects/{studentId}', [AdminController::class, 'viewEnrolledSubjects'])->name('admin.viewEnrolledSubjects');
     Route::get('/admin/student_list/view-past-enrolled-subjects/{studentId}', [AdminController::class, 'viewPastEnrolledSubjects'])->name('admin.viewPastEnrolledSubjects');
    Route::get('/admin/student_list/view-grades/{studentId}/{subjectId}', [AdminController::class, 'viewGrades'])->name('admin.viewGrades');


    Route::get('/admin/subject_list/view_subjects',  [AdminController::class, 'viewSubjects'])->name('admin.viewSubjects');
    Route::get('/admin/subject_list/changeInstructor/{importedClassId}',[AdminController::class, 'changeInstructorForm'])->name('admin.changeInstructorForm');
    Route::post('/admin/subject_list/changeInstructor/{importedClassId}', [AdminController::class, 'changeInstructor'])->name('admin.changeInstructor');

    });
//teacher side
Route::group(['middleware' => 'instructor'], function () {

    Route::get('teacher/dashboard', [DashboardController::class, 'dashboard']);
 
    Route::get('teacher/list/importexcel', function () {
    return view('teacher.list.importexcel');
 
});
    //Route::get('teacher/list/imported-data', function () {
   // return view('teacher.list.imported-data');
//});
    Route::post('teacher/list/importexcel', [ClassRecordController::class, 'import'])->name('teacher.list.importexcel');
    Route::post('teacher/list/imported-data', [ClassRecordController::class, 'import'])->name('teacher.list.imported-data');
    Route::post('save-data', [ClassRecordController::class, 'savedataexcel'])->name('save-data');
     //Route::post('/import-excel', [ClassRecordController::class, 'import'])->name('import.excel');
  //  Route::get('/imported-data', function () {
  // return view('imported-data');
//});
   // Route::get('teacher/scores/scores', function () {
    //return view('teacher.scores.scores');
//});
    //////for showing the enrolld students 
    Route::get('teacher/list/classlist', [InstructorController::class, 'listSubjects'])->name('teacher.list.classlist');
    Route::get('teacher/list/past_classlist', [InstructorController::class, 'pastlistSubjects'])->name('teacher.list.past_classlist');
    Route::get('teacher/list/studentlist/{subject}', [InstructorController::class, 'viewEnrolledStudents'])->name('teacher.list.studentlist');
    Route::put('/teacher/list/classlist/{subject}/update-type', [SubjectController::class, 'updateSubjectType'])
    ->name('teacher.update.subject.type');
   // Route::get('teacher/list/classlist/{subject}/studentlist', [StudentController::class, 'studentsBySubject'])->name('teacher.list.studentlist');

    
  ////////saving the set assessment////
  
   Route::post('/assessments', [ScoreController::class, 'saveAssessment'])->name('assessments.store');
   Route::get('/assessments/fetch', [ScoreController::class, 'fetchAssessments'])->name('assessments.fetch');
   Route::put('/assessments/update', [ScoreController::class, 'updateAssessments'])->name('assessments.update');

   Route::get('/assessments/add', [ScoreController::class, 'showAddAssessmentForm'])->name('assessments.add');
    //////for insertinf the scoress(WIP)
   
   Route::get('fetch/assessment/details/{enrolledStudentId}', [ScoreController::class, 'fetchassessmentDetails'])
    ->name('fetch.assessment.details');


    Route::get('/assessments/{subjectId}', [InstructorController::class, 'editAssessments'])->name('instructor.editAssessments');
    Route::get('/assessments/{assessmentId}/edit', [InstructorController::class, 'editSingleAssessment'])->name('instructor.editSingleAssessment');
    Route::put('assessments/{assessmentId}/update', [InstructorController::class, 'updateAssessment'])->name('instructor.updateAssessment');

   Route::post('insert/score/{enrolledStudentId}', [ScoreController::class, 'insertScore'])->name('insert.score');
   Route::post('insert/scores', [ScoreController::class, 'insertScore'])->name('insert.scores');
  //Route::get('update/score/{enrolledStudentId}', [ScoreController::class, 'updateScore'])->name('update.score');
   Route::put('update/score/{enrolledStudentId}', [ScoreController::class, 'updateScore'])->name('update.score');

   Route::get('/report/{subjectId}', [ReportController::class, 'index'])->name('report.index');
   Route::get('/studentlistremove/{subjectId}', [InstructorController::class, 'viewStudentsRemove'])->name('teacher.list.studentlistremove');
   Route::get('/remove-student/{enrolledStudentId}', [InstructorController::class, 'removeStudent'])->name('remove.student');

  ////Route::get('/test-transmutation', [TestController::class, 'testTransmutation']);

  ///(WIP)rute for getting the score values based from grading period
   Route::get('get-scores', [ScoreController::class, 'getScores'])->name('get.scores');
 //   Route::get('teacher/scores/scores', [CalculateNumberController::class, 'index'])->name('teacher.scores.scores');
   // Route::post('/calculate', [CalculateNumberController::class, 'calculate'])->name('calculate');
    //for updating the numbers(scores)
   // Route::get('teacher/scores/scores/{id}/edit', [NumberController::class, 'edit'])->name('teacher.scores.scores.edit');
  //  Route::put('teacher/scores/scores/{id}', [NumberController::class, 'update'])->name('teacher.scores.scores.update');
   Route::get('/report/{subjectId}/generate-pdf', [ReportController::class, 'generatePdf'])->name('report.generatePdf');
   Route::get('/report/generateGradesList/{subjectId}', [ReportController::class, 'generateGradesList'])->name('report.generateGradesList');

    Route::get('/generate-excel/{subjectId}', [ReportController::class, 'generateExcelReport'])->name('generateExcelReport');
   Route::get('/export-grades/{subjectId}', [ReportController::class, 'exportGradesList'])
      ->name('export.gradeslist');
   Route::get('/generate-summary-report/{subjectId}', [ReportController::class, 'generateSummaryReport'])->name('export.summary');

   Route::delete('/delete-student/{enrolledStudentId}', [InstructorController::class, 'deleteStudent'])->name('delete.student');
   Route::get('/assessment-descriptions/fetch', [AssessmentDescriptionController::class, 'fetch'])->name('assessment-descriptions.fetch');

   Route::post('/update-publish-status',  [InstructorController::class,'updatePublishStatus'])->name('update.publish.status');
   Route::post('/update-publish-grades-status', [InstructorController::class,'updatePublishGradesStatus'])->name('update.publish.grades.status');

   Route::post('/update-grade-status',  [InstructorController::class,'updateStatus'])->name('update.grade.status');
  
});

//Route::get('/assessment-descriptions/{type}', [AssessmentDescriptionController::class, 'getDescriptionsByType'])
   // ->name('assessment-descriptions.type');
 //Route::get('/assessment-descriptions/{type}', [InstructorController::class, 'getDescriptionsByType'])
   // ->name('assessment-descriptions.type');

//student side
Route::group(['middleware' => 'student'], function () {
    
    Route::get('student/dashboard', [DashboardController::class, 'dashboard']);
   Route::get('student/subjectlist/{studentId}', [StudentSubjectsController::class, 'studentsubjects'])
    ->name('student.subjectlist');
    Route::get('student/past_subjectlist/{studentId}', [StudentSubjectsController::class, 'studentpastsubjects'])
    ->name('student.studentpastsubjects');
   Route::get('student/scores/showscores/{enrolledStudentId}', [StudentScoreController::class, 'showscores'])->name('student.scores.showscores');
   Route::get('/student/notifications', [StudentScoreController::class, 'showNotifications'])->name('student.notifications');
Route::post('/student/mark-notifications-as-read', [StudentScoreController::class, 'markNotificationsAsRead'])->name('student.markNotificationsAsRead');
});



Route::group(['middleware' => 'secretary'], function () {
    
    Route::get('secretary/dashboard', [DashboardController::class, 'dashboard']);
   Route::get('/secretary/teacher_list/instructor_list', [SecretaryController::class, 'showInstructors']);
       Route::get('/secretary/teacher_list/{instructorId}/subjects', [SecretaryController::class, 'showInstructorSubjects'])
    ->name('secretary.teacher_list.subjects');

    Route::get('/secretary/teacher_list/{subject}/students', [SecretaryController::class, 'showEnrolledStudents'])->name('secretary.teacher_list.enrolled_students');

    Route::get('/view-student-points/{studentId}/{subjectId}', [SecretaryController::class, 'viewStudentPoints'])->name('view.student.points');

      Route::get('secretary/subject_types/viewtypes', [SubjectTypeController::class, 'viewTypes1']);
    Route::get('secretary/subject_types/createtypes', [SubjectTypeController::class, 'create1'])->name('subject_types.create');
    Route::post('secretary/subject_types/createtypes', [SubjectTypeController::class, 'store1'])->name('subject_types.store');
    Route::get('secretary/subject_types/edittypes/{id}', [SubjectTypeController::class, 'edit1'])->name('subject_types.edit');
    Route::put('secretary/subject_types/edittypes/{id}', [SubjectTypeController::class, 'update1'])->name('subject_types.update');
    Route::delete('secretary/subject_types/{id}', [SubjectTypeController::class, 'destroy1'])->name('subject_types.destroy');


    Route::get('secretary/set_semester/view_semesters', [SemesterController::class, 'viewSemester1'])->name('semesters.view_semesters');  
    Route::get('/semesters/create', [SemesterController::class, 'create1'])->name('semesters.create1');
    Route::post('secretary/set_semester/view_semesters', [SemesterController::class, 'store1'])->name('semesters.store1');
    Route::get('/semesters/{id}/edit', [SemesterController::class, 'edit1'])->name('semesters.edit1');
    Route::put('/semesters/{id}', [SemesterController::class, 'update1'])->name('semesters.update1');
    Route::delete('/semesters/{id}', [SemesterController::class, 'destroy1'])->name('semesters.destroy1');

    Route::get('secretary/set_semester/set_current', [SemesterController::class, 'setupCurrentSemesterView1'])->name('semesters.setupCurrentView');
    Route::post('/set-current-semester', [SemesterController::class, 'setupCurrentSemester1'])->name('semesters.setupCurrent1');
    });



/// Route::get('/file-import',[UserController::class,
       ///     'importView'])->name('import-view');
 ///Route::post('/import',[UserController::class,
        //    'import'])->name('import');
/// Route::get('/export',[UserController::class,
      ///      'export'])->name('export');