<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CustomResponse;
use App\Models\API\Currency;
use App\Models\API\UserCurrency;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    private $result = [];

    public function index(Request $request)
    {
        $currencyIds = UserCurrency::where('user_id', (string)$request->user()->id)->pluck('currency_id');

        $defaultCurrencies = Currency::where('editable', false)->get();
        $specialCurrencies = Currency::where('editable', true)->whereIn('id', $currencyIds)->get();

        $this->result['data'] = $defaultCurrencies->merge($specialCurrencies);

        return CustomResponse::customResponse(
            $this->result,
            CustomResponse::$successCode,
            __('api.currencies are gotten successfully')
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {

            return CustomResponse::customResponse($validator->messages(), CustomResponse::$unprocessableEntity);
        }

        $validated = $validator->validated();
        $validated["editable"] = true;

        DB::beginTransaction();
        try {
            $currency = Currency::create($validated);
            UserCurrency::create([
                "user_id" => $request->user()->id,
                "currency_id" => $currency->id
            ]);

            DB::commit();

            return CustomResponse::customResponse(
                $validated,
                CustomResponse::$successCode,
                __('api.currencies are gotten successfully')
            );
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return CustomResponse::customResponse(
            null,
            CustomResponse::$errorCode,
            __('api.currency was not added successfully')
        );
    }

    public function show($id)
    {
        $data = Currency::find($id);
        if ($data == null) {
            return CustomResponse::customResponse(
                null,
                CustomResponse::$notFound,
                __('api.currency not found')
            );
        }

        $this->result['data'] = $data;

        return CustomResponse::customResponse(
            $this->result,
            CustomResponse::$successCode,
            __('api.currencies are gotten successfully')
        );
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rules($id));

        if ($validator->fails()) {

            return CustomResponse::customResponse($validator->messages(), CustomResponse::$unprocessableEntity);
        }

        $validated = $validator->validated();

        $currencyAvailability = UserCurrency::where('user_id', (string)$request->user()->id)
            ->where('currency_id', $id)->first();

        if ($currencyAvailability == null) {

            return CustomResponse::customResponse(null, CustomResponse::$unprocessableEntity, 'api.currency was not found');
        }
        Currency::where('id', $id)->where('editable', true)->update($validated);
        $this->result["data"] = $validated;
        return CustomResponse::customResponse($this->result,CustomResponse::$successCode, 'api.currency was updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $currency = Currency::find($id);

        if ($currency == null) {
            return CustomResponse::customResponse(null, CustomResponse::$notFound, 'api.currency was not found');
        }

        if($currency->editable == false) {
            return CustomResponse::customResponse(null, CustomResponse::$unprocessableEntity, 'api.currency cannot be updated');
        }

        DB::beginTransaction();
        try {
            UserCurrency::where('currency_id', $id)->where('user_id', $request->user()->id)->delete();
            $this->result = $currency->delete();
            DB::commit();
            return CustomResponse::customResponse(null, CustomResponse::$successCode, "api.currency has been deleted successfully");

        } catch (\Exception $e) {
            DB::rollBack();
            return CustomResponse::customResponse(null, CustomResponse::$errorCode, "api.currency has not been deleted successfully");
        }
    }

    public function rules($id = null)
    {
        return [
            'symbol' => 'required|unique:currencies,symbol,' . $id . ',id|max:3',
            'name' => 'required|max:255',
            'full_name' => '',
        ];
    }
}
