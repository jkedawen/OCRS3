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
                <h3 class="card-title">Subjects</h3>
              </div>
              <!-- /.card-header -->
           <div class="card-body p-0">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Description</th>
                <th>Section</th>
                <th>Current Instructor</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($importedClasses as $importedClass)
                <tr>
                     <td>{{ $importedClass->subject->subject_code }}</td>
                    <td>{{ $importedClass->subject->description }}</td>
                        <td>{{ $importedClass->subject->section}}</td>
                    <td>{{ $importedClass->instructor->name }} {{ $importedClass->instructor->middle_name }} {{ $importedClass->instructor->last_name }}</td>
                    <td>
                        <a href="{{ route('admin.changeInstructorForm',  ['importedClassId' => $importedClass->id]) }}" class="btn btn-primary">Change Instructor</a>
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