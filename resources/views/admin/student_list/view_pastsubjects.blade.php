@extends('layouts.app')

@section('content')
    

   <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
         

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
                <h3 class="card-title">Past Semester Subjects of {{ $student->last_name }}, {{ $student->name }} {{ $student->middle_name }}</h3>
              </div>
              <!-- /.card-header -->
           <div class="card-body p-0">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Description</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pastEnrolledSubjects as $subject)
                <tr>
                    <td>{{ $subject->subject_code }}</td>
                    <td>{{ $subject->description }}</td>
                    <td>{{ $subject->section }}</td>
                    <td>
                       <a href="{{ route('admin.viewGrades', ['studentId' => $student->id, 'subjectId' => $subject->id]) }}" class="btn btn-primary">View Scores and Grades</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    </div>

            </div>
          </div>
         </div>
                 <!-- /.card-body -->
        </div>
            <!-- /.card -->
  
          <!-- /.col -->
      
      </section>
       </div>
@endsection