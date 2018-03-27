<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\LogModel;

class ProductCategoryController extends Controller
{
    /**
     * category model container
     *
     * @access Protected
     */
    protected $category;
    
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
        $this->category = new ProductCategoryModel();
        $this->log      = new LogModel();
    }
    
    /**
     * Show list of current category
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'categories' => $this->category->getAll() 
        ];
        
        return view('product-category.categoryIndex', $data);
    }
    
    /**
     * Render create category page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {
        return view('product-category.categoryCreate');
    }
    
    /**
     * Handle create category request from form
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
            'name'  => 'required|max:255',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->category->create($input['name']);
        
        // Log request
        $action = 'Create a product category (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('category-created', '');
        
        // Redirect to category index page
        return redirect('product/category');
    }
    
    /**
     * Render update category page
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
        
        // Get category data
        $category = $this->category->getOne($ID);
        
        if (!$category)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('product-category.categoryEdit', ['category' => $category]);
    }
    
    /**
     * Handle update category data request
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
            'ID'    => 'required|exists:product_categories,ID',
			'name'  => 'required|max:255',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $this->category->update($input['ID'], ['name' => $input['name']]);
        
        // Log request
        $action = 'Update a product category (ID:'.$input['ID'].'|new_name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('category-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove category page
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
        
        // Get category data
        $category = $this->category->getOne($ID);
        
        if (!$category)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('product-category.categoryRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove category request
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
        
        // Get category data
        $category = $this->category->getOne($ID);
        
        if (!$category)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->category->remove($ID);
        
        // Log request
        $action = 'Remove a product category (ID:'.$ID.'|name:'.$category->name.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('category-deleted', '');
        
        // Redirect to category index page
        return redirect('product/category');
    }
}
