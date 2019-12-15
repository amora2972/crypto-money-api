<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CustomResponse;
use App\Models\API\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
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
}
