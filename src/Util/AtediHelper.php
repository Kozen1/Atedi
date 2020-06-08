<?php

namespace App\Util;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class AtediHelper
{
    public function strTotalPrice($intervention) 
    {
        $totalEuro = 0;
        $totalCents = 0;

        $tasksCollection = $intervention->getTasks();
        foreach ( $tasksCollection as $task ) {
            
            $price = $task->getPrice();

            if (strpos($price,'.')) {
                $delimiter = '.';
            } else if (strpos($price,',')) {
                $delimiter = ',';
            } else {
                $delimiter = " ";
            }

            $taskPrice = explode($delimiter,$price);

            if (count($taskPrice) > 1) {
                $taskEuro = intval($taskPrice[0]);
                $taskCents = intval($taskPrice[1]);
                if (strlen($taskPrice[1]) == 1) {
                    $taskCents = $taskCents*10;
                }
            } else {
                $taskEuro = intval($taskPrice[0]);
                $taskCents = 0;
            }

            $totalEuro = $totalEuro + $taskEuro;
            $totalCents = $totalCents + $taskCents;

            if ( $totalCents >= 100 ) {
                $totalEuro++;
                $totalCents = $totalCents - 100;
            }
        }

        $totalEuro = strval($totalEuro);
        $totalCents = strval($totalCents);
        if (strlen($totalCents) == 1) {
            $totalCents = '0'.$totalCents;
        }
        $totalPrice = $totalEuro.','.$totalCents;

        return $totalPrice;
    }
}