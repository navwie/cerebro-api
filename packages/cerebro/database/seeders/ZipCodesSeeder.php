<?php

namespace Database\Seeders;

use App\Models\ZipCode;
use Illuminate\Database\Seeder;


class ZipCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ZipCode::truncate();
        $csvData = file_get_contents('database/seeders/data/dnm_zip_codes.csv');
        $rows = str_getcsv($csvData, "\n");

        foreach ($rows as $i => $row) {
            if ($i == 0) { // skip first row
                continue;
            }
            $columns = str_getcsv($row, ";");
            ZipCode::create([
                'zip_code' => $columns[0],
                'type' => $columns[1],
                'decommissioned' => $columns[2],
                'city' => $columns[3],
                'state' => $columns[4],
                'county' => $columns[5],
                'timezone' => $columns[6],
                'area_code' => $columns[7],
                'world_region' => $columns[8],
                'country' => $columns[9],
                'latitude' => $columns[10],
                'longitude' => $columns[11],
                'population' => $columns[12],
            ]);
        }
    }
}
