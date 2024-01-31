<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SsnRequests extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'delay_lvl',
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

    public static function resetDelay(Request $request)
    {
        SsnRequests::where('ip',$request->ip())->delete();
    }

    public static function getDelay(Request $request)
    {
        $SsnRequests = SsnRequests::where('ip',$request->ip())->first();
        return $SsnRequests==null ? 1 : $SsnRequests->delay_lvl;
    }

    public static function increacDelayLvl(Request $request)
    {
        $SsnRequsts = SsnRequests::firstOrCreate([
            'ip' => $request->ip()
        ], [
            'ip' => $request->ip(),
            'delay_lvl' => 1,
        ]);
        $SsnRequsts->delay_lvl++;
        $SsnRequsts->save();
    }
}
