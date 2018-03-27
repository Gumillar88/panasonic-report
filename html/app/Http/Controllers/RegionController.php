<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\RegionModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\LogModel;

class RegionController extends Controller
{
    /**
     * Regions model container
     *
     * @access protected
     */
    protected $region;

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
        $this->promotor   = new PromotorModel();
        $this->region   = new RegionModel();
        $this->log      = new LogModel();
    }
    
    /**
     * Get all promotor
     *
     * @access private
     * @return Array
     */
    private function _getAllArco()
    {
        $result = $this->promotor->getByType('arco');
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
     * Show list of current region
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'regions'   => $this->region->getAll(),
            'dataArco'  => $this->_getAllArco(),
        ];
        
        return view('regions.regionIndex', $data);
    }
    
    /**
     * Render create region page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
        $data = [
            'dataArco'  => $this->_getAllArco()
        ];			

        return view('regions.regionCreate',$data);
    }
    
    /**
     * Handle create region request from form
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
            'promotor_ID'   => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->region->create($input['name'],$input['promotor_ID']);
        
        // Log request
        $action = 'Create a region (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('region-created', '');
        
        // Redirect to region index page
        return redirect('region');
    }
    
    /**
     * Render update region page
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
        
        // Get region
        $region = $this->region->getOne($ID);
        
        if (!$region)
        {
            return App::abort(404);
        }
        
        $data = [
            'region'    => $region, 
            'dataArco'  => $this->_getAllArco()

        ];

        // Render page
        return view('regions.regionEdit', $data);
    }
    
    /**
     * Handle update region data request
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
            'ID'            => 'required|exists:regions,ID',
            'name'          => 'required|max:255',
            'promotor_ID'   => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'name'          => $input['name'],
            'promotor_ID'   => $input['promotor_ID'],
        ];
        
        // Save data
        $this->region->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a region (ID:'.$input['ID'].'|new_name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('region-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove region page
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
        
        // Get region
        $region = $this->region->getOne($ID);
        
        if (!$region)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('regions.regionRemove', ['region' => $region]);
    }
    
    /**
     * Handle remove region request
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
        
        // Get region
        $region = $this->region->getOne($ID);
        
        if (!$region)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->region->remove($ID);
        
        // Log request
        $action = 'Remove a region (ID:'.$ID.'|name:'.$region->name.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('region-removed', '');
        
        // Redirect to region index page
        return redirect('region');
    }
}
