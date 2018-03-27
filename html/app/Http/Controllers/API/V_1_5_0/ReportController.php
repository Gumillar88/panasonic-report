<?php

namespace App\Http\Controllers\API\V_1_5_0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\ReportModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductPriceModel;
use App\Http\Models\TokenModel;
use App\Http\Models\BranchModel;
use App\Http\Models\RegionModel;
use App\Http\Models\ReportSaleModel;
use App\Http\Models\CustomerModel;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;

    /**
     * dealer account model container
     *
     * @access Protected
     */
    protected $dealer_account;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Report model container
     *
     * @access protected
     */
    protected $report;
    
    /**
     * Product Category model container
     *
     * @access protected;
     */
    protected $productCategory;
    
    /**
     * Product Type model container
     *
     * @access protected
     */
    protected $productType;
    
    /**
     * Product model container
     *
     * @access protected
     */
    protected $product;

    /**
     * Product price model container
     *
     * @access Protected
     */
    protected $product_price;
    
    /**
     * Token model container
     *
     * @access protected
     */
    protected $token;

    /**
     * Report No Sale model container
     *
     * @access protected
     */
    protected $report_sale;
    
    /**
     * Branch model container
     *
     * @access protected
     */
    protected $branch;
    
    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
    /**
     * Customer model container
     *
     * @access protected
     */
    protected $customer;
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer           = new DealerModel();
        $this->dealer_type      = new DealerTypeModel();
        $this->dealer_account   = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->report           = new ReportModel();
        $this->productCategory  = new ProductCategoryModel();
        $this->product          = new ProductModel();
        $this->product_price    = new ProductPriceModel();
        $this->token            = new TokenModel();
        $this->report_sale      = new ReportSaleModel();
        $this->branch           = new BranchModel();
        $this->region           = new RegionModel();
        $this->customer         = new CustomerModel();
    }
    
    
    /**
     * Handle create new report request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Validate parameter
        $productID              = $request->get('productID', 0);
        $dateValue              = $request->get('date', 0);
        $quantity               = $request->get('quantity', false);
        $token                  = $request->get('token', false);
        $custom_name            = $request->get('custom_name', '');
        $price                  = $request->get('price', false);
        $productCombinationID   = $request->get('productCombinationID', false);
        $customerPhone          = $request->get('customerPhone', '');
        $customerName           = $request->get('customerName', '');
        $customerGender         = $request->get('customerGender', '');
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        if (($productID === 0) && ($custom_name === ''))
        {
            return response()->json(['error' => 'no-product-id']);
        }
        
        if (!$quantity)
        {
            return response()->json(['error' => 'no-quantity']);
        }
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Set dealer
        $dealer     = $this->dealer->getOne($promotor->dealer_ID);

        // Get tl from branch
        $TL = $this->promotor->getOne($promotor->parent_ID);
        
        // Set date
        $date = Carbon::now();
        
        if ($dateValue == 0)
        {
            $date = Carbon::yesterday();
        }
        
        if(!$request->has('custom_name'))
        {
            // Validate product
            $product = $this->product->getOne($productID);
            
            if (!$product) 
            {
                return response()->json(['error' => 'no-product']);
            }

            // Check product price based on dealer id
            $price              = $product->price;
            $typePrice          = $this->product_price->getDealerProduct($promotor->dealer_ID, $productID, $dealer->dealer_channel_ID);
            
            if ($typePrice !== null)
            {
                $price  = $typePrice->price;
            }
            
        }
        
        // Get customer ID
        $customerID = 0;
        
        if ($customerPhone !== '') 
        {
            $customer = $this->customer->getByPhone($customerPhone);
            
            // Set customer
            if ($customer)
            {
                $customerID = $customer->ID;
            }
            else if (!$customer && $customerName !== '' && $customerGender !== '')
            {
                // Create new customer
                $customerID = $this->customer->create($customerName, $customerPhone, $customerGender);
            }
        }
        

        // Save data
        $this->report->create([
            'dealer_ID'         => $promotor->dealer_ID, 
            'promotor_ID'       => $promotorID, 
            'account_ID'        => $dealer->dealer_account_ID, 
            'tl_ID'             => $TL->ID, 
            'arco_ID'           => $TL->parent_ID, 
            'customer_ID'       => $customerID,
            'product_model_ID'  => $productID, 
            'custom_name'       => $custom_name, 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => $date
        ]);
        
        if($productCombinationID != 0)
        {
            $productCombination = $this->product->getOne($productCombinationID);
            
            if ($productCombination !== null) 
            {
                // Check product price based on dealer id
                $productCombinationPrice        = $productCombination->price;
                $productCombinationTypePrice    = $this->product_price->getDealerProduct($promotor->dealer_ID, $productCombinationID, $dealer->dealer_channel_ID);
                
                if ($productCombinationTypePrice !== null)
                {
                    $productCombinationPrice  = $productCombinationTypePrice->price;
                }
                
                $this->report->create([
                    'dealer_ID'         => $promotor->dealer_ID, 
                    'promotor_ID'       => $promotorID, 
                    'account_ID'        => $dealer->dealer_account_ID, 
                    'tl_ID'             => $TL->ID, 
                    'arco_ID'           => $TL->parent_ID, 
                    'customer_ID'       => $customerID,
                    'product_model_ID'  => $productCombinationID, 
                    'custom_name'       => '', 
                    'price'             => $productCombinationPrice, 
                    'quantity'          => $quantity, 
                    'date'              => $date
                ]);
            }
        }
        
        // Return success
        return response()->json(['result' => 'success']);
    }

    /**
     * Handle list of reports request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request)
    {
        // Validate parameter
        $date   = $request->get('date', false);
        $token  = $request->get('token', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        if (!$date)
        {
            return response()->json(['error' => 'no-date']);
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
            'dealer'    => $this->dealer->getOne($promotor->dealer_ID),
            'reports' => $this->report->getByDatePromotor($promotorID, $date)
        ];
        
        return response()->json(['result' => $data]);
    }


    /**
     * Get sales target
     * @access public
     * @param  Request $request 
     * @return Response
     */
    public function getSales(Request $request)
    {
        
        // Validate parameter
        $token  = $request->get('token', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }

        //initialite variable
        $totalTargetPromotor        = 0;
        $totalTargetDealer          = 0;
        $totalTargetPromotorData    = 0;
        $totalTargetDealerData      = 0;
        $comparisonData             = 0;
        $comparisonTarget           = 0;
        
        // Define date object
        $dateObject = Carbon::now();

        //set time YYYY-MM
        $time = $dateObject->format('Y-m');

        //set date YYYY-MM-01
        $date       = $dateObject->format('Y-m-01');
        $dateOnly   = $dateObject->day;
        $monthOnly  = $dateObject->month;
        $yearOnly   = $dateObject->year;

        // Set current date 
        $curDate = $dateObject->format('Y-m-d');
        
        // Decrement date object by 30 days
        $dateObject = $dateObject->subMonth();
        
        
        // Define last period
        $prevDateOnly           = $dateObject->day;
        
        // Subtract 1 more day for odd month compare with previous even month
        if ($dateOnly === 31 && $dateObject->day === 1)
        {
            $dateObject = $dateObject->subDay();
        }
        // Subtract remaining day for february - march
        else if ($dateOnly > 28 && $monthOnly === 3)
        {
            // Date subtraction
            $dateSub = 28;
            
            // Change date substraction in leap day
            if (($yearOnly%4) === 0)
            {
                $dateSub = 29;
            }
            
            $dateObject = $dateObject->subDay($dateOnly-$dateSub);
        }
        
        // Set day of the last month
        $lastMonth              = $dateObject->format('Y-m');
        $firstDayOfLastMonth    = $dateObject->format('Y-m-01');
        $dayOFLastMonth         = $dateObject->format('Y-m-d');
        
        // Compile promotor report for current month
        // DATA SALES MONTH PROMOTOR
        $promotorReportData = $this->report->getReportPromotorByMonth($promotor->ID, $time);
        
        //TARGET PROMOTOR
        $targetPromotor = $this->report->getByMonthTargetPromotor($promotor->ID, $time);
        $totalTargetPromotor = 0;
        
        if ($targetPromotor)
        {
            $totalTargetPromotor = $targetPromotor->total;
        }
        
        $targetPromotorCompiled = [];
        
        foreach ($promotorReportData as $key => $value) 
        {
            if (!array_key_exists($value->product_model_ID, $targetPromotorCompiled))
            {
                if (!$value->name) 
                {
                    $value->name                = $value->custom_name;
                    $value->product_model_ID    = $value->custom_name;
                }
                
                // Set compiled data
                $tempData = [
                    'product_ID'    => $value->product_model_ID,
                    'product_name'  => $value->name,
                    'price'         => $value->price,
                    'quantity'      => 0,
                ];

                // Push data to container
                $targetPromotorCompiled[$value->product_model_ID] = $tempData;
            }
            
            // Increment quantity
            $targetPromotorCompiled[$value->product_model_ID]['quantity'] += $value->quantity;
            
            // Count promotor data
            $totalTargetPromotorData += $value->quantity * $value->price;
        }
        
        /**
         * GET TARGET DEALER
         */
        $targetDealer = $this->report->getByMonthTargetDealer($promotor->dealer_ID, $time);
        
        foreach ($targetDealer as $key => $value) 
        {
            // Calculate price
            $totalTargetDealer +=  $value->total;
            
        }

        //get target dealer data
        $targetDealerData = $this->report->getByMonthTargetDealerData($promotor->dealer_ID, $date,$curDate);
        
        foreach ($targetDealerData as $item) 
        {
            $totalTargetDealerData += $item->price * $item->quantity;

        }

        /**
         * COMPARISON PRICE
         */
        $comparisonTarget = 1;
        $comparisonData = 0;
        
        //get target promotor last month
        $comparisonTargetPromotorLastMonth = $this->report->getByMonthTargetPromotor($promotor->ID, $lastMonth);
        
        if ($comparisonTargetPromotorLastMonth)
        {
            $comparisonTarget = $comparisonTargetPromotorLastMonth->total;
        }

        $comparisonTargetPromotorLastMonthData = $this->report->getByMonthComparisonByPromotor($promotor->ID, $firstDayOfLastMonth, $dayOFLastMonth);
        
        if ($comparisonTargetPromotorLastMonthData)
        {
            foreach ($comparisonTargetPromotorLastMonthData as $key => $value) 
            {   
                // Add quantity to compiled data and get price from dealer
                $comparisonData += $value->quantity * $value->price;
            }
            
        }
        
        $data= [
            'targetPromotorProduct' => $targetPromotorCompiled,
            'targetPromotor'        => $totalTargetPromotor,
            'targetPromotorData'    => $totalTargetPromotorData,
            'targetDealer'          => $totalTargetDealer,
            'targetDealerData'      => $totalTargetDealerData,
            'comparison'            => $comparisonTarget,
            'comparisonData'        => $comparisonData,
        ];
        
        /**
         * Return report
         */
        return response()->json(['result' => $data]);
    }

    /**
     * Check if no sales already reported
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function checkNoSales(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        
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

        // SET DATE
        $date = date('Y-m-d');

        // CHECK DATA ABSENCE
        $reportSaleData = $this->report_sale->getData($promotorID, $date);
        $reportExists   = 1;
        
        if(!$reportSaleData)
        {
            $reportExists = 0;
        }
        
        return response()->json(['result' => $reportExists]);
    }
    
    /**
     * Handle no sales report
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function handleNoSales(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        $date  = $request->get('date', false);
        
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

        // Date = 1 Today , 0 = Yesterday
        if ($date == 1)
        {
            $date = Carbon::now();
        }
        else if ($date == 0)
        {
            $date = Carbon::yesterday();
        }

        // GET TL ID , ARCO ID 
        $dealer_ID      = $promotor->dealer_ID;
        //Get one dealer
        $dealer         = $this->dealer->getOne($dealer_ID);
        
        $account_ID = 0;
        
        //Get ID account
        $dealer_account = $this->dealer_account->getOne($dealer->dealer_account_ID);
        
        if ($dealer_account)
        {
            $account_ID     = $dealer_account->ID;
        }
        
        $tl_ID          = $promotor->parent_ID;
        $tl             = $this->promotor->getOne($tl_ID);

        $arco_ID        = $tl->parent_ID;

        // Set data
        $data = [
            'promotor_ID'   => $promotor->ID,
            'dealer_ID'     => $dealer_ID,
            'account_ID'    => $account_ID,
            'tl_ID'         => $tl_ID,
            'arco_ID'       => $arco_ID,
            'date'          => $date,
        ];

        //CHECK DATA ABSENCE
        $this->report_sale->create($data);
        
        return response()->json(['result' => 'success']);
    }

}