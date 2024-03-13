 @extends('layouts.app')
   
@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
             <div class="col-sm-6" style="text-align: right;">
            <a href="{{ url('student/past_subjectlist/{studentId}')}}" class="btn btn-primary">Past Subjects</a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
  <section class="content">
     <div class="container-fluid">
      <div class="row">

       <div class="col-md-12"> 
        @include('messages')
         <div class="card">
            <div class="card-header">
                        <h3 class="card-title">Enrolled Subjects</h3>
                    </div>
          @if ($enrolledStudentSubjects->count() > 0)
            <div class="card-body p-0">
                <table class="table table-striped">
                   <thead>
                        <tr>
                             <th>Subject Code</th>
                            <th>Subject Description</th>
                            <th>Instructor</th>
                             <th>Days</th>
                             <th>Time</th>
                            <th>Room</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($enrolledStudentSubjects as $enrolledSubject)
            <tr>
                <td>{{ $enrolledSubject->importedclasses->subject->subject_code }}</td>
                 <td>{{ $enrolledSubject->importedclasses->subject->description }}</td>
                  <td>{{ $enrolledSubject->importedclasses->instructor->name }} {{ $enrolledSubject->importedclasses->instructor->last_name }}</td>
                 <td>{{ $enrolledSubject->importedclasses->days }}</td>
                    <td>{{ $enrolledSubject->importedclasses->time }}</td>
                  <td>{{ $enrolledSubject->importedclasses->room }}</td>
                <td>
                     <a href="{{ route('student.scores.showscores', ['enrolledStudentId' => $enrolledSubject->id]) }}">View Scores</a>
                </td>
            </tr>
            @endforeach
                    </tbody>
                </table>
            </div>

              @else
                <p>currently not enrolled in any subjects.</p>
                 @endif
            </div>
          </div>
         </div>
                 <!-- /.card-body -->
        </div>
            <!-- /.card -->
  
          <!-- /.col -->
      
      </section>
       </div>

    <!-- /.content -->
 
  <!-- /.content-wrapper -->

@endsection

 