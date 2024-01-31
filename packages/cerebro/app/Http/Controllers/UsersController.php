<?php

namespace App\Http\Controllers;

use App\Models\Sites;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\DataTablesService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except('regenerate', 'update');
        $this->middleware('verified')->only('regenerate');
    }

    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\DataTablesService $dataTablesService
     * @return Response
     */
    public function index(Request $request, DataTablesService $dataTablesService)
    {
        $columns = [
            ['db' => 'id', 'dt' => 0],
            ['db' => 'name', 'dt' => 1],
            ['db' => 'email', 'dt' => 2],
            ['db' => 'post_back_amount', 'dt' => 3],
            ['db' => 'personal_min_req', 'dt' => 4],
            ['db' => 'actions', 'dt' => 5, 'formatter' => function () {
                return;
            }],
            ['db' => 'email_verified_at', 'dt' => 6]
        ];

        $where = $dataTablesService->filter($request, $columns, false);
        $order = $dataTablesService->columnOrder($columns);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $db = config('database.connections.mysql.database');

        if ($where !== '') {
            $where = "AND " . $where;
        }

        $users = DB::select(DB::raw("SELECT SQL_CALC_FOUND_ROWS
            id,
            name,email,
            post_back_amount,
            personal_min_req,
            deleted_at,
            menuroles,
            email_verified_at
            FROM $db.users
            WHERE (
                deleted_at IS NULL
            $where
            )
            GROUP BY id
            $order
            LIMIT $start, $length
        "));

        $recordsFiltered = DB::selectOne(DB::raw("SELECT FOUND_ROWS() AS quantity"))->quantity;
        $recordsTotal = DB::selectOne(DB::raw("SELECT COUNT(id) AS quantity FROM $db.users WHERE deleted_at IS NULL "))->quantity;

        $users = json_decode(json_encode($users), true);

        $returnData = [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataTablesService->prepareData($users, $columns)
        ];
        return response()->json($returnData);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json(
            ['status' => 200, 'data' => $user]
        );
    }

    /**
     * Store the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:1|max:256',
            'email' => 'required|email|max:256|unique:users,email',
            'email_ccpa' => 'nullable|email|max:256',
            'password' => 'required|confirmed',
            'min_price' => 'nullable|numeric|min:0|max:9999.99|regex:/^\d+(\.\d{1,2})?$/',
            'lead_min_price' => 'required|min:1|max:256',
            'processing_time_sec' => 'nullable|numeric|min:0',
            'processing_time_min' => 'nullable|numeric|min:0',
            'post_back_amount' => 'numeric|nullable|gte:0',
            'personal_min_req' => 'numeric|nullable|gte:0',
        ]);
        $data = $request->all();
        $data['processing_time'] = $data['processing_time_sec'] + $data['processing_time_min'] * 60;
        $data['password'] = Hash::make($data['password']);
        $data['menuroles'] = $data['role'];
        $user = User::create($data);

        if ($data['role'] == 'admin') {
            $user->assignRole('user');
        }

        $user->assignRole($data['role']);

        if(env('APP_ENV') == 'production'){
            $user->sendEmailVerificationNotification();
        }

        $token = $user->createToken('cerebroclienttoken')->plainTextToken;

        return response()->json(
            [
                'status' => 200,
                'token' => $token
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:1|max:256',
            'email' => 'required|email|max:256|unique:users,email,' . $id,
            'email_ccpa' => 'nullable|email|max:256',
            'password' => 'confirmed',
            'min_price' => 'nullable|numeric|min:0|max:9999.99|regex:/^\d+(\.\d{1,2})?$/',
            'lead_min_price' => 'required|min:1|max:256',
            'processing_time_sec' => 'nullable|int|min:0|max:59',
            'processing_time_min' => 'nullable|int|min:0|max:59',
            'post_back_amount' => 'numeric|nullable|gte:0',
            'personal_min_req' => 'numeric|nullable|gte:0'
        ]);

        if (!auth()->user()->hasRole('admin') && auth()->user()->id != $id) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $user = User::find($id);
        $oldEmail = $user->email;
        $data = $request->all();
        $data['processing_time'] = $data['processing_time_sec'] + $data['processing_time_min'] * 60;
        $data['menuroles'] = $data['role'];

        if ($data['password'] == null) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        $changes = $user->getChanges();
        if (isset($changes['email'])) {

            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
            $site = Sites::where(['form_id' => $user->id])->first();

            if($site) {
                $site->token = User::regenerateToken($user);
                $site->save();
            }
        }

        if (auth()->user()->hasRole('admin')) {
            $user->syncRoles($data['role']);
            if ($data['role'] == 'admin') {
                $user->assignRole('user');
            }
        }

        if ($request->ajax()) {
            return response()->json(
                ['status' => 200]
            );
        } else {
            return redirect('/profile');
        }
    }

    /**
     * Regenerate token for form.
     *
     * @param int $id
     * @return mixed
     */
    public function regenerate($id)
    {
        $user = User::find($id);

        if ($user) {
            $token = User::regenerateToken($user);

            return response()->json(
                [
                    'status' => 200,
                    'token' => $token
                ]
            );
        }

        return response()->json(['status' => 404]);
    }

    /**
     * Resend email confirmation to user.
     *
     * @param int $id
     * @return Response
     */
    public function resendEmailConfirmation($id)
    {
        $user = User::find($id);
        if ($user) {
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(
                ['status' => 200]
            );
        }
    }
}
