<?php

namespace Sagar290\CommissionCalc\Service;


use League\Csv\Reader;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Windwalker\Http\HttpClient;

class CommissionCalculator
{
    /**
     * @var \Iterator|string
     */
    private $records;

    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var mixed|null
     */
    private $rates;
    private $freeOfChargeAmount = 1000;
    private $userTypePrivateCommission = 0.3;
    private $userTypeBusinessCommission = 0.5;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
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

    public function loadData(string $string)
    {
        $csv = Reader::createFromPath('./input.csv', 'r');
        $this->records = $csv->getRecords();
    }

    public function calculateCommissions()
    {
        $commissions = [];
        foreach ($this->records as $record) {
            $commissions[] = $this->calculate($record);
        }

        return $commissions;
    }


    public function convertCurrency($amount, $currencyType, $convertTo = 'EUR')
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


    public function calculate($data)
    {

        if (!array_get($data, '1')) {
            throw new \Exception('Invalid data');
        }

//        $this->setUserData($data);

        $userType = array_get($data, '2');
        $operationType = array_get($data, '3');
        $amount = array_get($data, '4');
        $currencyType = array_get($data, '5');

        $conversionAmount = $this->convertCurrency($amount, $currencyType);

        if ($operationType == 'deposit') {
            return $this->depositCommission($conversionAmount);
        }

        $userId = array_get($data, '1');
        $date = array_get($data, '0');

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

    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param $data
     * @return void
     */
    public function setUserData($data)
    {
        $userId = array_get($data, '1');
        $date = array_get($data, '0');


        if (!array_get($this->users, $userId)) {
            $this->users[$userId] = [];
        }

        if (!array_get($this->users, "{$userId}.{$date}")) {
            $this->users[$userId][$date] = 1;
        }

        $this->users[$userId][$date] += 1;
    }


    private function depositCommission($conversionAmount)
    {
        return round((($conversionAmount * $this->depositCommission) / 100), 2);
    }

    private function withdrawCommission($conversionAmount, $userType, $isFreeOfCharge = false)
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