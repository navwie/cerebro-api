<?php

namespace App\Services;


use App\Models\LogApiTime;
use Illuminate\Http\Request;


class LogService
{
    public function saveLog(Request $request)
    {
        return LogApiTime::create($request->all());
    }

    public function updateLog(Request $request, $id)
    {
        LogApiTime::find($id)->update([
            'response' => $request->response,
            'invalid' => $request->invalid,
        ]);
        LogApiTime::find($id)->touch();
    }
}
