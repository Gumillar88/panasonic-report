<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\LogModel;

class BranchController extends Controller
{
    /**
     * Branch model container
     *
     * @access protected
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
        $this->promotor = new PromotorModel();
        $this->region   = new RegionModel();
        $this->branch   = new BranchModel();
        $this->log      = new LogModel();
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
     * Show list of current branch
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'regions'   => $this->_getAllRegion(),
            'dataTl'    => $this->_getAllTl(), 
            'branches'  => $this->branch->getAll(),
        ];
        
        return view('branches.branchIndex', $data);
    }
    
    /**
     * Render create branch page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
        $data = [
            'regions'   => $this->_getAllRegion(),
            'dataTl'    => $this->_getAllTl()
        ];			

        return view('branches.branchCreate', $data);
    }
    
    /**
     * Handle create branch request from form
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
            'region_ID'     => 'required|exists:regions,ID',
            'promotor_ID'   => 'required|exists:promotors,ID',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->branch->create($input['name'], $input['region_ID'], $input['promotor_ID']);
        
        // Log request
        $action = 'Create a branch (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('branch-created', '');
        
        // Redirect to branch index page
        return redirect('branch');
    }
    
    /**
     * Render update branch page
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
        
        // Get branch
        $branch = $this->branch->getOne($ID);
        
        if (!$branch)
        {
            return App::abort(404);
        }
        
        $data = [
            'branch'    => $branch,
            'regions'   => $this->_getAllRegion(),
            'dataTl'    => $this->_getAllTl()

        ];

        // Render page
        return view('branches.branchEdit', $data);
    }
    
    /**
     * Handle update branch data request
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
            'ID'            => 'required|exists:branches,ID',
            'name'          => 'required|max:255',
            'region_ID'     => 'required|exists:regions,ID',
            'promotor_ID'   => 'required|exists:promotors,ID',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'name'          => $input['name'],
            'region_ID'     => $input['region_ID'],
            'promotor_ID'   => $input['promotor_ID']
        ];
        
        // Save data
        $this->branch->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a branch (ID:'.$input['ID'].'|new_name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('branch-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove branch page
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
        
        // Get branch
        $branch = $this->branch->getOne($ID);
        
        if (!$branch)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('branches.branchRemove', ['branch' => $branch]);
    }
    
    /**
     * Handle remove branch request
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
        
        // Get branch
        $branch = $this->branch->getOne($ID);
        
        if (!$branch)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->branch->remove($ID);
        
        // Log request
        $action = 'Remove a branch (ID:'.$ID.'|name:'.$branch->name.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('branch-removed', '');
        
        // Redirect to branch index page
        return redirect('branch');
    }
}
