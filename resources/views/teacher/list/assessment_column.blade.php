<!-- assessment_column.blade.php -->
<td class="assessment-column">
    <input type="number" name="points[{{ $student->id }}][{{ $assessment->id }}]"
        class="form-control"
        data-grading-period="{{ $assessment->grading_period }}"
        data-type="{{ $assessment->type }}"
        value="{{ $student->getScore($assessment->id) }}"
        style="width: 80px; text-align: center;">
</td>