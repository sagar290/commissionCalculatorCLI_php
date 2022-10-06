<?php

namespace Sagar290\CommissionCalc\Service;


use Exception;
use League\Csv\Reader;
use Windwalker\Http\HttpClient;

class CommissionCalculator
{
    /**
     * @var \Iterator|string
     */
    private $records;

    /**
     * @var HttpClient
     */
    private HttpClient $client;
    /**
     * @var mixed|null
     */
    private $rates;
    private int $freeOfChargeAmount = 1000;
    private float $userTypePrivateCommission = 0.3;
    private float $userTypeBusinessCommission = 0.5;

    public function __construct()
    {
        $this->client = new HttpClient();
    }

    /**
     * user type
     * operation type
     * commission type
     * currency type
     * convert currency to EUR
     * calculate commission
     */

    private $users = [];

    private $depositCommission = 0.03;

    /**
     * @param string $inputFile
     * @return void
     */
    public function loadData(string $inputFile)
    {
        $csv = Reader::createFromPath($inputFile);
        $this->records = $csv->getRecords();
    }


    /**
     * calculate commissions
     * @return array
     * @throws Exception
     */
    public function calculateCommissions(): array
    {
        $commissions = [];
        foreach ($this->records as $record) {
            $commissions[] = $this->calculate($record);
        }

        return $commissions;
    }


    /**
     * Convert currency to EUR
     * @param $amount
     * @param $currencyType
     * @param string $convertTo
     * @return float
     */
    public function convertCurrency($amount, $currencyType, string $convertTo = 'EUR'): float
    {
        if ($currencyType == $convertTo) {
            return $amount;
        }

        if (!$this->rates) {
            $response = json_decode($this->client->get(
                'https://developers.paysera.com/tasks/api/currency-exchange-rates'
            )->getBody()->getContents(), true);

            $this->rates = array_get($response, 'rates');
        }

        return round($amount / array_get($this->rates, $currencyType, 1), 2);

    }


    /**
     * calculate commission
     * @param $row
     * @return float|int
     * @throws Exception
     */
    public function calculate($row)
    {

        if (!array_get($row, '1')) {
            throw new Exception('Invalid data');
        }

        $userType = array_get($row, '2');
        $operationType = array_get($row, '3');
        $amount = array_get($row, '4');
        $currencyType = array_get($row, '5');

        $conversionAmount = $this->convertCurrency($amount, $currencyType);

        if ($operationType == 'deposit') {
            return $this->depositCommission($conversionAmount);
        }

        $date = array_get($row, '0');

        $isFreeOfCharge = false;
        if (in_array(date('D', strtotime($date)), [
            'Sun',
            'Mon',
            'Tue',
            'Wed',
        ])) {
            $isFreeOfCharge = true;
        }

        return $this->withdrawCommission($conversionAmount, $userType, $isFreeOfCharge);

    }

    public function depositCommission($conversionAmount): float
    {
        return round((($conversionAmount * $this->depositCommission) / 100), 2);
    }

    public function withdrawCommission($conversionAmount, $userType, $isFreeOfCharge = false)
    {
        if ($userType == 'private') {
            if ($isFreeOfCharge) {
                $amountAfterFreeChargeAmount = $conversionAmount - $this->freeOfChargeAmount;

                return $amountAfterFreeChargeAmount > 0 ? round((($amountAfterFreeChargeAmount * $this->userTypePrivateCommission) / 100), 2) : 0;
            }

            return round((($conversionAmount * $this->userTypePrivateCommission) / 100), 2);
        }

        return round((($conversionAmount * $this->userTypeBusinessCommission) / 100), 2);
    }

}