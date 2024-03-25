   
@extends('layouts.app')

@section('content')


@push('scripts')
<script>
 
$(document).ready(function () {

    let assessmentCounter = 0;
    let savedAssessments = null; 

    let assessments = [];
 
    let newAssessments = [];

   


    $('#assessmentModal').on('shown.bs.modal', function () {
        $('#gradingPeriod, #assessmentType').trigger('change');
    });
  

    $('#addAssessmentFieldsBtn').click(function () {
        addAssessmentFields(true);
    });
    
    
    function fetchAssessments(gradingPeriod, type) {
    
       const filteredAssessments = assessments.filter(
        assessment => assessment.grading_period === gradingPeriod && assessment.type === type
    );


    updateAssessmentFields(filteredAssessments);
    }

   
    function addAssessmentFields(isNew) {
     
        const assessmentCount = $('.assessment-container').length + 1;
        const assessmentField = `
            <div class="mb-3 assessment-container" data-is-new="${isNew}">
                <label for="assessmentType${assessmentCount}">Type</label>
                <select class="form-control" name="type" disabled>
                    <option value="${$('#assessmentType').val()}">${$('#assessmentType').val()}</option>
                </select>
                <label for="assessmentDescription${assessmentCount}">Description</label>
                  <select name="description" id="assessmentDescription" class="form-control" required>
                
            </select>
                <label for="assessmentMaxPoints${assessmentCount}">Max Points</label>
                <input type="number" class="form-control" name="max_points" value="">
                <label for="assessmentActivityDate${assessmentCount}">Activity Date</label>
                <input type="date" class="form-control" name="activity_date" value="">
            </div>
        `;

        
        $('#assessmentFieldsContainer').append(assessmentField);

     
    const selectedGradingPeriod = $('#gradingPeriod').val();
    const selectedType = $('#assessmentType').val();

    $.ajax({
        type: 'GET',
        url: '{{ route('assessment-descriptions.fetch') }}',
        data: {
            grading_period: selectedGradingPeriod,
            type: selectedType,
        },
        success: function (response) {
            const descriptions = response.descriptions;
            updateDescriptionDropdown(descriptions, `#assessmentFieldsContainer .assessment-container:last-child select[name="description"]`);
        },
        error: function (error) {
            console.error('Error:', error);
        },
    });

    assessmentCounter++;
}

function updateDescriptionDropdown(descriptions, targetSelector) {
    const $descriptionDropdown = $(targetSelector);
    $descriptionDropdown.empty();  // Clear existing options

    descriptions.forEach(description => {
        $descriptionDropdown.append($('<option>', {
            value: description.description,
            text: description.description,
        }));
    });
}

    function resetIsNewFlag() {
    $('.assessment-container').each(function () {
        $(this).data('isNew', false);
    });
    }

    
    function populateAssessmentFields(assessments) {
   
        assessments.forEach(function (assessment, index) {
            const assessmentField = `
                <div class="mb-3 assessment-container">
                    <label for="assessmentType${index}">Type</label>
                     <select class="form-control" name="type" >
                        <option value="${assessment.type}">${assessment.type}</option>
                    </select>
                    <label for="assessmentDescription${index}">Description</label>
                    <input type="text" class="form-control" name="description" value="${assessment.description}">
                    <label for="assessmentMaxPoints${index}">Max Points</label>
                    <input type="number" class="form-control assessmentMaxPoints" id="assessmentMaxPoints${index}" name="assessment_max_points[]" value="${assessment.maxPoints}">
                    <label for="assessmentActivityDate${index}">Activity Date</label>
                    <input type="date" class="form-control" name="activity_date" value="${assessment.activity_date}">
         
                </div>
            `;

            $('#assessmentFieldsContainer').append(assessmentField);
        });
    }

   
  $('#assessmentModalButton').click(function () {

      
        $('#assessmentModal').modal('show');
        $('#gradingPeriod, #assessmentType').change(function () {
        const selectedGradingPeriod = $('#gradingPeriod').val();
        const selectedType = $('#assessmentType').val();


        $.ajax({
            type: 'GET',
            url: '{{ route('assessments.fetch') }}',
            data: {
                grading_period: selectedGradingPeriod,
                type: selectedType,
                 subject_id: $('#subject_id').val(),
                subject_type: $('#subject_type').val(),

            },
            success: function (response) {
                const assessments = response.assessments;
                updateAssessmentFields(assessments);
            },
            error: function (error) {
                console.error('Error:', error);
            },
        });
    });
   });
    
    function updateAssessmentFields(assessments) {
        const assessmentFields = assessments.map((assessment, index) => {
            return `
                <div class="mb-3 assessment-container">
                    <label for="assessmentType${index}">Type</label>
                    <select class="form-control" name="type" disabled>
                        <option value="${assessment.type}">${assessment.type}</option>
                    </select>
                    <label for="assessmentDescription${index}">Description</label>
                    <input type="text" class="form-control" name="description" value="${assessment.description}">
                    <label for="assessmentMaxPoints${index}">Max Points</label>
                   <input type="number" class="form-control assessmentMaxPoints" id="assessmentMaxPoints${index}" name="assessment_max_points[]" value="${assessment.maxPoints}">
                   <label for="assessmentActivityDate${index}">Activity Date</label>
                    <input type="date" class="form-control" name="activity_date" value="${assessment.activity_date}">
         
                </div>
            `;
        });

       
        $('#assessmentFieldsContainer').empty().append(assessmentFields);
    }
  $('#saveAssessmentsBtn').click(function () {
   
    const assessments = [];
    $('.assessment-container').each(function (index) {
        const isNew = $(this).data('isNew');
        const gradingPeriod = $('#gradingPeriod').val();
        const type = $(this).find('.form-control[name="type"]').val();
        const description = $(this).find('.form-control[name="description"]').val();
        const max_points = $(this).find('.form-control[name="max_points"]').val();
        const activity_date = $(this).find('.form-control[name="activity_date"]').val();

        assessments.push({ isNew, grading_period: gradingPeriod, type, description, max_points, activity_date });
    });

    console.log('Assessments to save:', assessments);

   
    const newAssessmentsToSave = assessments.filter(assessment => assessment.isNew);

   
    $.ajax({
        type: 'POST',
        url: '{{ route('assessments.store') }}',
        data: {
            _token: $('input[name="_token"]').val(),
            assessments: JSON.stringify(assessments),
            subject_id: $('#subject_id').val(),
            subject_type: $('#subject_type').val(),
        },
        success: function (response) {
         
            $('#assessmentModal').modal('hide');
            location.reload(); 
        },
        error: function (error) {
            console.error('Error:', error);
        },
    });
});

});
</script>



<script> 
$(document).ready(function () {
   console.log('Score Modal script is running.');
   $('.score-button').on('click', function (event) {
    const enrolledStudentId = $(this).data('enrolled-student-id');
    const modal = $(`#scoreModal-${enrolledStudentId}`);

    let isSaving = false; 

    
    $('.save-score').on('click', function (event) {
        event.preventDefault();

        if (isSaving) {
            return; 
        }

        isSaving = true;

        //console.log('save button clicked');

        const assessmentId = modal.find('#assessment').val();
        const points = modal.find('#points').val();

    
        const url = `{{ route('insert.score', '') }}/${enrolledStudentId}`;

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                _token: '{{ csrf_token() }}',
                enrolledStudentId: enrolledStudentId,
                assessmentId: assessmentId,
                points: points
            },
            success: function (response) {
                console.log('Score saved successfully');
                modal.modal('hide');
                isSaving = false; 
                 location.reload(); 
            },
            error: function (error) {
                console.error('Error:', error);
                isSaving = false; 
            }
        });
    });
});
 });
</script>

    <script>
    function deleteStudent(enrolledStudentId) {
        if (confirm('Are you sure you want to delete this student?')) {
          
            var form = document.createElement('form');
            form.action = "{{ url('delete-student') }}" + '/' + enrolledStudentId;
            form.method = 'post';
            form.style.display = 'none';

         
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            form.appendChild(csrfToken);

      
            var methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>






<script>
    $(document).ready(function () {
        
        function updateAssessmentTypeOptions() {
            var subjectType = $('#subject_type').val();
            var assessmentTypeDropdown = $('#assessmentType');

           
            assessmentTypeDropdown.empty();

            
            var options = [];
            if (subjectType === 'Lec') {
                options = ['Quiz', 'OtherActivity', 'Exam', 'Additional Points Quiz', 'Additional Points OT', 'Additional Points Exam', 'Direct Bonus Grade'];
            } else if (subjectType === 'Lab') {
                options = ['Lab Activity', 'Lab Exam', 'Additional Points Lab', 'Direct Bonus Grade'];
            } else if (subjectType === 'LecLab6040' || subjectType === 'LecLab4060' || subjectType === 'LecLab5050') {
                
                options = ['Quiz', 'OtherActivity', 'Exam', 'Lab Activity', 'Lab Exam', 'Additional Points Quiz', 'Additional Points OT', 'Additional Points Exam', 'Additional Points Lab', 'Direct Bonus Grade'];
            }

            
            for (var i = 0; i < options.length; i++) {
                assessmentTypeDropdown.append('<option value="' + options[i] + '">' + options[i] + '</option>');
            }
        }

   
        updateAssessmentTypeOptions();

        
        $('#subject_type').change(updateAssessmentTypeOptions);
    });
</script>

<script>
 $(document).ready(function () {
   $('.assessment-description').popover({
        trigger: 'hover',
        placement: 'top', 
        container: 'body', 
        html: true,
        content: function () {
            return $(this).data('description');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
   document.querySelectorAll('.btn-publish').forEach(function (btn) {
    btn.addEventListener('click', function (event) {
        event.preventDefault();

        var assessmentId = this.getAttribute('data-assessment-id');
        var assessmentDescription = findClosestAssessmentDescription(this);

        if (!assessmentDescription) {
            console.error('Error: Associated assessment description not found.');
            return;
        }

        console.log('Selected button:', this);
        console.log('Parent TH:', assessmentDescription.closest('th'));
        console.log('Associated assessment description:', assessmentDescription);

        var isPublished = this.getAttribute('data-published') === 'true';

        var confirmPublish = confirm('Do you want to ' + (isPublished ? 'hide' : 'show') + ' scores for this assessment to the students?');
        if (confirmPublish) {
            
                fetch('{{ route("update.publish.status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ assessmentId: assessmentId, isPublished: !isPublished })
                })
                .then(response => response.json())
                .then(data => {
                  
                    console.log('AJAX Response:', data);

                    
                    if (data.success) {
                        btn.innerText = isPublished ? 'Show Scores' : 'Hide Scores';
                        btn.setAttribute('data-published', isPublished ? 'false' : 'true');
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);

                    
                    console.log('Assessment ID:', assessmentId);
                    console.log('Is Published:', !isPublished);
                });
            }
        });
    });

   function findClosestAssessmentDescription(element) {
   
    var parent = element.closest('.assessment-column');

    if (parent) {
        
        return parent.querySelector('.assessment-description');
    }

    return null;
}
});


</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-publish-grades').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();

            var gradingPeriod = this.getAttribute('data-grading-period');
            var subjectId = this.getAttribute('data-subject-id');
            console.log('Clicked the Publish Grades button for grading period:', gradingPeriod);
            console.log('Subject ID:', subjectId);

            var isPublished = this.getAttribute('data-published') === 'true';
            var confirmMessage = 'Do you want to ' + (isPublished ? 'hide' : 'publish') + ' grades for ' + gradingPeriod + '?';
            var confirmPublish = confirm(confirmMessage);

            if (confirmPublish) {
                fetch('{{ route("update.publish.grades.status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        gradingPeriod: gradingPeriod,
                        isPublished: !isPublished,
                        subjectId: subjectId
                    })
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response:', data);

                        btn.innerText = isPublished ? 'Publish Grades' : 'Hide Grades';
                        btn.setAttribute('data-published', isPublished ? 'false' : 'true');

                        localStorage.setItem('publishedState_' + gradingPeriod + '_' + subjectId, !isPublished);

                        console.log('Grades for ' + gradingPeriod + ' ' + (isPublished ? 'hidden' : 'published') + ' successfully.');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    });

    document.querySelectorAll('.btn-publish-grades').forEach(function (btn) {
        var gradingPeriod = btn.getAttribute('data-grading-period');
        var subjectId = btn.getAttribute('data-subject-id');
        var isPublished = localStorage.getItem('publishedState_' + gradingPeriod + '_' + subjectId) === 'true';

        btn.innerText = isPublished ? 'Hide Grades' : 'Publish Grades';
        btn.setAttribute('data-published', isPublished ? 'true' : 'false');
    });
});
</script>

<script>
$(document).ready(function() {
    function updateDisplayedValue(dropdown, actualGrade, selectedStatus) {
       
        var displayedValue;
        switch (selectedStatus) {
            case 'DEFAULT':
                displayedValue = actualGrade;
                break;
            case 'DRP':
                displayedValue = 'DRP';
                break;
            case 'WITHDRAW':
                displayedValue = 'Withdraw';
                break;
            case 'INC':
                displayedValue = 'INC';
                break;
            default:
                displayedValue = actualGrade;
                break;
        }

     
        dropdown.closest('.grade-dropdown').find('.displayed-value').text(displayedValue);
    }

    $('.status-dropdown').change(function() {
        var gradeId = $(this).data('grade-id');
        var selectedStatus = $(this).val();
        var gradeType = $(this).data('grade-type');

      
        var dropdown = $(this);

       
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

       
        $.ajax({
            url: '{{ route('update.grade.status') }}',
            method: 'POST',
            data: {
                gradeId: gradeId,
                status: selectedStatus,
                gradeType: gradeType,
                _token: csrfToken
            },
            success: function(response) {
                  console.log('Response:', response);
               
                updateDisplayedValue(dropdown, response.actualGrade, selectedStatus);
            },
            error: function(error) {
                console.error('Error updating grade status:', error);
            }
        });
    });

    
    $('.status-dropdown').each(function() {
        var dropdown = $(this);
        var actualGrade = dropdown.closest('.grade-dropdown').find('.displayed-value').text();
        var selectedStatus = dropdown.val();
        updateDisplayedValue(dropdown, actualGrade, selectedStatus);
    });
});
</script>

<script>
    $(document).ready(function() {
        $('.assessment-column input[type="text"]').on('input', function() {
            var value = $(this).val();
            var containsLetters = /[a-zA-Z]/.test(value);
            var containsNumbers = /\d/.test(value);
            
            if (containsLetters && containsNumbers) {
                $(this).val('');    
                alert('Enter numbers only or letters only.');
            }
        });
    });
</script>

@endpush




    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          
      

        </div>
      </div><!-- /.container-fluid -->
    </section>




<section class="content">
    <div class="container-fluid">
        <div class="row">
           <div class="col-md-12">
                @php
                    $studentCount = count($enrolledStudents);
                @endphp

                @include('messages')

                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Subject:</strong> {{ $subject->subject_code }}</p>
                                    <p><strong>Description:</strong> {{ $subject->description }}</p>
                                     <p><strong>Section:</strong> {{ $subject->section }}</p>
                                    <p><strong>Time:</strong> {{ $subject->importedClasses->first()->time }}</p>
                                    <p><strong>Enrolled Students:</strong> {{ $studentCount }}</p>
                                </div>
                                <div class="col-md-6">
                            
                                    <p><strong>Days:</strong> {{ $subject->importedClasses->first()->days }}</p>
                                    <p><strong>Term:</strong> {{ $subject->term }}</p>
                                    <p><strong>Calculation Used:</strong> {{ $subject->subject_type }}</p>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="card-header">
                        <h3 class="card-title">List of Enrolled Students</h3>
                    </div>

                    <div class="card-body">
                        <button type="button" class="btn btn-primary" id="assessmentModalButton" data-toggle="modal" data-target="#assessmentModal">
                            Add Assessment
                        </button>
                          <a href="{{ route('instructor.editAssessments', ['subjectId' => $subject->id]) }}" class="btn btn-primary">Edit Assessments</a>


                      <form action="{{ route('insert.scores') }}" method="post">
                                 @csrf
                               <div class="form-row mb-3">
                                   
                                    
                                </div>


                                <style>
                                    .table-scroll-container {
                                        overflow-x: auto;
                                        max-width: 100%;
                                    }

                                   .table-container table {
                                        width: auto;
                                       
                                    } 

                                   
                                    table, th, td {
                                        border: 1px solid #000; 
                                        border-collapse: collapse;
                                    }

                                    .fixed-column {
                                        position: sticky;
                                        left: 0;
                                        z-index: 1;
                                        border: 1px solid #000; 
                                    }

                                    .assessment-column {
                                        text-align: center;
                                        width: 80px;
                                        border: 1px solid #000; 
                                        
                                    }

                                    .assessment-type-header,
                                    .grading-period-header,
                                    .gender-header {
                                        background-color: #f2f2f2;
                                        border: 1px solid #000; 

                                    }

                                    
                                    .table-container thead th {
                                        border-top: 1px solid #000; 
                                        border-bottom: 1px solid #000; 
                                    }

                                    
                                    .table-container tbody tr:first-child td {
                                        border-top: 1px solid #000; 
                                        border-bottom: 1px solid #000; 
                                    }


                                     
                                </style>
                           <div class="table-container" class="table-scroll-container" >
                           <table class="table" >
                             <thead>
                                <tr>
                                    <th class="fixed-column"></th>
                                    <th class="fixed-column"></th>
                                    <th class="fixed-column"></th>
                                     <th class="fixed-column"></th> 

                                    @php
                                        $gradingPeriods = $assessments->pluck('grading_period')->unique();
                                        $assessmentTypes = $assessments->pluck('type')->unique();
                                    @endphp
                                    
                                   
                                    @foreach ($gradingPeriods as $gradingPeriod)
                                        @php
                                            $gradingPeriodAssessmentTypes = $assessments
                                                ->where('grading_period', $gradingPeriod)
                                                ->pluck('type')
                                                ->unique();
                                            $colspan = $gradingPeriodAssessmentTypes->reduce(function ($carry, $assessmentType) use ($assessments, $gradingPeriod) {
                                                return $carry + $assessments
                                                    ->where('grading_period', $gradingPeriod)
                                                    ->where('type', $assessmentType)
                                                    ->count() + 1;
                                            }, 0);
                                        @endphp

                                        <!-- Header for the Grading periods e.i  -->
                                        <th colspan="{{ $colspan + 1 }}" class="text-center grading-period-header">
                                            {{ $gradingPeriod }}
                                        </th>
                                    @endforeach
                                </tr>

                               <tr>
                                <th class="fixed-column"></th> 
                                <th class="fixed-column"></th> 
                                 <th class="fixed-column"></th> 
                                <th class="fixed-column"></th> 

                              @foreach ($gradingPeriods as $gradingPeriod)
                                    @php
                                        $gradingPeriodAssessmentTypes = $assessments
                                            ->where('grading_period', $gradingPeriod)
                                            ->pluck('type')
                                            ->unique();
                                    @endphp


                                    @foreach ($gradingPeriodAssessmentTypes as $assessmentType)

                                        <th colspan="{{ $assessments->where('grading_period', $gradingPeriod)->where('type', $assessmentType)->count() }}" class="text-center assessment-type-header">
                                            {{ $assessmentType }}
                                        </th>
                                           <th class="text-center">Total </th>
    
                                    @endforeach
                                    
          
                                    @if ($gradingPeriod == "First Grading")
                                        <th class="text-center">FG Grade</th>
                                    @endif

                                      
                                    @if ($gradingPeriod == "Midterm")
                                        <th class="text-center">Midterm Grade</th>
                                    @endif

                                    @if ($gradingPeriod == "Finals")
                                        <th class="text-center">Finals Grade</th>
                                    @endif
                                @endforeach

                            </tr>

                    <tr>
                        <th class="fixed-column"></th> 
                         <th class="fixed-column"></th> 
                        <th class="fixed-column"></th> 
                        <th class="fixed-column"></th> 

                        @foreach ($gradingPeriods as $gradingPeriod)
                            @foreach ($assessmentTypes as $assessmentType)
                                @php
                                    $gradingPeriodAssessments = $assessments
                                        ->where('grading_period', $gradingPeriod)
                                        ->where('type', $assessmentType)
                                        ->sortBy(function ($assessment) {
                                            $typeOrder = [
                                                        'Quiz' => 1,
                                                        'Additional Points Quiz' => 1,
                                                        'OtherActivity' => 2,
                                                        'Additional Points OT' => 2,
                                                        'Exam' => 3,
                                                        'Additional Points Exam' => 3,
                                                        'Lab Activity' => 4,
                                                        'Additional Points Lab' => 4,
                                                        'Lab Exam' => 5,
                                                        'Direct Bonus Grade' => 6,
                                                        ];

                                                        
                                                             return [
                                                                'type_order' => $typeOrder[$assessment->type] ?? 999,
                                                               'activity_date' => $assessment->activity_date ? $assessment->activity_date : '9999-12-31',
                                                               
                                                            ];

                                                        });

                                                  $maxPointsTotal = $gradingPeriodAssessments->sum('max_points');
                                                  $hasAssessments = $gradingPeriodAssessments->isNotEmpty();
                                                    @endphp

                       

                                            @foreach ($gradingPeriodAssessments as $assessment)
                                                        <th class="assessment-column">
                                                            <p class="assessment-description"
                                                                data-grading-period="{{ $assessment->grading_period }}"
                                                                data-type="{{ $assessment->type }}"
                                                                data-description="{{ $assessment->description }}">
                                                                {{ $assessment->abbreviation }} <br> {{ number_format($assessment->max_points, $assessment->max_points == intval($assessment->max_points) ? 0 : 2) }}
                                                            </p>

                                                        </th>    

                                                @endforeach
                                             @if ($hasAssessments)
                                                            
                                                            <td class="assessment-column">
                                                                <p class="assessment-description"
                                                                    data-grading-period="{{ $gradingPeriod }}"
                                                                    data-type="{{ $assessmentType }}"
                                                                    data-description="Total Max Points">
                                                                    {{ $maxPointsTotal }}
                                                                </p>
                                                            </td>
                                                             @endif

                                              @endforeach
                                           
                                   
                                    @if ($gradingPeriod == "First Grading")
                                        <th class="text-center"></th>
                                    @endif
                                    
                                    @if ($gradingPeriod == "Midterm")
                                        <th class="text-center"></th>
                                    @endif

                                   
                                    @if ($gradingPeriod == "Finals")
                                        <th class="text-center"></th>
                                    @endif

                                         @endforeach
                                 </tr>

                            <tr>
                                <th class="fixed-column">No.</th> 
                                <th class="fixed-column">ID</th> 
                                <th class="fixed-column">Name</th> 
                                <th class="fixed-column">Course</th> 
                            </tr>


                        </thead>

                       <tbody>
                            @php
                                $studentNumberMale = 1;
                                $studentNumberFemale = 1;
                            @endphp

                            @foreach ($sortedStudents as $gender => $students)
                                <tr>
                                    <td colspan="{{ count($gradingPeriods) + 99 }}" class="gender-header">
                                        {{ $gender }}
                                    </td>
                                </tr>

                                @foreach ($students as $enrolledStudent)
                                    <tr>
                                        <td class="fixed-column">
                                            {{ $gender == 'Male' ? $studentNumberMale++ : $studentNumberFemale++ }}
                                        </td>
                                        <td class="fixed-column">{{ $enrolledStudent->student->id_number }}</td>
                                        <td class="fixed-column">{{ $enrolledStudent->student->last_name }}, {{ $enrolledStudent->student->name }} {{ $enrolledStudent->student->middle_name }}</td>
                                        <td class="fixed-column">{{ $enrolledStudent->student->course }}</td>

                                        @php
                                            $totalPointsForAssessmentType = 0;
                                            $currentColIndex = 1; // Start from the first column


                                            
                                            foreach ($gradingPeriods as $gradingPeriod) {
                                               
                                                foreach ($assessmentTypes as $assessmentType) {
                                                    
                                                    $gradingPeriodAssessments = $assessments
                                                        ->where('grading_period', $gradingPeriod)
                                                        ->where('type', $assessmentType)
                                                        ->sortBy(function ($assessment) {
                                                            $typeOrder = [
                                                                'Quiz' => 1,
                                                                'Additional Points Quiz' => 2,
                                                                'OtherActivity' => 3,
                                                                'Additional Points OT' => 4,
                                                                'Exam' => 5,
                                                                'Additional Points Exam' => 6,
                                                                'Lab Activity' => 7,
                                                                'Lab Exam' => 8,
                                                                'Additional Points Lab' => 9,
                                                                'Direct Bonus Grade' => 10,
                                                            ];
                                                           return [
                                                                'type_order' => $typeOrder[$assessment->type] ?? 999,
                                                               'activity_date' => $assessment->activity_date ? $assessment->activity_date : '9999-12-31',
                                                               
                                                            ];
                                                        });

                                                   
                                                    foreach ($gradingPeriodAssessments as $assessment) {
                                                        $textboxName = "points[{$enrolledStudent->id}][{$assessment->id}]";
                                                        $textboxValue = is_null($enrolledStudent->getScore($assessment->id)) ? '' : $enrolledStudent->getScore($assessment->id);

                                                        echo '<td class="assessment-column">
                                                            <input type="text" name="' . $textboxName . '" class="form-control"
                                                                data-grading-period="' . $assessment->grading_period . '"
                                                                data-type="' . $assessment->type . '"
                                                                value="' . $textboxValue . '"
                                                                style="width: 80px; text-align: center;">
                                                        </td>';


                                                      
                                                        $totalPointsForAssessmentType += is_numeric($textboxValue) ? $textboxValue : 0;

                                                        $currentColIndex++; 
                                                    }

                                                  
                                                    if ($gradingPeriodAssessments->isNotEmpty()) {
                                                       
                                                        echo '<td class="assessment-column">
                                                            <p class="assessment-description" data-type="' . $assessmentType . '" data-description="Total Points">
                                                                ' . $totalPointsForAssessmentType . '
                                                            </p>
                                                        </td>';

                                                        $currentColIndex++; // Move to the next column
                                                    }

                                                    $totalPointsForAssessmentType = 0; // Reset total points for the next assessment type
                                                }

                                               
                                               if ($gradingPeriod == "First Grading") {
                                                    echo '<td class="grade-column">';
                                                    foreach ($enrolledStudent->grades as $grade) {
                                                        if ($grade->fg_grade !== null) {
                                                            echo '<div class="grade-dropdown displayed-value">';
                                                           echo '<span class="displayed-value">' . number_format($grade->fg_grade, $grade->fg_grade == intval($grade->fg_grade) ? 0 : 2) . '</span>';
                                                          echo '<select class="status-dropdown" data-grade-id="' . $grade->id . '">';
                                                            echo '<option value="DEFAULT">Grade</option>';
                                                            echo '<option value="DRP" ' . ($grade->status === 'DRP' ? 'selected' : '') . '>DRP</option>';
                                                            echo '<option value="WITHDRAW" ' . ($grade->status === 'WITHDRAW' ? 'selected' : '') . '>Withdraw</option>';
                                                            echo '</select>';
                                                            echo '</div>';
                                                            echo '<br>';
                                                        }
                                                    }
                                                    echo '</td>';
                                                }

   

                                            if ($gradingPeriod == "Midterm") {
                                                echo '<td class="grade-column">';
                                                foreach ($enrolledStudent->grades as $grade) {
                                                    if ($grade->midterms_grade !== null) {

                                                        echo '<div class="grade-dropdown displayed-value">';
                                                       echo '<span class="displayed-value">' . number_format($grade->midterms_grade, $grade->midterms_grade == intval($grade->midterms_grade) ? 0 : 2) . '</span>';
                                                        echo '<select class="status-dropdown"  data-grade-type="midterm" data-grade-id="' . $grade->id . '">';
                                                            echo '<option value="DEFAULT">Grade</option>';
                                                            echo '<option value="DRP" ' . ($grade->midterms_status === 'DRP' ? 'selected' : '') . '>DRP</option>';
                                                            echo '<option value="WITHDRAW" ' . ($grade->midterms_status === 'WITHDRAW' ? 'selected' : '') . '>Withdraw</option>';
                                                            echo '</select>';
                                                            echo '</div>';
                                                            echo '<br>';
                                                    }
                                                }
                                                echo '</td>';
                                            }

                                          
                                            if ($gradingPeriod == "Finals") {
                                                echo '<td class="grade-column">';
                                                foreach ($enrolledStudent->grades as $grade) {
                                                       echo '<div class="grade-dropdown displayed-value">';
                                                    if ($grade->finals_grade !== null) {
                                                        echo '<span class="displayed-value">' . number_format($grade->finals_grade, $grade->finals_grade == intval($grade->finals_grade) ? 0 : 2) . '</span>';
                                                     echo '<select class="status-dropdown"  data-grade-type="final" data-grade-id="' . $grade->id . '">';
                                                            echo '<option value="DEFAULT">Grade </option>';
                                                             echo '<option value="DRP" ' . ($grade->finals_status === 'DRP' ? 'selected' : '') . '>DRP</option>';
                                                                echo '<option value="WITHDRAW" ' . ($grade->finals_status === 'WITHDRAW' ? 'selected' : '') . '>Withdraw</option>';
                                                                  echo '<option value="NFE" ' . ($grade->finals_status === 'NFE' ? 'selected' : '') . '>NFE</option>';
                                                                echo '<option value="INC" ' . ($grade->finals_status === 'INC' ? 'selected' : '') . '>INC</option>';
                                                                echo '</select>';
                                                            echo '</div>';
                                                            echo '<br>';
                                                    }
                                                }
                                                echo '</td>';
                                            }
                                            }
                                        @endphp
                                    </tr>
                                @endforeach
                            @endforeach


                            <!----for the date appear below---->
                          <tr>
                            <th class="fixed-column"></th> 
                             <th class="fixed-column"></th> 
                            <th class="fixed-column"></th> 
                            <th class="fixed-column"></th> 

                          @php
                            $currentColIndex = 1; // Start from the first column

                          
                            foreach ($gradingPeriods as $gradingPeriod) {
                               
                                foreach ($assessmentTypes as $assessmentType) {
                                   
                                    $gradingPeriodAssessments = $assessments
                                        ->where('grading_period', $gradingPeriod)
                                        ->where('type', $assessmentType)
                                        ->sortBy(function ($assessment) {
                                            $typeOrder = [
                                                'Quiz' => 1,
                                                        'Additional Points Quiz' => 2,
                                                        'OtherActivity' => 3,
                                                        'Additional Points OT' => 4,
                                                        'Exam' => 5,
                                                        'Additional Points Exam' => 6,
                                                        'Lab Activity' => 7,
                                                        'Lab Exam' => 8,
                                                        'Additional Points Lab' => 9,
                                                        'Direct Bonus Grade' => 10,
                                            ];
                                            return [
                                                                'type_order' => $typeOrder[$assessment->type] ?? 999,
                                                              'activity_date' => $assessment->activity_date ? $assessment->activity_date : '9999-12-31',
                                                               
                                                            ];
                                        });

                                    
                                    foreach ($gradingPeriodAssessments as $assessment) {
                                        echo '<th class="assessment-column">
                                            <p class="assessment-description"
                                                data-grading-period="' . $assessment->grading_period . '"
                                                data-type="' . $assessment->type . '"
                                                data-description="' . $assessment->description . '">
                                                ' . ($assessment->activity_date ?? '') . '
                                            </p>
                                                <button class="btn btn-sm btn-publish publish-button btn-primary" data-assessment-id="' . $assessment->id . '"   data-published="' . ($assessment->published ? 'true' : 'false') . '">
                                                    ' . ($assessment->published ? 'Hide Scores' : 'Show Scores') . '
                                                </button>

                                        </th>';

                                        
                                        $currentColIndex++; // Move to the next column
                                    }

                                  
                                    if ($gradingPeriodAssessments->isNotEmpty()) {
                                        //// Empty th for Total Points
                                        echo '<th class="assessment-column"></th>';
                                        $currentColIndex++; 
                                    }
                                }

                                
                               $subjectId = $subject->id;
                             
                                ///// Empty th for grades column under 
                             echo '<th class="grade-column">
                                    <button class="btn btn-sm btn-publish-grades btn-primary"
                                            data-grading-period="' . $gradingPeriod . '"
                                            data-subject-id="' . $subjectId . '">Grades</button>
                                </th>';

                        $currentColIndex++;
                                
                            }

                        @endphp
                                     </tr>
                        </tbody>
                    </table>


                        </div>
                        <a href="{{ route('generateExcelReport', ['subjectId' => $subject->id]) }}" class="btn btn-success" target="_blank">Records Report</a>
                       
                          <a href="{{ route('export.gradeslist', ['subjectId' => $subject->id]) }}" class="btn btn-success" target="_blank">  
                                   Grades List
                                </a>

                          <a href="{{ route('export.summary', ['subjectId' => $subject->id]) }}" class="btn btn-success" target="_blank">  
                                  Summary
                                </a>      
                         <a href="{{ route('teacher.list.studentlistremove', ['subjectId' => $subject->id]) }}" class="btn btn-primary">View Student List</a>

                        <button type="submit" class="btn btn-primary">Save Scores</button>
                    </form>
                  
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<!----modal for setting the assessment--->
<div class="modal fade" id="assessmentModal" tabindex="-1" role="dialog" aria-labelledby="assessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('assessments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assessmentModalLabel">Set Assessment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="assessment_id" id="assessment_id">
                 <input type="hidden" name="subject_id" id="subject_id" value="{{ $subject->id }}">
                <input type="hidden" name="subjectType" id="subject_type" value="{{ $subject->subject_type }}">

                <div class="modal-body">
                     <div class="form-group">
                   <label for="gradingPeriod">Grading Period:</label>
                      <select class="form-control" id="gradingPeriod" name="grading_period" required>
                          <option value="" disabled selected>--- Select Grading ---</option>
                          <option value="First Grading">First Grading</option>
                          <option value="Midterm">Midterm</option>
                          <option value="Finals">Finals</option>
                      </select>
                    </div>
                    <div class="form-group">
                        <label for="assessmentType">Assessment Type:</label>
                            <select class="form-control" id="assessmentType" name="type" required>
                                <option value="" disabled selected>--- Select Type ---</option>
                                <option value="Quiz">Quiz</option>
                                <option value="OtherActivity">Other Activity</option>
                                <option value="Exam">Exam</option>
                                <option value="Lab Activity">Lab Activity</option>
                                <option value="Lab Exam">Lab Exam</option>
                                <option value="Additional Points Quiz">Additional Points Quiz</option>
                                <option value="Additional Points OT">Additional Points Other Activity</option>
                                <option value="Additional Points Exam">Additional Points Exam</option>
                                 <option value="Additional Points Lab">Additional Points Lab</option>
                                <option value="Direct Bonus Grade">Direct Bonus Grade</option>
                                
                            </select>

                        </div>
                       <div id="assessmentFieldsContainer">
                          
                        </div>
                 </div>
                 <div class="modal-footer">
                  
                  <button type="button" class="btn btn-primary" id="addAssessmentFieldsBtn">+</button>
                  <button type="button" class="btn btn-primary" id="saveAssessmentsBtn">save</button>
                 

              </div>
            </form>
        </div>
    </div>
</div>




</div>

    <!-- /.content -->
 
  <!-- /.content-wrapper \

$('#assessmentModal').on('shown.bs.modal', function () {
    //$('#assessmentFieldsContainer').empty(); // Clear existing fields
    if (savedAssessments === null) {
        $.ajax({
            type: 'GET',
            url: '{{ route('assessments.fetch') }}',
            success: function (response) {
                savedAssessments = response.assessments;
                populateAssessmentFields(savedAssessments);
            },
            error: function (error) {
                console.error('Error:', error);
            },
        });
    } else {
        // Data is already fetched, just populate the fields
        populateAssessmentFields(savedAssessments);
    }
});


  -->

  @endsection


  <!--<div class="form-row mb-3">
                                    <div class="col-md-2">
                                        <label for="gradingPeriodDropdown">Grading Period:</label>
                                        <select id="gradingPeriodDropdown" name="gradingPeriodDropdown" class="form-control">
                                        
                                            <option value="First Grading">First Grading</option>
                                            <option value="Midterm">Midterm</option>
                                            <option value="Finals">Finals</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="typeDropdown">Assessment Type:</label>
                                        <select id="typeDropdown" name="typeDropdown" class="form-control">
                                          
                                            <option value="Quiz">Quiz</option>
                                            <option value="OtherActivity">OtherActivity</option>
                                            <option value="Project">Project</option>
                                            <option value="Exam">Exam</option>
                                            <option value="Lab Activity">Lab Activity</option>
                                        </select>
                                    </div>
                                </div>-->
                                