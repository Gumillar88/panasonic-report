<?php

namespace App\Http\Controllers\API\V_1_5_0;

use Validator;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;
use App\Http\Models\TokenModel;

class CompetitorController extends Controller
{
    /**
     * Competitor price model container
     *
     * @access protected
     */
    protected $price;
    
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $brand;
    
    /**
     * Product category model container
     *
     * @access protected
     */
    protected $category;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Token model container
     *
     * @access protected
     */
    protected $token;
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->brand    = new CompetitorBrandModel();
        $this->price    = new CompetitorPriceModel();
        $this->category = new ProductCategoryModel();
        $this->promotor = new PromotorModel();
        $this->token    = new TokenModel();
    }
    
    /**
     * Get list of competitor price based on date
     *
     * @access public
     * @param Request $request
     * @return Void
     */
    public function priceList(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        $date   = $request->get('date', date('Y-m-d'));
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Get data
        $data = $this->price->getListByPromotor($promotorID, $date);
        $finalData = [];
        
        
        // Compile data
        foreach ($data as $item)
        {
            $brand = $item->brand;
            
            if (!$brand)
            {
                $brand = $item->brand_custom;
            }
            
            $finalData[] = [
                'ID'        => $item->ID,
                'brand'     => $brand,
                'model'     => $item->model_name,
                'category'  => $item->category
            ];
        }
        
        return response()->json(['result' => $finalData]);
        
    }
    
    /**
     * Set competitor price data
     *
     * @access public
     * @param Request $request
     * @return Void
     */
    public function priceSet(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Validate input
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'ID'                => 'exists:competitor_prices,ID',
            'productCategoryID' => 'required|exists:product_categories,ID',
            'productModelID'    => 'exists:product_models,ID',
            'brandID'           => 'exists:competitor_brands,ID',
            'brandCustom'       => 'required_without:brandID|max:255',
            'modelName'         => 'required|max:255',
            'priceNormal'       => 'required',
            'pricePromo'        => 'required',
            'date'              => 'required_without:ID'
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['error' => 'data-error']);
        }
        
        // Set brand ID and custom brand
        $brandID        = $request->get('brandID', 0);
        $customBrand    = '';
        
        if ($brandID === 0)
        {
            $customBrand = $input['brandCustom'];
        }

        // Set data
        $data = [
            'competitor_brand_ID'       => $brandID,
            'competitor_brand_custom'   => $customBrand,
            'product_category_ID'       => $input['productCategoryID'],
            'product_model_ID'          => $request->get('productModelID', ''),
            'model_name'                => $input['modelName'],
            'price_normal'              => $input['priceNormal'],
            'price_promo'               => $input['pricePromo'],
        ];
        
        // Update data
        if (array_key_exists('ID', $input))
        {
            $this->price->update($input['ID'], $data);
        }
        else // Create data
        {
            // Set date
            $data['date'] = date('Y-m-d');

            if ($input['date'] != '1')
            {
                $data['date'] = date('Y-m-d', strtotime('-1 day'));
            }
            
            // Set promotor and dealer
            $data['promotor_ID']    = $promotor->ID;
            $data['dealer_ID']      = $promotor->dealer_ID;
        
            $this->price->create($data);
        }
        
        return response()->json(['result' => 'success']);
    }
    
    /**
     * Get competitor price data
     *
     * @access public
     * @param Request $request
     * @return Void
     */
    public function priceGet(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        $ID     = $request->get('ID', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        if (!$ID)
        {
            return response()->json(['error' => 'no-id']);
        }
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Validate price id
        $priceData = $this->price->getOne($ID);
        
        if (!$priceData) 
        {
            return response()->json(['error' => 'no-data']);
        }
        
        // Set data
        $data = [
            'brandID'           => $priceData->competitor_brand_ID,
            'brandCustom'       => $priceData->competitor_brand_custom,
            'productCatagoryID' => $priceData->product_category_ID,
            'productModelID'    => $priceData->product_model_ID,
            'modelName'         => $priceData->model_name,
            'priceNormal'       => $priceData->price_normal,
            'pricePromo'        => $priceData->price_promo,
        ];
            
        return response()->json(['result' => $data]);
    }
}