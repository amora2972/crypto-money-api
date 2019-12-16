<?php


namespace App\Http\Repositories;


use App\Models\API\Currency;
use App\Models\API\Transaction;
use GuzzleHttp\Client;

class Calculator
{
    private $newRate;
    private $currency;
    private $exchangeQuantity;

    /**
     * Calculator constructor.
     * @param $currentRate
     * @param $newRate
     * @param $currency
     */
    public function __construct($currency, $exchangeQuantity, $newRate = null)
    {
        $this->newRate = $newRate;
        $this->currency = $currency;
        $this->exchangeQuantity = $exchangeQuantity;
    }


    public function calculateProfit()
    {
        $currencyId = Currency::where('symbol', $this->currency)->pluck('id');
        $transactions = Transaction::withCurrency($currencyId)->withUser()->get();
        $amount = 0;
        $total = 0;
        $transactions->map(function ($transaction) use (&$amount, &$total) {
            $amount += $transaction->amount;
            $total += $transaction->total;
        });
        $oldRate = $total / $amount;

        if (is_null($this->newRate)) {

            $params = http_build_query ([
                "access_key" => config('coinlayer.COIN_LAYER_API_KEY'),
                "symbols" => $this->currency
            ]);
            $url = config('coinlayer.COIN_LAYER_BASE_URL') . 'live?' . $params;
            $client = new Client();
            $data = $client->get($url);
            $data = json_decode($data->getBody()->getContents());
            if($data->success) {
                $this->newRate = ((array) $data->rates)[$this->currency];
                $newPrice = $this->exchangeQuantity * $this->newRate;
            }
        } else {
            $newPrice = $this->exchangeQuantity * $this->newRate;
        }

        $oldPrice = $this->exchangeQuantity * $oldRate;
        $profit = $newPrice - $oldPrice;
        return $profit;
    }

    /**
     * @return mixed
     */
    public function getCurrentRate()
    {
        return $this->currentRate;
    }

    /**
     * @param mixed $currentRate
     */
    public function setCurrentRate($currentRate): void
    {
        $this->currentRate = $currentRate;
    }

    /**
     * @return mixed
     */
    public function getNewRate()
    {
        return $this->newRate;
    }

    /**
     * @param mixed $newRate
     */
    public function setNewRate($newRate): void
    {
        $this->newRate = $newRate;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }
}
