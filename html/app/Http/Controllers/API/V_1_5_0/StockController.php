<?php

namespace App\Http\Controllers\API\V_1_5_0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\TokenModel;
use App\Http\Models\ReportStockModel;

class StockController extends Controller
{
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
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
     * Report Stock model container
     *
     * @access protected
     */
    protected $stock;
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer   = new DealerModel();
        $this->promotor = new PromotorModel();
        $this->token    = new TokenModel();
        $this->stock    = new ReportStockModel();
    }
    
    /**
     * Render list index empty stock
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $timestamp  = $request->get('timestamp', time());
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);

        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Get dealer data
        $dealer = $this->dealer->getOne($promotor->dealer_ID);
        
        if(!$dealer)
        {
            return response()->json(['error' => 'no-dealer']);
        }

        // Get data
        $report_stock = $this->stock->getPagination($promotor->dealer_ID, ((int) $timestamp));

        return response()->json([
            'store'     => $dealer->name,
            'result'    => $report_stock
        ]);
    }
    
    /**
     * Create empty stock report
     *
     * @access public
     * @param Request $request
     * @return Reponse
     */
    public function create(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $productID  = $request->get('productID', false);

        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        if (!$productID)
        {
            return response()->json(['error' => 'no-product-id']);
        }
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }

        //check product already submit in delaer
        $data = $this->stock->getOne($productID, $promotor->dealer_ID);
        
        if ($data)
        {
            return response()->json(['error' => 'report-stock-data-already']);
        }

        // Save data
        $this->stock->create($promotor->ID, $promotor->dealer_ID, $productID);

        // Return success
        return response()->json(['result' => 'success']);
    }
    
    /**
     * Update empty stock report
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {   
        // Validate parameter
        $token      = $request->get('token', false);
        $productID  = $request->get('productID', false);

        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        if (!$productID)
        {
            return response()->json(['error' => 'no-product-id']);
        }
        
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }

        // Check product already submit in dealer
        $data = $this->stock->getOne($productID, $promotor->dealer_ID);
        
        if (!$data)
        {
            return response()->json(['error' => 'data-not-found']);
        }

        if($data->updated == 0)
        {
            // Save data
            $this->stock->update($data->ID, [
                'resolver_ID'   => $promotor->ID,
                'updated'       => time()
            ]);
        }

        // Return success
        return response()->json(['result' => 'success']);
    }
}