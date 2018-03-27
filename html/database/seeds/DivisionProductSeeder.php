<?php

use Illuminate\Database\Seeder;

class DivisionProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_categories')->truncate();
        DB::table('product_models')->truncate();
        
        // Read file
        $categoryProductJSON = Storage::get('category-product.json');
        $categoryProductData = json_decode($categoryProductJSON, true);
        
        $priceProductJSON = Storage::get('price-product.json');
        $priceProductData = json_decode($priceProductJSON, true);
        
        foreach ($categoryProductData as $category => $data) 
        {
            $time = time();

            // Insert category
            $categoryID = DB::table('product_categories')->insertGetId([
                'name'          => $category,
                'created'       => $time,
                'updated'       => $time,
            ]);

            // Set product
            $productData = [];

            foreach ($data as $product) 
            {
                $time = time();
                
                $price = 0;
                
                if (array_key_exists($product['name'], $priceProductData))
                {
                    $price = (int) $priceProductData[$product['name']];
                }

                $productData[] = [
                    'product_category_ID'   => $categoryID,
                    'name'                  => $product['name'],
                    'subcategory'           => '',
                    'price'                 => $price,
                    'color'                 => '',
                    'created'               => $time,
                    'updated'               => $time
                ];
            }

            DB::table('product_models')->insert($productData);

        }
    }
}
