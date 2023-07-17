<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Config;

class CRMController extends Controller
{
    //
    public function edit(Request $request): Response
    {
        return Inertia::render('Crm/Create', []);
    }

    public function create(Request $request)
    {
        //validate according to zoho rules
        $validator = Validator::make($request->all(), [
            'deal_name' => ['required', 'max:255'],
            'deal_stage' => ['required', 'max:255'],
            'acc_name' => ['required', 'max:255', 'regex:/[a-zA-Z]/'],
            'acc_phone' => ['required', 'regex:/^([\+]?)(?![\.-])(?>(?>[\.-]?[ ]?[\da-zA-Z]+)+|([ ]?\((?![\.-])(?>[ \.-]?[\da-zA-Z]+)+\)(?![\.])([ -]?[\da-zA-Z]+)?)+)+(?>(?>([,]+)?[;]?[\da-zA-Z]+)+)?[;]?$/'],
            'acc_website' => ['required', 'regex:/^(http:\\/\\/www.|https:\\/\\/www.|ftp:\\/\\/www.|www.|http:\\/\\/|https:\\/\\/|ftp:\\/\\/|){1}[^\\x00-\\x19\\x22-\\x27\\x2A-\\x2C\\x2E-\\x2F\\x3A-\\x40\\x5B-\\x5E\\x60\\x7B\\x7D-\\x7F]+(\\.[^\\x00-\\x19\\x22\\x24-\\x2C\\x2E-\\x2F\\x3C\\x3E\\x40\\x5B-\\x5E\\x60\\x7B\\x7D-\\x7F]+)+(\\/[^\\x00-\\x19\\x22\\x3C\\x3E\\x5E\\x7B\\x7D-\\x7D\\x7F]*)*$/'],
        ], $messages = [
            'acc_name.regex' => 'The Account Name must containt letters.',
        ]);
 
        if ($validator->fails()) {
            error_log("Validator fail");
            return redirect()->back()->withErrors($validator);
        }

        //preemtiveliy refresh access token:
        $token_req = Http::withQueryParameters([
            'refresh_token' => config('access.refresh'),
            'client_id' => config('access.client_id'),
            'client_secret' => config('access.secret'),
            'grant_type' => "refresh_token",
        ])->post('https://accounts.zoho.eu/oauth/v2/token')->json();

        $token = $token_req['access_token'];

        $deal_data = ["data" => [[
            "Deal_Name" => $request["deal_name"],
            "Stage" => $request["deal_stage"],
            "Account_Name" => $request["acc_name"],
        ]]];

        $account_data = ["data" => [[
            "Account_Name" => $request["acc_name"],
            "Website" => $request["acc_website"],
            "Phone" => $request["acc_phone"],
        ]]];

        //create account
        $acc_response = Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken " . $token,
            'Content-Type' => 'application/json',
         ])
         ->send('POST', "https://www.zohoapis.eu/crm/v3/Accounts", [
            'body' => json_encode($account_data)
         ])->json();

        //create deal for the given account
        $deal_response = Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken " . $token,
            'Content-Type' => 'application/json',
         ])
         ->send('POST', "https://www.zohoapis.eu/crm/v3/Deals", [
            'body' => json_encode($deal_data)
         ])->json();

         return Inertia::render('Crm/Create', ['deal_succes' => $deal_response, 'acc_succes' => $acc_response]);

    }


}