@extends('layouts.app')

@section('content')
   
   
  <div class="content-wrapper">
 
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           
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
                <h3 class="card-title">Enrolled Students List</h3>
              </div>
              <!-- /.card-header -->
  <div class="card-body p-0">
    <table class="table table-striped">
        {{-- Iterate over Male and Female --}}
        @foreach (['Male', 'Female'] as $gender)
            {{-- Display headers for Male and Female --}}
            <thead>
                <tr>
                    <th colspan="3">{{ $gender }}</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Course</th>
                </tr>
            </thead>
            <tbody>
                {{-- Check if the gender exists in the sortedStudents array --}}
                @if (isset($sortedStudents[$gender]))
                    {{-- Display students for the current gender --}}
                    @foreach ($sortedStudents[$gender] as $student)
                        <tr>
                            <td>{{ $student->student->id_number }}</td>
                            <td>{{ $student->student->last_name }}, {{ $student->student->name }} {{ $student->student->middle_name }}</td>
                            <td>{{ $student->student->course }}</td>
                            <td><a class="btn btn-primary" href="{{ route('remove.student', ['enrolledStudentId' => $student->id]) }}" onclick="return confirm('Are you sure you want to remove this student?')">Remove</a></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        @endforeach
    </table>
</div>




            </div>
          </div>
         </div>
                 <div class="container mt-3">
    <a href="{{ route('teacher.list.studentlist', ['subject' => $subject->id]) }}" class="btn btn-primary">Back</a>
</div>
        </div>
         
      
      </section>
       </div>

@endsection

   