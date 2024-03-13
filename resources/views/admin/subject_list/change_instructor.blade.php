@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           
          </div>
          
        </div>
      </div>
    </section>


      <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
            <form method="post" action="{{ route('admin.changeInstructor', ['importedClassId' => $importedClass->id]) }}">
                {{ csrf_field() }}
         <div class="card-body">
            <div class="form-group">
                <label for="newInstructor">Assign New Instructor:</label>
                <select class="form-control" name="newInstructor" required>
                       <option value="" disabled selected>--- Select Instructor ---</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}">{{ $instructor->name }} {{ $instructor->middle_name }} {{ $instructor->last_name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Reassign Instructor</button>
            </div>
    </form>
      </div>
         

          </div>
         
        </div>
     
      </div>
    </section>
    
  </div>
@endsection

 