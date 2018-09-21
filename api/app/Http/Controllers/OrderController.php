<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Add new order request.
     *
     * @return Response
     */
    public function add(Request $request) {

        var_dump($request);

        return response()->json([
            'status' => false,
            'type' => 'error_add_order',
            'message' => 'Bad Request'
        ]);
    }
}
