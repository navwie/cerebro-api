<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Error;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Store a newly created error in storage.
     *
     * @param \App\Http\Requests\ApiReapplyStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $data = $request->all();
        Error::create($data);
    }
}
