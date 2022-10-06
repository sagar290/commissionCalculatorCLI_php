<?php

namespace Sagar290\CommissionCalc\Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Sagar290\CommissionCalc\Exception\InvalidDataException;
use Sagar290\CommissionCalc\Service\CommissionCalculator;

class CalculatorTest extends TestCase
{
    public CommissionCalculator $calculator;

    public function __construct()
    {
        $this->calculator = new CommissionCalculator();

        parent::__construct();
    }

    /**
     * @test
     */
    public function is_valid_withdraw_commission_for_private_user_type()
    {

        $commission = $this->calculator->withdrawCommission(
            2000,
            'private'
        );

        $this->assertEquals(6, $commission, 'Commission is not correct');


        $commission = $this->calculator->withdrawCommission(
            2000,
            'private',
            true
        );

        $this->assertEquals(3, $commission, "Free of charge commission is not correct");
    }

    /**
     * @test
     */
    public function is_valid_withdraw_commission_for_business_user_type()
    {
        $commission = $this->calculator->withdrawCommission(
            2000,
            'business',
        );

        $this->assertEquals(10, $commission, "Commission is not correct");
    }

    /**
     * @test
     */
    public function is_valid_deposit_commission()
    {
        $commission = $this->calculator->depositCommission(
            2000,
        );

        $this->assertEquals(0.6, $commission, "Commission is not correct");

    }

    /**
     * @test
     * @throws Exception
     */
    public function is_valid_calculation()
    {
        $data = [
            [
                "expected" => 0.60,
                "data" => ["2014-12-31", 4, "private", "withdraw", "1200.00", "EUR"]
            ],
            [
                "expected" => 3.00,
                "data" => ["2015-01-01", 4, "private", "withdraw", "1000.00", "EUR"]
            ],
            [
                "expected" => 0.00,
                "data" => ["2016-01-05", 4, "private", "withdraw", "1000.00", "EUR"]
            ],
            [
                "expected" => 0.06,
                "data" => ["2016-01-05", 1, "private", "deposit", "200.00", "EUR"]
            ]
        ];

        foreach ($data as $key => $item) {
            $this->assertEquals($item['expected'], $this->calculator->calculate($item['data']), "commission is not correct in index {$key}");
        }
    }

    // Exception Tests


    /**
     * @test
     * @throws Exception
     */
    public function is_valid_data_format_in_calculate_params_for_empty_array()
    {
        $this->expectException(InvalidDataException::class);
        $this->calculator->calculate([]);

    }


    /**
     * @test
     * @throws Exception
     */
    public function is_valid_data_format_in_calculate_params_for_corrupted_array()
    {
        $this->expectException(InvalidDataException::class);
        $this->calculator->calculate(["random", "ss", "ass"]);

    }

    /**
     * @test
     * @throws Exception
     */
    public function is_valid_data_format_in_calculate_params_for_string()
    {
        $this->expectException(InvalidDataException::class);
        $this->calculator->calculate("random");

    }

}