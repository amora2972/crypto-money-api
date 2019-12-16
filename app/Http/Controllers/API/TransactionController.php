<?php

namespace App\Http\Controllers\API;

use App\Http\Helpers\CustomResponse;
use App\Http\Requests\API\TransactionRequest;
use App\Models\API\Transaction;
use Illuminate\Http\Request;

class TransactionController
{
    private $result = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->result['data'] = Transaction::with('currency')->withUser()->get();

        return CustomResponse::customResponse($this->result, CustomResponse::$successCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        $validator = $request->validated();

        $input = $validator;

        if ($input["total"] != $input["buying_rate"] * $input["amount"]) {
            $this->result["data"] = $input;
            return CustomResponse::customResponse($this->result, CustomResponse::$unprocessableEntity, 'api.total was not calculated correctly');
        }

        $input["user_id"] = (string)$request->user()->id;

        try {

            $this->result["data"] = Transaction::create($input);
            return CustomResponse::customResponse($this->result, CustomResponse::$successCode, 'api.transaction has been created successfully');
        } catch (\Exception $e) {

            return CustomResponse::customResponse($this->result, CustomResponse::$errorCode, 'api.transaction was not created successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $transaction = Transaction::with('currency')->withUser()->where('id', $id)->first();
            $this->result["data"] = $transaction;
            return CustomResponse::customResponse($this->result, CustomResponse::$successCode);
        } catch (\Exception $e) {
            return CustomResponse::customResponse($this->result, CustomResponse::$errorCode, "api.transaction could not be shown");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionRequest $request, $id)
    {
        $validator = $request->validated();
        if ($validator["total"] != $validator["amount"] * $validator["buying_rate"]) {
            return CustomResponse::customResponse($this->result, CustomResponse::$unprocessableEntity, 'api.total was not calculated correctly');
        }
        $transaction = Transaction::where('id', $id);

        if (!$transaction->count() > 0) {
            return CustomResponse::customResponse($this->result, CustomResponse::$notFound, 'api.transaction was not found');
        }
        try {

            $transaction->update($validator);
            $this->result["data"] = Transaction::where('id', $id)->first();

            return CustomResponse::customResponse($this->result, CustomResponse::$successCode, "api.transaction has been updated successfully");
        } catch (\Exception $e) {

            return CustomResponse::customResponse($this->result, CustomResponse::$errorCode, "api.transaction has not been updated successfully");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::where('id', $id);

        if (!$transaction->count() > 0) {
            return CustomResponse::customResponse($this->result, CustomResponse::$notFound, 'api.transaction was not found');
        }

        try {

            $this->result = $transaction->delete();
            return CustomResponse::customResponse($this->result, CustomResponse::$successCode, "api.transaction has been deleted successfully");

        } catch (\Exception $e) {

            return CustomResponse::customResponse($this->result, CustomResponse::$errorCode, "api.transaction has not been deleted successfully");
        }
    }
}
