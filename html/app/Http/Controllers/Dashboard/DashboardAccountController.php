<?php

namespace App\Http\Controllers\Dashboard;

use App;
use Crypt;
use Hash;
use Mail;
use Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DashboardAccountModel;
use App\Http\Models\DashboardTokenModel;
use App\Http\Models\LogModel;

class DashboardAccountController extends Controller
{
    /**
     * Dashboard account model container
     *
     * @access protected
     */
    protected $account;
    
    /**
     * Dashboard token model container
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
        $this->account  = new DashboardAccountModel();
        $this->token    = new DashboardTokenModel();
        $this->log      = new LogModel();
    }
    
    /**
     * Render dashboard account index page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data = [
            'accounts'  => $this->account->getAll(),
        ];
        
        return view('dashboard.dashboardAccountIndex', $data);
    }
    
    /**
     * Render create dashboard account page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {
        return view('dashboard.dashboardAccountCreate');
    }
    
    /**
     * Handle create dashboard account request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function createHandle(Request $request)
    {
        // Get input
        $input = $request->all();
        
        // Validate parameter
        $validator = Validator::make($input, [
            'name'      => 'required|max:255',
            'email'     => 'required|email|unique:dashboard_accounts,email',
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->account->create($input['name'], $input['email']);
        
        // Log activity
        $action = 'Create a dashboard account (ID:'.$ID.'|name:'.$input['name'].'|email:'.$input['email'].')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('account-created', '');
        return redirect('dashboard-account');
    }
    
    /**
     * Render edit dashboard account page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateRender(Request $request)
    {
        // Get ID
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get dashboard account
        $account = $this->account->getOne($ID);
        
        if (!$account)
        {
            return App::abort(404);
        }
        
        // Get news data
        $data = [
            'account'   => $this->account->getOne($ID),
            'tokens'    => $this->token->getByDashboardAccount($ID)
        ];
                
        return view('dashboard.dashboardAccountEdit', $data);
    }
    
    /**
     * Handle edit dashboard account request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        // Get input
        $input = $request->all();
        
        // Validate parameter
        $validator = Validator::make($input, [
            'ID'        => 'required|exists:dashboard_accounts,ID',
            'name'      => 'required|max:255',
            'email'     => 'required|email|unique:dashboard_accounts,email',
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'name'  => $input['name'],
            'email' => $input['email']
        ];
        
        // Save data
        $this->account->update($input['ID'], $data);
        
        // Log activity
        $action = 'Update a dashboard account (ID:'.$input['ID'].'|new_name:'.$input['name'].'|new_email:'.$input['email'].')';
        $this->log->record($request->userID, $action);
       
        // Set session and redirect
        $request->session()->flash('account-updated', '');
        return back();
    }
    
    /**
     * Render remove dashboard account page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeRender(Request $request)
    {
        // Get ID
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get dashboard account
        $account = $this->account->getOne($ID);
        
        if (!$account)
        {
            return App::abort(404);
        }
        
        return view('dashboard.dashboardAccountRemove', ['account' => $account]);
    }
    
    /**
     * Handle remove dashboard account request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeHandle(Request $request)
    {
        // Get ID
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get dashboard account
        $account = $this->account->getOne($ID);
        
        if (!$account)
        {
            return App::abort(404);
        }
        
        // Remove dashboard account
        $this->account->remove($ID);
        
        // Log activity
        $action = 'Remove a dashboard account (ID:'.$ID.'|name:'.$account->name.'|email:'.$account->email.')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('account-removed', '');
        return redirect('dashboard-account');
    }
    
    /**
     * Handle remove token based on specific dashboard account ID
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeTokenHandle(Request $request)
    {
        // Get token ID
        $tokenID = $request->get('tokenID', false);
        
        if (!$tokenID)
        {
            return App::abort(404);
        }
        
        // Get dashboard token
        $token = $this->token->getOne($tokenID);
        
        if (!$token)
        {
            return App::abort(404);
        }
        
        // Remove dashboard token
        $this->token->remove($tokenID);
        
        // Set session and redirect
        $request->session()->flash('token-removed', '');
        return redirect('dashboard-account/edit?ID='.$token->dashboard_account_ID);
    }
}