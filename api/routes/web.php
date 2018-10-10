<?php

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

$router->get('/', function () {

    return response()->json([
        'status' => false,
        'type' => 'error_request',
        'message' => 'Staff Only'
    ]);
});

$router->get('/orders', 'OrderController@list');
$router->post('/order/new', 'OrderController@new');
$router->post('/order/discounts', 'OrderController@discounts');
