@extends('layouts.app')
   
@section('content')
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Subject List</h1>
          </div>
          <div class="col-sm-6" style="text-align: right;">
            <a href="{{ url('teacher/list/past_classlist')}}" class="btn btn-primary">Past Subjects</a>
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
             
              <!-- /.card-header -->
<div class="card-body p-0">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Description</th>
                <th>Section</th>
                <th>Days</th>
                <th>Time</th>
                <th>Room</th>
                <th>Class Type</th> 
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $subject)
            <tr>
                <td>{{ $subject->subject_code }}</td>
                <td>{{ $subject->description }}</td>
                <td>{{ $subject->section }}</td>
                <td>{{ $subject->importedClasses->first()->days }}</td>
                <td>{{ $subject->importedClasses->first()->time }}</td>
                <td>{{ $subject->importedClasses->first()->room }}</td>
                
                <td>
                    
                    <form action="{{ route('teacher.update.subject.type', ['subject' => $subject]) }}" method="POST">
                            @csrf
                            @method('PUT')

                         <select name="subject_type" class="form-control">
                                @foreach ($subjectTypes as $type)
                                    <option value="{{ $type }}" {{ $subject->subject_type === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-primary mt-1">Update</button>
                        </form>
                </td>
                <td>
                    <a href="{{ route('teacher.list.studentlist', ['subject' => $subject]) }}" class="btn btn-primary mt-2">View Students</a>
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
