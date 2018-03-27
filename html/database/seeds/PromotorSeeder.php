<?php

use Illuminate\Database\Seeder;

class PromotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('promotors')->truncate();
        
        // Read file
        $promotorJSON = Storage::get('promotor.json');
        $promotorData = json_decode($promotorJSON, true);
        
        // Compile all promotor data with dealer id
        $time  = time();
        
        $duplicateNumbers = [
            '+6281368048800', 
            '+6285782578350', 
            '+6281315627557', 
            '+6283877869366', 
            '+622198535760', 
            '+6287777083741', 
            '+6285781641524', 
            '+6281312284818'
        ];
        
        foreach ($promotorData as $key => $promotor)
        {
            if (!array_key_exists('number', $promotor)) 
            {
                continue;
            }
            
            if (in_array($promotor['number'], $duplicateNumbers))
            {
                continue;
            }
            
            echo $key."\r\n";
            $data = [
                'dealer_ID' => 0,
                'phone'     => $promotor['number'],
                'password'  => Hash::make(str_replace('-', '', $promotor['birthday'])),
                'name'      => $promotor['name'],
                'created'   => $time,
                'updated'   => $time,
            ];

            DB::table('promotors')->insert($data);
            
        }
        
    }
}
