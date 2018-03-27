<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\LogModel;

class DealerController extends Controller
{
    /**
     * Dealer model container
     *
     * @access Protected
     */
    protected $dealer;
    
    /**
     * Dealer Type model container
     *
     * @access Protected
     */
    protected $type;
    
    /**
     * Dealer Channel model container
     *
     * @access protected
     */
    protected $channel;

    /**
     * Dealer Account model container
     *
     * @access Protected
     */
    protected $dealer_account;

	/**
     * Region model container
     *
     * @access Protected
     */
    protected $region;

    /**
     * Branch model container
     *
     * @access Protected
     */
    protected $branch;
    
    /**
     * Region model container
     *
     * @access Protected
     */
    protected $division;
    
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
        $this->dealer   = new DealerModel();
        $this->type     = new DealerTypeModel();
        $this->channel  = new DealerChannelModel();
        $this->account  = new DealerAccountModel();
        $this->region   = new RegionModel();
        $this->branch   = new BranchModel();
        $this->log      = new LogModel();
    }
    
    /**
     * Get all region
     *
     * @access private
     * @return Array
     */
    private function _getAllRegion()
    {
        $result = $this->region->getAll();
        $data   = [
            '0' => '(none)'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }
    
    /**
     * Get all branch
     *
     * @access private
     * @return Array
     */
    private function _getAllBranch()
    {
        $result = $this->branch->getAll();
        $data   = [
            '0' => '(none)'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }
    
    /**
     * Get all dealer type
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerType()
    {
        $result = $this->type->getAll();
        $data   = [
            '0' => '(none)',
        ];
        
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
        $result = $this->channel->getAll();
        $data   = [
            '0' => '(none)',
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }


    /**
     * Get all dealer account
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerAccount()
    {
        $result = $this->account->getAll();
        $data   = [
            '0' => '(none)'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }

    /**
     * Show list of current dealer
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'dealers'           => $this->dealer->getAll(),
            'regions'           => $this->_getAllRegion(),
            'branch'            => $this->_getAllBranch(),
            'dealerChannels'    => $this->_getAllDealerChannel(),
            'dealerTypes'       => $this->_getAllDealerType(),
            'dealerAccounts'    => $this->_getAllDealerAccount(),
        ];
        
        return view('dealers.dealerIndex', $data);
    }
    
    /**
     * Render create dealer page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
		$data = [
            'branches'          => $this->_getAllBranch(),
            'dealerChannels'    => $this->_getAllDealerChannel(),
            'dealerTypes'       => $this->_getAllDealerType(),
            'dealerAccounts'    => $this->_getAllDealerAccount(),
        ];
		
        return view('dealers.dealerCreate', $data);
    }
    
    /**
     * Handle create dealer request from form
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
            'name'              => 'required|max:255',
            'address'           => 'required|max:255',
            'company'           => 'required|max:255',
            'code'              => 'required|max:255|unique:dealers,code',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Get branch id and region id
        $branchID   = (int) $request->get('branch_ID', 0);
        $regionID   = 0;
        
        $branch = $this->branch->getOne($branchID);
        
        if ($branch)
        {
            $regionID = $branch->region_ID;
        }
        
        // Set data
        $data = [
            'region_ID'         => $regionID,
            'branch_ID'         => $branchID,
            'dealer_account_ID' => (int) $request->get('dealer_account_ID', 0),
            'dealer_channel_ID' => (int) $request->get('dealer_channel_ID', 0),
            'dealer_type_ID'    => (int) $request->get('dealer_type_ID', 0),
            'code'              => $input['code'],
            'name'              => $input['name'],
            'company'           => $input['company'],
            'address'           => $input['address']
        ];
        
        // Save data
        $ID = $this->dealer->create($data);
        
        // Log request
        $action = 'Create a dealer (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-created', '');
        
        // Redirect to dealer index page
        return redirect('dealer');
    }
    
    /**
     * Render update dealer page
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
        
        // Get dealer
        $dealer = $this->dealer->getOne($ID);
        
        if (!$dealer)
        {
            return App::abort(404);
        }
        
        // Set data
        $data = [
            'branches'          => $this->_getAllBranch(),
            'dealerChannels'    => $this->_getAllDealerChannel(),
            'dealerTypes'       => $this->_getAllDealerType(),
            'dealerAccounts'    => $this->_getAllDealerAccount(),
            'dealer'            => $dealer,
        ];
        
        // Render page
        return view('dealers.dealerEdit', $data);
    }
    
    /**
     * Handle update dealer data request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Set input
        $input = $request->all();
        
        // Validate input
        $validator = Validator::make($input, [
            'ID'                => 'required|exists:dealers,ID',
            'name'              => 'required|max:255',
            'address'           => 'required|max:255',
            'company'           => 'required|max:255',
            'code'              => 'required|max:255|unique:dealers,code,'.$input['ID'].',ID',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Get branch id and region id
        $branchID   = (int) $request->get('branch_ID', 0);
        $regionID   = 0;
        
        $branch = $this->branch->getOne($branchID);
        
        if ($branch)
        {
            $regionID = $branch->region_ID;
        }
        
        // Set data
        $data = [
            'region_ID'         => $regionID,
            'branch_ID'         => $branchID,
            'dealer_account_ID' => (int) $request->get('dealer_account_ID', 0),
            'dealer_channel_ID' => (int) $request->get('dealer_channel_ID', 0),
            'dealer_type_ID'    => (int) $request->get('dealer_type_ID', 0),
            'code'              => $input['code'],
            'name'              => $input['name'],
            'company'           => $input['company'],
            'address'           => $input['address']
        ];
        
        // Save data
        $this->dealer->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a dealer (ID:'.$input['ID'].'|new_name:'.$input['name'].'|new_code:'.$input['code'].')';
        $this->log->record($request->userID, $action);
        
        
        // Add flash session
        $request->session()->flash('dealer-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove dealer page
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
        
        // Get dealer
        $dealer = $this->dealer->getOne($ID);
        
        if (!$dealer)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('dealers.dealerRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove dealer request
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
        
        // Get dealer
        $dealer = $this->dealer->getOne($ID);
        
        if (!$dealer)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->dealer->remove($ID);
        
        // Log request
        $action = 'Remove a dealer (ID:'.$ID.'|name:'.$dealer->name.'|code:'.$dealer->code.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-removed', '');
        
        // Redirect to dealer index page
        return redirect('dealer');
    }
}
