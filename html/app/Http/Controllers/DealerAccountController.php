<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\BranchModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\LogModel;

class DealerAccountController extends Controller
{
    /**
     * Dealer account model container
     *
     * @access Protected
     */
    protected $dealer_account;

    /**
     * Branch model container
     *
     * @access Protected
     */
    protected $branch;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
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
        $this->dealer_account   = new DealerAccountModel();
        $this->branch           = new BranchModel();
        $this->promotor         = new PromotorModel();
        $this->log              = new LogModel();
    }
    
    /**
     * Get all promotor which has TL type
     *
     * @access private
     * @return Array
     */
    private function _getAllTl()
    {
        $result = $this->promotor->getByType('tl');
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
     * Show list of current dealer account
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'dealer_accounts'   => $this->dealer_account->getAll(),
            'branch'            => $this->_getAllBranch(),
            'dataTl'            => $this->_getAllTl()
        ];

        return view('dealer-account.dealerAccountIndex',$data);
    }
    
    /**
     * Render create dealer account page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {
        $data = [
            'dataBranch'    => $this->_getAllBranch(),
            'dataTl'        => $this->_getAllTl()
        ];
        return view('dealer-account.dealerAccountCreate',$data);
    }
    
    /**
     * Handle create dealer account request from form
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
            'name'          => 'required|max:255',
            'branch_ID'     => 'required|exists:branches,ID',
            'promotor_ID'   => 'required|exists:promotors,ID',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->dealer_account->create($input['name'], $input['branch_ID'], $input['promotor_ID']);
        
        // Log request
        $action = 'Create a dealer account (ID:'.$ID.'|name:'.$input['name'].'|branch_ID:'.$input['branch_ID'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-account-created', '');
        
        // Redirect to dealer account index page
        return redirect('dealer-account');
    }
    
    /**
     * Render update dealer account page
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
        
        // Get dealer type
        $dealer_account = $this->dealer_account->getOne($ID);
        
        if (!$dealer_account)
        {
            return App::abort(404);
        }

        // Set data
        $data = [
            'dealer_account'    => $dealer_account,
            'dataBranch'        => $this->_getAllBranch(),
            'dataTl'            => $this->_getAllTl()
        ];
        
        // Render page
        return view('dealer-account.dealerAccountEdit', $data);
    }
    
    /**
     * Handle update dealer type data request
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
            'ID'            => 'required|exists:dealer_accounts,ID',
            'name'          => 'required|max:255',
            'branch_ID'     => 'required|exists:branches,ID',
            'promotor_ID'   => 'required|exists:promotors,ID',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'name'          => $input['name'],
            'branch_ID'     => $input['branch_ID'],
            'promotor_ID'   => $input['promotor_ID']
        ];
        
        // Save data
        $this->dealer_account->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a dealer account (ID:'.$input['ID'].'|new_name:'.$input['name'].'|new_branch_ID:'.$input['branch_ID'].')';
        $this->log->record($request->userID, $action);
        
        
        // Add flash session
        $request->session()->flash('dealer-account-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove dealer account page
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
        
        // Get dealer account
        $dealer_account = $this->dealer_account->getOne($ID);
        
        if (!$dealer_account)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('dealer-account.dealerAccountRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove dealer account request
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
        
        // Get dealer account
        $dealer_account = $this->dealer_account->getOne($ID);
        
        if (!$dealer_account)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->dealer_account->remove($ID);
        
        // Log request
        $action = 'Remove a dealer type (ID:'.$ID.'|name:'.$dealer_account->name.'|branch_ID:'.$dealer_account->branch_ID.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-account-removed', '');
        
        // Redirect to dealer index page
        return redirect('dealer-account');
    }
}
    