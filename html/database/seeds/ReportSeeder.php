<?php

use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reports')->truncate();
        
        // Read file
        $reportJSON = Storage::get('report.json');
        $reportData = json_decode($reportJSON, true);
        
        // Collect the data and id
        $promotors = DB::table('promotors')->get();
        
        // Make key and value array
        $promotorIDMap = [];
        
        foreach ($promotors as $promotor) 
        {
            $promotorIDMap[$promotor->phone] = $promotor->ID;
        }
        
        /**
         * Compile all product code and id
         */
        $products       = DB::table('products')->get();
        $productIDMap   = [];
        
        foreach ($products as $product)
        {
            $productIDMap[$product->code] = $product->ID;
        }
        
        
        // Compile all reports data and promotor
        $time       = time();
        
        $reports = [];
        
        foreach ($reportData as $key => $report)
        {
            if (array_key_exists('number', $report)) 
            {

                if (
                    array_key_exists($report['number'], $promotorIDMap) && 
                    array_key_exists($report['productCode'], $productIDMap)
                )
                {
                    echo $key."\r\n";
                    
                    $tempData = [
                        'promotor_ID'   => $promotorIDMap[$report['number']],
                        'product_ID'    => $productIDMap[$report['productCode']],
                        'quantity'      => $report['quantity'],
                        'date'          => date('Y-m-d', strtotime($report['date'])),
                        'created'       => $time,
                        'updated'       => $time,
                    ];
                    
                    DB::table('reports')->insert($tempData);
                    
                }
            }
            
        }
        
    }
}
