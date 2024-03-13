<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransmutationService; 
class TestController extends Controller
{
   public function testTransmutation()
    {
        $transmutationService = new TransmutationService();
        $totalMaxPointsLab = 330; 

        $result = $transmutationService->getLabTransmutationTable($totalMaxPointsLab);

        
        dd($result);
    }
}
