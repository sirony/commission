<?php

include('App.php');


$comm = new App;

foreach (explode("\n", file_get_contents($argv[1])) as $row){

    $comm->inputStr = $row;
    
    $comm->parseStr();

    $comm->setBinStatus();
    $comm->setRate();

    echo $comm->getCommission()."\n";

}