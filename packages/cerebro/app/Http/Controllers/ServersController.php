<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServersRequest;
use App\Models\Servers;
use App\Services\DataTablesService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ServersController extends Controller
{
    public function servers()
    {
        // return Inertia::render('Servers');
        return view('dashboard.servers.servers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, DataTablesService $dataTablesService)
    {
        return response()->json(Servers::drawIndexTable($request, $dataTablesService));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ServersRequest $request)
    {
        Servers::create($request->all());

        return response()->json(
            [
                'status' => 200
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $server = Servers::find($id);
        return response()->json(
            ['status' => 200, 'data' => $server]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ServersRequest $request, $id)
    {
        /**
         * @var $server Servers
         */
        $data = $request->all();
        $server = Servers::find($id);
        $server->update($data);

        return response()->json(
            ['status' => 200]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $server = Servers::find($id);
        if ($server) {
            $server->delete();
            return response()->json(
                ['status' => 200]
            );
        }

        return response()->json(
            ['status' => 500, 'message' => 'Server not found']
        );
    }
}
