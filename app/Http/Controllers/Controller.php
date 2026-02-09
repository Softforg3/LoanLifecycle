<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function exampleFunction(Request $request)
    {
        // simple example for you to see how little needs to be done for basic post input handling
        $inputFromPostData = $request->input('post_field_name');

        // all return values are by default serialized to json
        return ['exampleOutput' => $inputFromPostData];
    }
}
