<?php

namespace App\Http\Controllers\API\V_1_5_0;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\DealerModel;
use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\TokenModel;

class GeneralController extends Controller
{
    /**
     * Product Category model container
     *
     * @access protected;
     */
    protected $productCategory;
    
    /**
     * Product model container
     *
     * @access protected
     */
    protected $product;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $competitorBrand;
    
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
        $this->productCategory  = new ProductCategoryModel();
        $this->product          = new ProductModel();
        $this->promotor         = new PromotorModel();
        $this->dealer           = new DealerModel();
        $this->competitorBrand  = new CompetitorBrandModel();
        $this->token            = new TokenModel();
    }
    
    /**
     * Handle request to get latest version of data
     * check if there is new data froms server
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function checkData(Request $request)
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
        
        // Get the latest data
        $timestamps = [
            $this->productCategory->getLatestTimestamp(),
            $this->product->getLatestTimestamp(),
            $this->competitorBrand->getLatestTimestamp()
        ];
        
        // Sort timestamp
        rsort($timestamps);
        
        return response()->json(['version' => $timestamps[0]]);
    }

    /**
     * Handle request to get date
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function checkDate(Request $request)
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
        
        $time = Carbon::now();
        
        $timeResult = true;

        if($time->hour >= 0 && $time->hour <= 9)
        {
            $timeResult = false;
            
            // Only on monday, sending limited until 3 AM
            if ($time->dayOfWeek == 1 && $time->hour > 3)
            {
                $timeResult = true;
            }
        }
        
        return response()->json(['time' => $timeResult]);
    }
    
    /**
     * Get general data for the app
     * List data generated
     * - Division
     * - Product Category
     * - Product
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getData(Request $request)
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
        
        // Get data
        $data = [
            'productCategories' => $this->productCategory->getAll(),
            'products'          => $this->product->getAll(),
            'competitorBrands'  => $this->competitorBrand->getAll()
        ];
        
        return response()->json(['result' => $data]);
    }
    
    /**
     * Handle profile data request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request)
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
        
        // Get dealer name
        $dealerName = '(none)';
        
        if ($promotor->dealer_ID != 0)
        {
            $dealer = $this->dealer->getOne($promotor->dealer_ID);
            $dealerName = $dealer->name;
        }
        
        
        // Get data
        $data = [
            'name'      => $promotor->name,
            'dealerID'  => $promotor->dealer_ID,
            'dealerName'=> $dealerName
        ];
        
        return response()->json(['result' => $data]);
    }
    
}