<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades List</title>
   <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 50px; 
        }

        h2 {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px; 
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px; 
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .assessment-table {
            width: 100%;
            font-size: 10px; 
        }

        .assessment-table th, .assessment-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        .page-break {
        page-break-after: always;
    }

    </style>
</head>
<body>
  
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

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Student Name</th>
                <th>Course</th>
                <th>First Grading</th>
                <th>Midterms</th>
                <th>Finals</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sortedStudents as $gender => $students)
                <tr>
                    <td colspan="6" class="gender-header">{{ $gender }}</td>
                </tr>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->student->id_number }}</td>
                        <td>{{ $student->student->name }}</td>
                        <td>{{ $student->student->course }}</td>

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


 

</body>
</html>