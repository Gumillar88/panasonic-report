<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductIncentiveModel;
use App\Http\Models\LogModel;

class ProductIncentiveController extends Controller
{
    /**
     * Dealer channel model container
     *
     * @access Protected
     */
    protected $dealerChannel;
    
    /**
     * Product model container
     *
     * @access protected
     */
    protected $product;
    
    /**
     * Product incentive model container
     *
     * @access protected
     */
    protected $incentive;

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
        $this->dealerChannel    = new DealerChannelModel();
        $this->product          = new ProductModel();
        $this->incentive        = new ProductIncentiveModel();
        $this->log              = new LogModel();
    }
    
    /**
     * Get all dealer channel
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerChannel()
    {
        $result = $this->dealerChannel->getAll();
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
    private function _getAllProductModel()
    {
        $result = $this->product->getAll();
        $data   = [];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }
    
    /**
     * Show list of current product incentive
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'dealerChannels'    => $this->_getAllDealerChannel(),
            'productModels'     => $this->_getAllProductModel(),
            'productIncentives' => $this->incentive->getAll()
        ];

        return view('product-incentive.productIncentiveIndex',$data);
    }
    
    /**
     * Render create product incentive page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
        $data = [
            'productModels'     => $this->_getAllProductModel(),
            'dealerChannels'    => $this->_getAllDealerChannel()
        ];

        return view('product-incentive.productIncentiveCreate',$data);
    }
    
    /**
     * Handle create product incentive request
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
            'dealer_channel_ID' => 'required|exists:dealer_channels,ID',
            'product_model_ID'  => 'required|exists:product_models,ID',
            'value'             => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Check existing incentive data before saving the new one
        $incentive = $this->incentive->getByChannelModel(
            $input['dealer_channel_ID'], 
            $input['product_model_ID']
        );
        
        // Redirect to edit page
        if ($incentive !== null) 
        {
            $request->session()->flash('product-incentive-exist', '');
            return redirect('product/incentive/edit?ID='.$incentive->ID);
        }
        
        // Save data
        $ID = $this->incentive->create(
            $input['dealer_channel_ID'], 
            $input['product_model_ID'], 
            $input['value']
        );
        
        // Log request
        $action = 'Create a product incentive (ID:'.$ID.'|value:'.$input['value'].'|dealer_channel_ID:'.$input['dealer_channel_ID'].'|product:'.$input['product_model_ID'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-incentive-created', '');
        
        // Redirect to product incentive index page
        return redirect('product/incentive');
    }
    
    /**
     * Render update product incentive page
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
        
        // Get product incentive
        $incentive = $this->incentive->getOne($ID);
        
        if (!$incentive)
        {
            return App::abort(404);
        }
        
        // Set data
        $data = [
            'incentive'         => $incentive,
            'productModels'     => $this->_getAllProductModel(),
            'dealerChannels'    => $this->_getAllDealerChannel()
        ];
        
        // Render page
        return view('product-incentive.productIncentiveEdit', $data);
    }
    
    /**
     * Handle update product incentive data request
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
            'ID'                => 'required|exists:product_incentives,ID',
            'dealer_channel_ID' => 'required|exists:dealer_channels,ID',
            'product_model_ID'  => 'required|exists:product_models,ID',
            'value'             => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'dealer_channel_ID' => $input['dealer_channel_ID'],
            'product_model_ID'  => $input['product_model_ID'],
            'value'             => $input['value'],
        ];
        
        // Save data
        $this->incentive->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a product incentive (ID:'.$input['ID'].'|value:'.$input['value'].'|dealer_channel_ID:'.$input['dealer_channel_ID'].'|product:'.$input['product_model_ID'].')';
        $this->log->record($request->userID, $action);
        
        
        // Add flash session
        $request->session()->flash('product-incentive-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove product incentive page
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
        
        // Get product incentive
        $incentive = $this->incentive->getOne($ID);
        
        if (!$incentive)
        {
            return App::abort(404);
        }
        
        $data = [
            'incentive'         => $incentive,
            'dealerChannels'    => $this->_getAllDealerChannel(),
            'productModels'     => $this->_getAllProductModel(),
        ];
        
        // Render page
        return view('product-incentive.productIncentiveRemove', $data);
    }
    
    /**
     * Handle remove product incentive request
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
        
        // Get product incentive
        $incentive = $this->incentive->getOne($ID);
        
        if (!$incentive)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->incentive->remove($ID);
        
        // Log request
        $action = 'Remove a product incentive (ID:'.$ID.'|value:'.$incentive->value.'|dealer_type_ID:'.$incentive->dealer_channel_ID.'|product:'.$incentive->product_model_ID.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-incentive-removed', '');
        
        // Redirect to product incentive page
        return redirect('product/incentive');
    }

}