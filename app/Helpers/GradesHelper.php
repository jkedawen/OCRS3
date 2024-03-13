<?php

function getStatusDropdown($gradeType, $gradeId, $currentStatus)
{
    $options = ['Actual Grade', 'DRP', 'WITHDRAW', 'INC'];

    $dropdown = '<select class="status-dropdown" data-grade-type="' . $gradeType . '" data-grade-id="' . $gradeId . '">';
    foreach ($options as $option) {
        $selected = ($option === $currentStatus) ? 'selected' : '';
        $dropdown .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
    }
    $dropdown .= '</select>';

    return $dropdown;
}