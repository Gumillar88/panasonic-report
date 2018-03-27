<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\LogModel;

class DealerTypeController extends Controller
{
    /**
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;
    
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
        $this->dealer_type  = new DealerTypeModel();
        $this->log          = new LogModel();
    }
    
    
    /**
     * Show list of current dealer type
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'dealer_types' => $this->dealer_type->getAll(),
        ];

        return view('dealer-type.dealerTypeIndex',$data);
    }
    
    /**
     * Render create dealer type page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
        return view('dealer-type.dealerTypeCreate');
    }
    
    /**
     * Handle create dealer type request from form
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
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->dealer_type->create($input['name']);
        
        // Log request
        $action = 'Create a dealer type (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-type-created', '');
        
        // Redirect to dealer type index page
        return redirect('dealer-type');
    }
    
    /**
     * Render update dealer type page
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
        $dealer_type = $this->dealer_type->getOne($ID);
        
        if (!$dealer_type)
        {
            return App::abort(404);
        }
        
        // Set data
        $data = [
            'dealer_type'        => $dealer_type,
        ];
        
        // Render page
        return view('dealer-type.dealerTypeEdit', $data);
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
            'ID'        => 'required|exists:dealer_types,ID',
            'name'      => 'required|max:255',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'name'      => $input['name']
        ];
        
        // Save data
        $this->dealer_type->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a dealer type (ID:'.$input['ID'].'|new_name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        
        // Add flash session
        $request->session()->flash('dealer-type-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove dealer type page
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
        
        // Get dealer type
        $dealer_type = $this->dealer_type->getOne($ID);
        
        if (!$dealer_type)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('dealer-type.dealerTypeRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove dealer type request
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
        
        // Get dealer type
        $dealer_type = $this->dealer_type->getOne($ID);
        
        if (!$dealer_type)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->dealer_type->remove($ID);
        
        // Log request
        $action = 'Remove a dealer type (ID:'.$ID.'|name:'.$dealer_type->name.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-type-removed', '');
        
        // Redirect to dealer index page
        return redirect('dealer-type');
    }
}
    