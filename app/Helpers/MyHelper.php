<?php


use Illuminate\Support\Facades\Auth;

// app/Helpers/MyHelper.php
if (!function_exists('calculateWeightedAverage')) {
    function calculateWeightedAverage($actualPointsQuizzes, $maxPointsQuizzes, $actualPointsClassStandings, $maxPointsClassStandings, $actualPointsExam, $maxPointsExam) {
        $weightedAverage = (
            ($actualPointsQuizzes / $maxPointsQuizzes) * 40 +
            ($actualPointsClassStandings / $maxPointsClassStandings) * 20 +
            ($actualPointsExam / $maxPointsExam) * 40
        );

        return $weightedAverage;
    }
}