<?php

use function Psy\sh;
use GuzzleHttp\Client;

ini_set('display_errors', 1);
error_reporting(-1);

set_error_handler(function($code, $message, $file, $line) {
    if (!error_reporting()) {
        return false;
    }

    throw new ErrorException($message, $code, E_ERROR, $file, $line);
});

require 'vendor/autoload.php';

$http = new Client([
    'allow_redirects' => false,
    'base_uri' => 'http://localhost:8000',
    'connect_timeout' => 5,
    'read_timeout' => 5,
    'timeout' => 5,
]);

function login(Client $http, $method, $url, array $options = []) {
    return $http->request($method, $url, $options + [
        'form_params' => [
            'grant_type'    => 'client_credentials',
            'client_id'     => 1,
            'client_secret' => 'xxxsecretxxx',
            'scope'         => 'kick',
        ],
    ]);
}

function authenticate(Client $http, $token, $method, $url, array $options = []) {
    return $http->request($method, $url, $options + [
        'headers' => [
            'Authorization' => "Bearer $token",
        ],
    ]);
}

eval(sh());
