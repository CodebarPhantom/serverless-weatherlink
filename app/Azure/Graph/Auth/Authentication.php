<?php

namespace App\Azure\Graph\Auth;
use Illuminate\Support\Facades\Http;


class Authentication{

    public function signIn()
    {
        $request = Http::asForm()->post(config("azure.authority").config("azure.tokenEndpoint"), [
            'client_id' => config("azure.appId"),
            'scope' => config("azure.scopes"),
            'client_secret' => config("azure.appSecret"),
            'grant_type' => 'client_credentials',

        ]);
        $response = json_decode($request->getBody());

        return $response;
    }

}
