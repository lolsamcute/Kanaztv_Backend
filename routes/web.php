<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});

$router->get('/', function () use ($router) {
    return "Hi";
});

$router->group(['prefix' => 'kazantv/api'], function ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('verify', 'AuthController@verify');
    $router->post('set-pin', 'AuthController@setPin');
    $router->post('register', 'AuthController@create');
    $router->post('resend-pin', 'AuthController@resendPin');
});
