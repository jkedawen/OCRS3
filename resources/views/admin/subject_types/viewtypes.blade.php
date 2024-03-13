@extends('layouts.app')

@section('content')

   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
        
          <div class="col-sm-6" style="text-align: right;">
            <a href="{{ route('subject_types.create') }}" class="btn btn-success">Add Class Type</a>
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
                <h3 class="card-title">Subject Type List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Class Type</th>
                <th>Lec Percentage</th>
                <th>Lab Percentage</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjectTypes as $subjectType)
                <tr>
                    <td>{{ $subjectType->id }}</td>
                    <td>{{ $subjectType->subject_type }}</td>
                    <td>{{ $subjectType->lec_percentage }}</td>
                    <td>{{ $subjectType->lab_percentage }}</td>
                    <td>
                        <a href="{{ route('subject_types.edit', $subjectType->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('subject_types.destroy', $subjectType->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
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