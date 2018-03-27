<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductPriceModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\LogModel;
use PHPExcel_Reader_Excel2007;

class ProductPriceController extends Controller
{
    /**
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;

    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealer_channel;
    
    /**
     * Product model container
     *
     * @access Protected
     */
    protected $product_model;

    /**
     * Product price model container
     *
     * @access Protected
     */
    protected $product_price;

    /**
     * Log model container
     *
     * @access protected
     */
    protected $log;
	
    /**
     * Class constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer_type      = new DealerTypeModel();
        $this->product_model    = new ProductModel();
        $this->product_price    = new ProductPriceModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->log              = new LogModel();
    }
    
    /**
     * Get all dealer type
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerType()
    {
        $result = $this->dealer_type->getAll();
        $data   = [];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }

    /**
     * Get all dealer channel
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerChannel()
    {
        $result = $this->dealer_channel->getAll();
        $data   = [];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }

    /**
     * Get all product 
     *
     * @access private
     * @return Array
     */
    private function _getAllProduct()
    {
        $result = $this->product_model->getAll();
        $data   = [];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }
    
    /**
     * Show list of current product price
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'product_prices'    => $this->product_price->getAll(),
            'products'          => $this->_getAllProduct(),
            'dealer_types'      => $this->_getAllDealerType(),
            'dealer_channels'   => $this->_getAllDealerChannel()
        ];

        return view('product-price.productPriceIndex',$data);
    }
    
    /**
     * Render create product price page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
        $data = [
            'products'          => $this->_getAllProduct(),
            'dealer_types'      => $this->_getAllDealerType(),
            'dealer_channels'   => $this->_getAllDealerChannel()
        ];

        return view('product-price.productPriceCreate',$data);
    }
    
    /**
     * Handle create product price request from form
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function createHandle(Request $request)
    {
        // Set input
        $input = $request->all();
        
        // Validate input
        $validator = Validator::make($input, [
            'dealer_type_ID'    => 'required|exists:dealer_types,ID',
            'dealer_channel_ID' => 'required|exists:dealer_channels,ID',
            'product_ID'        => 'required|exists:product_models,ID',
            'price'             => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->product_price->create($input['dealer_type_ID'], $input['dealer_channel_ID'], $input['product_ID'], $input['price']);
        
        // Log request
        $action = 'Create a product price (ID:'.$ID.'|price:'.$input['price'].'|dealer_type_ID:'.$input['dealer_type_ID'].'|dealer_channel_ID:'.$input['dealer_channel_ID'].'|product:'.$input['product_ID'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-price-created', '');
        
        // Redirect to product price index page
        return redirect('product/price');
    }
    
    /**
     * Render update product price page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateRender(Request $request)
    {
        // Set input
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get product price
        $product_price = $this->product_price->getOne($ID);
        
        if (!$product_price)
        {
            return App::abort(404);
        }
        
        // Set data
        $data = [
            'product_price'        => $product_price,
            'products'          => $this->_getAllProduct(),
            'dealer_types'      => $this->_getAllDealerType(),
            'dealer_channels'   => $this->_getAllDealerChannel()
        ];
        
        // Render page
        return view('product-price.productPriceEdit', $data);
    }
    
    /**
     * Handle update product price data request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        // Set input
        $input = $request->all();
        
        // Validate input
        $validator = Validator::make($input, [
            'ID'                => 'required|exists:product_price,ID',
            'dealer_type_ID'    => 'required|exists:dealer_types,ID',
            'dealer_channel_ID' => 'required|exists:dealer_channels,ID',
            'product_ID'        => 'required|exists:product_models,ID',
            'price'             => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'dealer_type_ID' => $input['dealer_type_ID'],
            'dealer_channel_ID' => $input['dealer_channel_ID'],
            'product_ID'     => $input['product_ID'],
            'price'          => $input['price'],
        ];
        
        // Save data
        $this->product_price->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a product price (price:'.$input['price'].'|dealer_type_ID:'.$input['dealer_type_ID'].'|dealer_channel_ID:'.$input['dealer_channel_ID'].'|product:'.$input['product_ID'].')';
        $this->log->record($request->userID, $action);
        
        
        // Add flash session
        $request->session()->flash('product-price-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove product price page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeRender(Request $request)
    {
        // Set input
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get product price
        $product_price = $this->product_price->getOne($ID);
        
        if (!$product_price)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('product-price.productPriceRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove product price request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeHandle(Request $request)
    {
        // Set input
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get product price
        $product_price = $this->product_price->getOne($ID);
        
        if (!$product_price)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->product_price->remove($ID);
        
        // Log request
        $action = 'Remove a dealer type(ID:'.$ID.'|price:'.$product_price->price.'|dealer_type_ID:'.$product_price->dealer_type_ID.'|product:'.$product_price->product_ID.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-price-removed', '');
        
        // Redirect to product price page
        return redirect('product/price');
    }

    /**
     * Render upload Product
     * @access public
     * @return Response
     */
    public function uploadRender()
    {   
        $data = [
            'type' => 'normal'
        ];
        
        return view('product-price.productPriceUpload', $data);
    }

    /**
     * Handle upload Product
     * @access public
     * @return Response
     */
    public function uploadHandle(Request $request)
    {
        // Set input
        $input = $request->all();
        
        // Set rules
        $rules = [
            'excel' => 'required|mimes:xlsx' 
        ];
        
        // Set messages
        $message = [
            'mimes' => 'Excel file must be in Microsoft Excel 2007 file format.'
        ];
        
        // Validate parameter
        $validator = Validator::make($input, $rules, $message);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator);
        }
        
        // Create excel reader
        $objReader = new PHPExcel_Reader_Excel2007();
        $objReader->setReadDataOnly(true);
        
        // Get active sheet
        $objPHPExcel    = $objReader->load($request->file('excel')->getPathName());
        $sheetCount     = $objPHPExcel->getSheetCount();
        $sheetNames     = $objPHPExcel->getSheetNames();
        
        // Loop rows to get data
        $rows       = [];
        
        foreach ($sheetNames as $i => $sheetName)
        { 
            if (!in_array($sheetName, ['R1', 'R2', 'R3', 'R4']))
            {
                continue;
            }
            
            $sheet      = $objPHPExcel->setActiveSheetIndex($i);
            $rowValue   = 12;
            $rowLoop    = true;
            $rowEnd     = 0;
            
            while($rowLoop)
            {
                // Set temporary container
                $tempRow = [];
                $rowProcess = true;
                
                // Get value from column
                $productName = $sheet->getCell('B'.$rowValue)->getValue();
                $price_MUP = $sheet->getCell('E'.$rowValue)->getValue();
                $price_SO = $sheet->getCell('J'.$rowValue)->getValue();
                $price_SMO = $sheet->getCell('O'.$rowValue)->getValue();

                // If value not found, then stop the loop
                if ($productName === null && $price_MUP === null && $price_SO === null)
                {
                    $rowEnd++;
                    $rowProcess = false;
                }
                else
                {
                    //not empty row
                    $rowEnd = 0;
                }

                if ($rowEnd === 10)
                {
                    $rowLoop = false;
                    break;
                }

                $tempRow = [
                    'product'   => $productName,
                    'price_MUP' => (int) $price_MUP,
                    'price_SO'  => (int) $price_SO,
                    'price_SMO' => 0, // Default value for SMO
                    'dealerType'=> $sheetNames[$i],
                ];
                
                if ($price_SMO !== null)
                {
                    $tempRow['price_SMO'] = (int) $price_SMO;
                }
                
                // Put temporary container to rows if the loop still continue
                if ($rowLoop === true && $rowProcess === true)
                {
                    $rows[] = $tempRow;
                }
                $rowValue++;
            }
        }


        // validate data
        foreach ($rows as $key => $value) 
        {
            //remove data if price is empty
            if ($value['price_MUP'] == 0 && $value['price_SO'] == 0)
            {
                unset($rows[$key]);
            }
            else
            {

                //remove space
                $value['product'] = str_replace(' ', '', $value['product']);

                // check product name have '/'
                if(strpos($value['product'], '/') !== false) 
                {
                    // case 1 CS/CU
                    if(strpos($value['product'], 'CS/CU') !== false) 
                    {
                        $productName = explode('/', $value['product']);
                        $rowEdit = false;

                        foreach ($productName as $total => $row) 
                        {
                            if($total == 0 )
                            {
                                // cs
                                $rows[] = [
                                    'product'   => $row.substr($productName[1], 2),
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                            else
                            {   
                                //cu
                                $rows[] = [
                                    'product'   => $row,
                                    'price_MUP' => 0,
                                    'price_SO'  => 0,
                                    'price_SMO' => 0,
                                    'dealerType'=> $value['dealerType'],
                                ];

                                $rowEdit = true;
                            }
                        }
                    }
                    // case 2 DMC
                    else if(strpos($value['product'], 'DMC') !== false) 
                    {
                        $productName = explode('/', $value['product']);
                        $rowEdit = false;
                        $tempProduct = '';

                        foreach ($productName as $total => $row) 
                        {
                            if($total == 0 )
                            {
                                $tempProduct = $productName[0];
                                $rows[] = [
                                    'product'   => $row,
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                            else
                            {   
                                if(strlen($row) > 1)
                                {
                                    $tempProduct = $row;
                                    $rows[] = [
                                        'product'   => $row,
                                        'price_MUP' => $value['price_MUP'],
                                        'price_SO'  => $value['price_SO'],
                                        'price_SMO' => $value['price_SMO'],
                                        'dealerType'=> $value['dealerType'],
                                    ];
                                    $rowEdit = true;
                                }
                                else
                                {
                                    $rows[] = [
                                        'product'   => substr($tempProduct, 0,-1).$row,
                                        'price_MUP' => $value['price_MUP'],
                                        'price_SO'  => $value['price_SO'],
                                        'price_SMO' => $value['price_SMO'],
                                        'dealerType'=> $value['dealerType'],
                                    ];
                                    $rowEdit = true;
                                    
                                }

                            }
                        }
                    }
                    //case 3
                    else if(strpos($value['product'], '/-') !== false) 
                    {
                        $productName = explode('/', $value['product']);
                        $rowEdit = false;

                        $subName = explode('-', $productName[0]);

                        foreach ($productName as $total => $row) 
                        {
                            if($total == 0 )
                            {
                                $rows[] = [
                                    'product'   => $row,
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                            else
                            {   
                                $rows[] = [
                                    'product'   => $subName[0].$row.substr($subName[1], 1),
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                        }
                    }
                    //case 4
                    else if(strpos($value['product'], 'EW-') !== false) 
                    {
                        $productName = explode('/', $value['product']);
                        $rowEdit = false;

                        $nameLast = $productName[count($productName)-1];

                        // EW-DS11-D/K/P/R/
                        // 
                        // S401 
                        foreach ($productName as $total => $row) 
                        {
                            if($total == 0 )
                            {
                                $rows[] = [
                                    'product'   => $row.substr($nameLast, 1),
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                            else if ($total === count($productName) -1)
                            {   
                                $rows[] = [
                                    'product'   => substr($productName[0],0,-1).$row,
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                            else
                            {
                                $rows[] = [
                                    'product'   => substr($productName[0],0,-1).$row.substr($nameLast, 1),
                                    'price_MUP' => $value['price_MUP'],
                                    'price_SO'  => $value['price_SO'],
                                    'price_SMO' => $value['price_SMO'],
                                    'dealerType'=> $value['dealerType'],
                                ];
                                $rowEdit = true;
                            }
                        }
                    }
                    else
                    // case 5
                    {
                        $productName    = explode('/', $value['product']);
                        $count          = count($productName);
                        $rowEdit        = false;
                        if($count > 1)
                        {
                            $data = [];
                            foreach ($productName as $total => $row) 
                            {
                                if($total == 0 )
                                {
                                    $rows[] = [
                                        'product'   => $row,
                                        'price_MUP' => $value['price_MUP'],
                                        'price_SO'  => $value['price_SO'],
                                        'price_SMO' => $value['price_SMO'],
                                        'dealerType'=> $value['dealerType'],
                                    ];
                                    $rowEdit = true;
                                }
                                else
                                {
                                    $rows[] = [
                                        'product'   => substr($productName[0], 0,'-'.strlen($row)).$row,
                                        'price_MUP' => $value['price_MUP'],
                                        'price_SO'  => $value['price_SO'],
                                        'price_SMO' => $value['price_SMO'],
                                        'dealerType'=> $value['dealerType'],
                                    ];
                                    $rowEdit = true;
                                }

                            }
                            //end foreach
                            
                        }
                        //end if
                        
                    }
                    //check already exploded
                    if($rowEdit === true)
                    {
                        unset($rows[$key]);
                    }
                    

                }
            }


        }

        // Set render data
        $data = [
            'type'          => 'review',
            'rows'          => $rows,
        ];


        return view('product-price.productPriceUpload', $data);

    }
    /**
     * Handle upload product single request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function uploadSingle(Request $request)
    {
        // Wrap input
        $input = $request->all();
        
        // Set rules
        $rules = [
            'product'       => 'required',
            'dealerType'    => 'required',
            'price_MUP'     => 'required',
            'price_SO'      => 'required',
            'price_SMO'     => 'required',
        ];
        
        // Set messages
        $messages = [
            'product.required'      => 'General error',
            'dealerType.required'   => 'General error',
            'price_MUP.required'    => 'General error',
            'price_SO.required'     => 'General error',
            'price_SMO.required'    => 'General error',
        ];
        
        // Validate parameter
        $validator = Validator::make($input, $rules, $messages);
        
        if ($validator->fails())
        {
            // Grab first message of the errors.
            $errors     = json_decode($validator->errors(), true);
            $response   = reset($errors)[0];
            
            // Return response
            return response()->json($response);
        }

        // Remove space on product name
        $input['product'] = str_replace(' ', '', $input['product']);
        
        // get dealer type
        $dealerType     = $this->dealer_type->getDealertType($input['dealerType']);
        
        // Check if dealer type exist or not
        if(!$dealerType)
        {
            return response()->json('General error');   
        }
        
        // Get dealer channel and prepare container for it
        $dealerChannel  = $this->dealer_channel->getAll();
        $compiledChannel = [];

        foreach ($dealerChannel as $key => $value) 
        {
           if(!array_key_exists($value->name, $compiledChannel))
           {
                $compiledChannel[$value->name] = $value->ID;
           }
        }
        
        // Get product data
        $product    = $this->product_model->getByProductName($input['product']);
        
        // Set default response
        $response = 'Updated';
        
        // If there is no product create new product and change response
        if(!$product)
        {
            $productID  = $this->product_model->create(0, $input['product'], 0);
            $response   = 'Created';
        }
        else
        {
            $productID  = $product->ID;
        }
        
        /**
         * Check price SO
         */
        $productPriceSO = $this->product_price->getDealerProductPrice(
            $dealerType->ID, 
            $compiledChannel['SO'],
            $productID
        );
        
        // If there is no price for SO create new price
        if(!$productPriceSO)
        {
            // Save data
            $this->product_price->create($dealerType->ID, $compiledChannel['SO'], $productID, $input['price_SO']);
            
            // Set response
            $response .= ' (SO new price)';
        }
        else // Or update it
        {
            // Only update if price different
            if ($productPriceSO->price != $input['price_SO'] && $input['price_SO'] > 0)
            {
                // Update
                $this->product_price->update($productPriceSO->ID, [
                    'price' => $input['price_SO']
                ]);
                
                // Record log
                $this->product_price->log(
                    $productID, 
                    $dealerType->ID, 
                    $compiledChannel['SO'],
                    $productPriceSO->price,
                    $input['price_SO']
                );
                
                // Create response
                $response .= ' (SO '.number_format($productPriceSO->price).' to '.number_format($input['price_SO']).')';
            }
            else
            {
                $response .= ' (SO price same)';
            }
        }
        
        if ($dealerType->name === 'R2')
        {
            /**
             * Check price SMO
             */
            $productPriceSMO = $this->product_price->getDealerProductPrice(
                $dealerType->ID, 
                $compiledChannel['SMO'],
                $productID
            );

            // If there is no price for SO create new price
            if(!$productPriceSMO)
            {
                // Save data
                $this->product_price->create($dealerType->ID, $compiledChannel['SMO'], $productID, $input['price_SMO']);

                // Set response
                $response .= ' (SMO new price)';
            }
            else // Or update it
            {
                // Only update if price different
                if ($productPriceSMO->price != $input['price_SMO'] && $input['price_SMO'] > 0)
                {
                    // Update
                    $this->product_price->update($productPriceSMO->ID, [
                        'price' => $input['price_SMO']
                    ]);

                    // Record log
                    $this->product_price->log(
                        $productID, 
                        $dealerType->ID, 
                        $compiledChannel['SMO'],
                        $productPriceSMO->price,
                        $input['price_SMO']
                    );

                    // Create response
                    $response .= ' (SMO '.number_format($productPriceSMO->price).' to '.number_format($input['price_SMO']).')';
                }
                else
                {
                    $response .= ' (SMO price same)';
                }
            }
        }
        
        /**
         * Check price MUP
         */
        $productPriceMUP = $this->product_price->getDealerProductPrice(
            $dealerType->ID, 
            $compiledChannel['MUP'], 
            $productID
        );
        
        // If there is no price for MUP create new price
        if(!$productPriceMUP)
        {
            // Save data
            $this->product_price->create($dealerType->ID, $compiledChannel['MUP'], $productID, $input['price_MUP']);
            
            // Set response
            $response .= ' (MUP new price)';
        }
        else // Or update it
        {
            // Only update if price different
            if ($productPriceMUP->price != $input['price_MUP'] && $input['price_MUP'] > 0)
            {
                // Update data
                $this->product_price->update($productPriceMUP->ID, [
                    'price' => $input['price_MUP']
                ]);
                
                // Record log
                $this->product_price->log(
                    $productID, 
                    $dealerType->ID, 
                    $compiledChannel['MUP'],
                    $productPriceMUP->price,
                    $input['price_MUP']
                );
                
                // Create response
                $response .= ' (MUP '.number_format($productPriceMUP->price).' to '.number_format($input['price_MUP']).')';
            }
            else
            {
                $response .= ' (MUP price same)';
            }
        }
        
        return response()->json($response);
    }
}
    