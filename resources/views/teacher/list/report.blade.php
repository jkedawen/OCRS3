
@extends('layouts.app')

@section('content')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
     

        </div>
      </div><!-- /.container-fluid -->
    </section>

<div class="container">
    <h2>Grades</h2>

    <div class="row mt-3">
        <div class="col-md-2">
            <label for="passPercentage">Pass Percentage:</label>
            <input type="text" id="passPercentage" class="form-control" value="{{ $passPercentage }}%" readonly>
        
            <label for="failPercentage">Fail Percentage:</label>
            <input type="text" id="failPercentage" class="form-control" value="{{ $failPercentage }}%" readonly>

         
        </div>
    </div>
   
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>First Grading</th>
                <th>Midterms</th>
                <th>Finals</th>
            </tr>
        </thead>
        <tbody>
    @foreach ($sortedStudents as $gender => $students)
        <tr>
            <td colspan="4" class="gender-header">{{ $gender }}</td>
        </tr>

        @foreach ($students as $student)
            <tr>
                <td>{{ $student->student->id_number }} - {{ $student->student->name }}</td>
                <td>
                    @foreach ($student->grades as $grade)
                        @if ($grade->fg_grade !== null)
                            {{ $grade->fg_grade }}
                        @endif
                    @endforeach
                </td>
                <td>
                    @foreach ($student->grades as $grade)
                        @if ($grade->midterms_grade !== null)
                            {{ $grade->midterms_grade }}
                        @endif
                    @endforeach
                </td>
                <td>
                    @foreach ($student->grades as $grade)
                        @if ($grade->finals_grade !== null)
                            {{ $grade->finals_grade }}
                        @endif
                    @endforeach
                </td>
            </tr>
        @endforeach
    @endforeach
</tbody>

    </table>
   
   <a href="{{ route('report.generatePdf', $subjectId) }}" class="btn btn-primary mt-3">Download Records Report</a>
    <a href="{{ route('report.generateGradesList', $subjectId) }}" class="btn btn-primary mt-3">Download Grades List</a>
 
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