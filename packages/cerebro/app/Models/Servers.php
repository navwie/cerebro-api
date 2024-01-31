<?php

namespace App\Models;

use App\Services\DataTablesService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Servers extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_name',
        'ip_address',
        'base_dir',
        'lets_encrypt_email',
        'container_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
    ];

    protected $dates = [
    ];

    protected $attributes = [
    ];

    public function site()
    {
        return $this->hasMany(Sites::class);
    }

    public static function drawIndexTable(Request $request, DataTablesService $dataTablesService)
    {
        $columns = [
            ['db' => 'id', 'where' => 'id', 'dt' => 0],
            ['db' => 'name', 'where' => 'name', 'dt' => 1],
            ['db' => 'ip_address', 'where' => 'ip_address', 'dt' => 2],
            ['db' => 'is_active', 'where' => 'is_active', 'dt' => 3],
            ['db' => 'actions', 'dt' => 4, 'formatter' => function () {
                return;
            }],
        ];

        $where = $dataTablesService::filter($request, $columns);
        $order = $dataTablesService::columnOrder($columns);
        $start = $request->get('start');
        $length = $request->get('length');

        $database = config('database.connections.mysql.database');

        $servers = DB::select(DB::raw("SELECT SQL_CALC_FOUND_ROWS
            id,
            name,
            ip_address,
            last_activity AS is_active
            FROM $database.servers
            $where AND deleted_at IS NULL
            $order
            LIMIT $start, $length
        "));

        $filtered = DB::selectOne(DB::raw("SELECT FOUND_ROWS() AS quantity"));
        $total = DB::selectOne(DB::raw("SELECT COUNT(id) AS quantity FROM $database.servers WHERE deleted_at IS NULL "));

        $array = json_decode(json_encode($servers), true);

        return [
            "draw" => $request->get('draw') !== null ? $request->get('draw') : 0,
            "recordsTotal" => intval($total->quantity),
            "recordsFiltered" => intval($filtered->quantity),
            "data" => $dataTablesService::prepareData($array, $columns)
        ];
    }
}


