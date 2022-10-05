<?php


use League\Csv\Reader;

require __DIR__ . '/vendor/autoload.php';


$class = app(\Sagar290\CommissionCalc\Service\CommissionCalculator::class);

$class->loadData('./input.csv');

$commissions = $class->calculateCommissions();

array_walk($commissions, function ($commission) {
    echo $commission . PHP_EOL;
});