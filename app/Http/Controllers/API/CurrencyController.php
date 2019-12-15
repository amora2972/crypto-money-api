<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CustomResponse;
use App\Models\API\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    private $result = [];

    public function index()
    {
        $data = Currency::all();

        $this->result['data'] = $data;

        return CustomResponse::customResponse(
            $this->result,
            CustomResponse::$successCode,
            __('api.currencies are gotten successfully')
        );
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), $this->store_rules())->validate();

        $currency = new Currency;
        $currency->symbol = @$request->symbol;
        $currency->full_name = @$request->full_name;
        $currency->name = @$request->name;
        $currency->save();

        return CustomResponse::customResponse(
            $validated,
            CustomResponse::$successCode,
            __('api.currencies are gotten successfully')
        );
    }

    public function show($id)
    {
        $data = Currency::find($id);

        $this->result['data'] = $data;

        return CustomResponse::customResponse(
            $this->result,
            CustomResponse::$successCode,
            __('api.currencies are gotten successfully')
        );
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function store_rules()
    {
        return [
            'symbol' => 'required|unique:currencies|max:3',
            'name' => 'required|max:255',
            'full_name' => '',
        ];
    }
}
