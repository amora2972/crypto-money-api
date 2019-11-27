<?php

use Illuminate\Database\Seeder;
use GuzzleHttp\Client;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $wantedCurrencies = ["BCD", "BCH", "ETH", "ETN"];

    public function run()
    {
        $currencies = array();

        $client = new Client();
        $url = Config::get("coinlayer.COIN_LAYER_BASE_URL") . "list?access_key=" . Config::get('coinlayer.COIN_LAYER_API_KEY');

        $data = $client->get($url);
        $data = json_decode($data->getBody()->getContents());

        foreach ($data->crypto as $key => $row) {
            if (in_array($key, $this->wantedCurrencies)) {
                $currencies[] = $row;
            }
        }
        foreach($currencies as $key => $row)
        {
            $currency = new \App\Models\API\Currency();
            $currency->symbol = $row->symbol;
            $currency->name = $row->name;
            $currency->full_name = $row->name_full;
            $currency->max_supply = $row->max_supply;
            $currency->save();
        }
    }
}
