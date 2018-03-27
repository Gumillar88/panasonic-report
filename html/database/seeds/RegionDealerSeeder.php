<?php

use Illuminate\Database\Seeder;

class RegionDealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regions')->truncate();
        DB::table('dealers')->truncate();
        
        // Read file
        $regionDealerJSON = Storage::get('region-dealer.json');
        $regionDealerData = json_decode($regionDealerJSON, true);
        
        foreach ($regionDealerData as $region => $data)
        {
            // Set time
            $time = time();

            // Insert region
            $regionID = DB::table('regions')->insertGetId([
                'name'      => $region,
                'created'   => $time,
                'updated'   => $time
            ]);

            // Insert dealer
            $dealerData = [];

            foreach ($data as $dealer) 
            {
                // Set time
                $time = time();

                $dealerData[] = [
                    'region_ID' => $regionID,
                    'code'      => $dealer['code'],
                    'name'      => $dealer['name'],
                    'created'   => $time,
                    'updated'   => $time,
                ];
            }

            DB::table('dealers')->insert($dealerData);
        }
    }
}
