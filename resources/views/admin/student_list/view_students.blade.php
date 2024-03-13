@extends('layouts.app')

@section('content')
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           
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
                <h3 class="card-title">Student List</h3>
              </div>
              <!-- /.card-header -->
           <div class="card-body p-0">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Last Name</th>
                <th>Name</th>
                <th>Middle Name</th>
                <th>Action</th>
           
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->id_number }}</td>
                    <td>{{ $student->last_name }}</td>  
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->middle_name }}</td>
                    <td> <a href="{{ route('admin.viewEnrolledSubjects', ['studentId' => $student->id]) }}" class="btn btn-primary">View Enrolled Subjects</a>
                    <a href="{{ route('admin.viewPastEnrolledSubjects', ['studentId' => $student->id]) }}" class="btn btn-primary">View Past Semester Subjects</a></td>
                    
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