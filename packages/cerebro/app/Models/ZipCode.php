<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * https://www.unitedstateszipcodes.org/zip-code-database/
 */
class ZipCode extends Model
{
    use HasFactory;

    protected $table = 'zip_codes';

    protected $fillable = [
       'zip_code',
       'type',
       'decommissioned',
       'city',
       'state',
       'county',
       'timezone',
       'area_code',
       'world_region',
       'country',
       'latitude',
       'longitude',
       'population',
    ];
}
