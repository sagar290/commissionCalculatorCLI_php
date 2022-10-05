<?php

use Sagar290\CommissionCalc\Service\CommissionCalculator;

require __DIR__ . '/vendor/autoload.php';

$class = new CommissionCalculator();

$class->loadData($argv[1]);

$commissions = $class->calculateCommissions();

array_walk($commissions, function ($commission) {
    echo $commission . PHP_EOL;
});