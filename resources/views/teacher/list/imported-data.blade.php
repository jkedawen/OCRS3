@extends('layouts.app')
   
  @section('content')
@push('scripts')
  <script>
        $(document).ready(function () {
        
         $('form').submit(function (e) {
            const selectedClassType = $('#subjectType').val();

            if (!selectedClassType) {
               
                e.preventDefault();
                alert('Select a class type.');
            } else {
                $('#subjectTypeHidden').val(selectedClassType);
            }
        });
    });
</script>
@endpush
<style>

       

        h2 {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 15px; 
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px; 
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @include('messages')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Class List Data</h3>
                        </div>
                        
                        <div class="class-info">
                            <table class="class-info-table">
                                <tr>
                                    <th>Term: </th>
                                    <td>{{ $term }}</td>
                                    <th>Days:</th>
                                    <td>{{ $days }}</td>
                                </tr>
                                <tr>
                                    <th>Section:</th>
                                    <td>{{ $section }}</td>
                                    <th>Time:</th>
                                    <td>{{ $time }}</td>
                                </tr>
                                <tr>
                                    <th>Subject Code:</th>
                                    <td>{{ $subjectCode }}</td>
                                    <th>Room:</th>
                                    <td>{{ $room }}</td>
                                </tr>
                                <tr>
                                    <th>Subject Description:</th>
                                    <td colspan="3">{{ $subjectDescription }}</td>
                                </tr>
                            </table>
                        </div>
                       
                        <div class="student-lists">
                            <div class="male-students">
                                <p><b>Male Students:</b></p>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Last Name</th>
                                            <th>Name</th>
                                            <th>Middle Name</th>
                                            <th>Course</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($maleStudentValues as $male_student)
                                            <tr>
                                                <td>{{ $male_student['id_number'] }}</td>
                                                <td>{{ $male_student['last_name'] }}</td>
                                                <td>{{ $male_student['name'] }}</td>
                                                <td>{{ $male_student['middle_name'] }}</td>
                                                <td>{{ $male_student['course'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="female-students">
                                <p><b>Female Students:</b></p>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Last Name</th>
                                            <th>Name</th>
                                            <th>Middle Name</th>
                                            <th>Course</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($femaleStudentValues as $female_student)
                                            <tr>
                                                <td>{{ $female_student['id_number'] }}</td>
                                                <td>{{ $female_student['last_name'] }}</td>
                                                <td>{{ $female_student['name'] }}</td>
                                                <td>{{ $female_student['middle_name'] }}</td>
                                                <td>{{ $female_student['course'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                       <div class="form-row mb-3">
                       <div class="col-md-2">
                        <div class="subject-type-select">
                            <label for="subjectType">Class Type:</label>
                            <select class="form-control" name="subject_type" id="subjectType" required>
                                 <option value="" disabled selected>--- Select Class Type ---</option>
                                @foreach($subjectType as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


<!---------form to save the values to the db -->
  <form action="{{ route('save-data') }}" method="POST">
    @csrf
    <input type="hidden" name="section" value="{{ json_encode($section) }}">
    <input type="hidden" name="subject_code" value="{{ json_encode($subjectCode) }}">
    <input type="hidden" name="description" value="{{ json_encode($subjectDescription) }}">
    <input type="hidden" name="term" value="{{ json_encode($term) }}">
    <input type="hidden" name="days" value="{{ json_encode($days) }}">
    <input type="hidden" name="time" value="{{ json_encode($time) }}">
    <input type="hidden" name="room" value="{{ json_encode($room) }}">
    <input type="hidden" name="subject_type" id="subjectTypeHidden">
    <input type="hidden" name="male_student_values" value="{{ json_encode($maleStudentValues) }}">
    <input type="hidden" name="female_student_values" value="{{ json_encode($femaleStudentValues) }}">
    
     
    <button type="submit" class="btn btn-primary mt-3">Save</button>

  </form>
</div>
</div>
</div>
</div>
</div>
</section>
</div>




@endsection