<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Station;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            [
                'StationID' => 'ST001',
                'StationName' => 'KL Sentral',
                'Location' => 'Kuala Lumpur, WP Kuala Lumpur',
                'Is_active' => true,
                'Created_at' => now()
            ],
            [
                'StationID' => 'ST002',
                'StationName' => 'Butterworth',
                'Location' => 'Butterworth, Penang',
                'Is_active' => true,
                'Created_at' => now()
            ],
            [
                'StationID' => 'ST003',
                'StationName' => 'Ipoh',
                'Location' => 'Ipoh, Perak',
                'Is_active' => true,
                'Created_at' => now()
            ],
            [
                'StationID' => 'ST004',
                'StationName' => 'Johor Bahru Sentral',
                'Location' => 'Johor Bahru, Johor',
                'Is_active' => true,
                'Created_at' => now()
            ]
        ];

        foreach ($stations as $station) {
            Station::updateOrCreate(
                ['StationID' => $station['StationID']],
                $station
            );
        }
    }
}
