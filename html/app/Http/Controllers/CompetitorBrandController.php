<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\LogModel;
use App\Http\Models\CompetitorBrandModel;

class CompetitorBrandController extends Controller
{
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $brand;

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
        $this->brand    = new CompetitorBrandModel();
        $this->log      = new LogModel();
    }
    
    /**
     * Show list of current brand
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'brands'   => $this->brand->getAll(),
        ];
        
        return view('competitor-brands.competitorBrandIndex', $data);
    }
    
    /**
     * Render create brand page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {
        return view('competitor-brands.competitorBrandCreate');
    }
    
    /**
     * Handle create brand request from form
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
            'name'          => 'required|max:255'
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->brand->create($input['name']);
        
        // Log request
        $action = 'Create a competitor brand (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('brand-created', '');
        
        // Redirect to brand index page
        return redirect('competitor-brand');
    }
    
    /**
     * Render update brand page
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
        
        // Get brand
        $brand = $this->brand->getOne($ID);
        
        if (!$brand)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('competitor-brands.competitorBrandEdit', ['brand' => $brand]);
    }
    
    /**
     * Handle update brand data request
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
            'ID'    => 'required|exists:competitor_brands,ID',
            'name'  => 'required|max:255',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $this->brand->update($input['ID'], ['name' => $input['name']]);
        
        // Log request
        $action = 'Update a brand (ID:'.$input['ID'].'|new_name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('brand-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove brand page
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
        
        // Get brand
        $brand = $this->brand->getOne($ID);
        
        if (!$brand)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('competitor-brands.competitorBrandRemove', ['brand' => $brand]);
    }
    
    /**
     * Handle remove brand request
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
        
        // Get brand
        $brand = $this->brand->getOne($ID);
        
        if (!$brand)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->brand->remove($ID, $brand->name);
        
        // Log request
        $action = 'Remove a competitor brand (ID:'.$ID.'|name:'.$brand->name.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('brand-removed', '');
        
        // Redirect to region index page
        return redirect('competitor-brand');
    }
}
