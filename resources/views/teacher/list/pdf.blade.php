<!DOCTYPE html>
<html>
<head>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 5px;
    }

    h2 {
        margin-bottom: 5px; 
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px; 
        font-size: 5px; 
    }

    th, td {
        border: 0.5px solid #ddd; 
        padding: 3px; 
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .assessment-table {
        width: 100%;
        font-size: 5px; 
    }

    .assessment-table th, .assessment-table td {
        border: 0.5px solid #ddd; 
        padding: 3px; 
        text-align: center;
    }

    .page-break {
        page-break-after: always;
    }
</style>
</head>
<body>

    <h2>Subject Details</h2>
    <table>
     <table>
        <tr>
            <th>Subject Code:</th>
            <td>{{ $subject->subject_code }}</td>
        </tr>
        <tr>
            <th>Description:</th>
            <td>{{ $subject->description }}</td>
        </tr>
        <tr>
            <th>Term:</th>
            <td>{{ $subject->term }}</td>
        </tr>

        <tr>
            <th>Instructor:</th>
            <td>{{ $subject->importedClasses->first()->instructor->name }}</td>
        </tr>
    </table>
    </table>
  <h1>Student Record</h1>
@if ($hasFGAssessments || $hasMidtermAssessments || $hasFinalsAssessments)
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>

                @if ($hasFGAssessments)
                    <th colspan="{{ count($assessments) + 1 }}">First Grading</th>
                @endif

                @if ($hasMidtermAssessments)
                    <th colspan="{{ count($midtermAssessments) + 1 }}">Midterms</th>
                @endif

                @if ($hasFinalsAssessments)
                    <th colspan="{{ count($finalsAssessments) + 2 }}">Finals</th>
                @endif
            </tr>

            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Course</th>

                @if ($hasFGAssessments)
                    @foreach ($assessments as $assessment)
                        <th>{{ $assessment->description }}</th>
                    @endforeach
                    <th>FG Grade</th>
                @endif

                @if ($hasMidtermAssessments)
                    @foreach ($midtermAssessments as $midtermAssessment)
                        <th>{{ $midtermAssessment->description }}</th>
                    @endforeach
                    <th>Midterm Grade</th>
                @endif

                @if ($hasFinalsAssessments)
                    @foreach ($finalsAssessments as $finalsAssessment)
                        <th>{{ $finalsAssessment->description }}</th>
                    @endforeach
                    <th>Finals Grade</th>
                @endif
            </tr>

            <tr>
                <th></th>
                <th></th>
                <th></th>

                @if ($hasFGAssessments)
                    @foreach ($assessments as $assessment)
                        <th>{{ $assessment->max_points }}</th>
                    @endforeach
                    <th></th>
                @endif

                @if ($hasMidtermAssessments)
                    @foreach ($midtermAssessments as $midtermAssessment)
                        <th>{{ $midtermAssessment->max_points }}</th>
                    @endforeach
                    <th></th>
                @endif

                @if ($hasFinalsAssessments)
                    @foreach ($finalsAssessments as $finalsAssessment)
                        <th>{{ $finalsAssessment->max_points }}</th>
                    @endforeach
                    <th></th>
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach ($sortedStudents as $gender => $students)
                <tr>
                    <td colspan="{{ count($assessments) + count($midtermAssessments) + count($finalsAssessments) + 3 }}"
                        class="gender-header">{{ $gender }}</td>
                </tr>

                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->student->id_number }}</td>
                        <td>{{ $student->student->name }}</td>
                        <td>{{ $student->student->course }}</td>

                        @if ($hasFGAssessments)
                            @foreach ($assessments as $assessment)
                                <td>{{ $student->getScore($assessment->id) ?: 'A' }}</td>
                            @endforeach
                            <td>{{ $student->grades->avg('fg_grade') }}</td>
                        @endif

                        @if ($hasMidtermAssessments)
                            @foreach ($midtermAssessments as $midtermAssessment)
                                <td>{{ $student->getScore($midtermAssessment->id) ?: 'A' }}</td>
                            @endforeach
                            <td>{{ $student->grades->avg('midterms_grade') }}</td>
                        @endif

                        @if ($hasFinalsAssessments)
                            @foreach ($finalsAssessments as $finalsAssessment)
                                <td>{{ $student->getScore($finalsAssessment->id) ?: 'A' }}</td>
                            @endforeach
                            <td>{{ $student->grades->avg('finals_grade') }}</td>
                        @endif
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <th></th>
                <th></th>
                <th></th>

                @if ($hasFGAssessments)
                    @foreach ($assessments as $assessment)
                        <th>{{ $assessment->activity_date }}</th>
                    @endforeach
                    <th></th>
                @endif

                @if ($hasMidtermAssessments)
                    @foreach ($midtermAssessments as $midtermAssessment)
                        <th>{{ $midtermAssessment->activity_date }}</th>
                    @endforeach
                    <th></th>
                @endif

                @if ($hasFinalsAssessments)
                    @foreach ($finalsAssessments as $finalsAssessment)
                        <th>{{ $finalsAssessment->activity_date }}</th>
                    @endforeach
                    <th></th>
                @endif
            </tr>
        </tbody>
    </table>
@endif

<div class="page-break"></div>
@if ($passingStudents->isNotEmpty() || $failingStudents->isNotEmpty())
    <h2>Passed Students</h2>
    <table>
        <thead>
            <tr>
                 <th>ID</th>
                 <th>Student Name</th>
                 <th>Course</th>
                 <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($passingStudents as $student)
                @if ($student->grades->isNotEmpty() && $student->grades->avg('finals_grade') !== null)
                    <tr>
                        <td>{{ $student->student->id_number }}</td>
                        <td>{{ $student->student->name }}</td>
                        <td>{{ $student->student->course }}</td>
                        <td>{{ $student->grades->avg('finals_grade') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <h2>Failed Students</h2>
    <table>
        <thead>
            <tr>
                 <th>ID</th>
                 <th>Student Name</th>
                 <th>Course</th>
                 <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($failingStudents as $student)
                @if ($student->grades->isNotEmpty() && $student->grades->avg('finals_grade') !== null)
                    <tr>
                        <td>{{ $student->student->id_number }}</td>
                        <td>{{ $student->student->name }}</td>
                        <td>{{ $student->student->course }}</td>
                        <td>{{ $student->grades->avg('finals_grade') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endif
</body>
</html>