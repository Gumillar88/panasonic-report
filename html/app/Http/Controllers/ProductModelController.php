<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\LogModel;
use PHPExcel_Reader_Excel2007;

class ProductModelController extends Controller
{
	/**
     * Product category model container
     *
     * @access Protected
     */
    protected $product_category;
    
    /**
     * Product model container
     *
     * @access Protected
     */
    protected $model;
    
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
        $this->product_category = new ProductCategoryModel();
        $this->model            = new ProductModel();
        $this->log              = new LogModel();
    }
    
    /**
     * Get Product category list
     *
     * @access private
     * @return Array
     */
    private function _getProductCategoryList()
    {   
        // Set default category data
        $categoryData = [
            0 => '(none)'
        ];

        // Put all category to category data container
        $categories    = $this->product_category->getAll();

        foreach ($categories as $category)
        {
            $categoryData[$category->ID] = $category->name;
        }
        
        // Sort by value
        asort($categoryData);

        return $categoryData;
    }

    /**
     * Show list of current product
     *
     * @access public
     * @return Response
     */
    public function index(Request $request)
    {   
        // Get TYPE category_ID
        $category_ID = $request->get('category_ID', false);
        
        if (!$category_ID)
        {
            return App::abort(404);
        }

        $request->session()->put('category_ID', $category_ID);

        $data = [
            'categories'    => $this->_getProductCategoryList(),
            'products'      => $this->model->getByCategory($category_ID) 
        ];
        
        return view('products.productIndex', $data);
    }
    
    /**
     * Render create product page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
		$data = [
            'product_categories'=> $this->_getProductCategoryList(),
        ];
		
        return view('products.productCreate', $data);
    }
    
    /**
     * Handle create product request from form
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
            'price' => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Get type id
        $product_category_ID = $request->get('product_category_ID', '0');
        
        // Save data
        $ID = $this->model->create($product_category_ID, $input['name'], $input['price']);
        
        // Log request
        $action = 'Create a product (ID:'.$ID.'|input:'.json_encode($input).')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-created', '');
        
        // Redirect to product index page
        return redirect('product/category');
    }
    
    /**
     * Render update product page
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
        
        // Get product data
        $product = $this->model->getOne($ID);
        
        if (!$product)
        {
            return App::abort(404);
        }
        
        
        // Set data
        $data = [
            'product_category'      => $product->product_category_ID,
            'product_categories'    => $this->_getProductCategoryList(),
            'product'               => $product
        ];
        
        // Render page
        return view('products.productEdit', $data);
    }
    
    /**
     * Handle update product data request
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
            'ID'        => 'required|exists:product_models,ID',
            'name'      => 'required|max:255',
            'price'     => 'required',
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'product_category_ID'       => (int) $request->get('product_category_ID', 0),
            'name'                  => $input['name'],
            'price'                 => $input['price'],
        ];
        
        // Save data
        $this->model->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a product category (ID:'.$input['ID'].'|input:'.json_encode($input).')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove product page
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
        
        // Get product data
        $product = $this->model->getOne($ID);
        
        if (!$product)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('products.productRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove product request
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
        
        // Get product data
        $product = $this->model->getOne($ID);
        
        if (!$product)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->model->remove($ID);
        
        // Log request
        $action = 'Remove a product category (ID:'.$ID.'|input:'.json_encode($product).')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('product-removed', '');
        
        // Redirect to product index page
        return redirect('product/category');
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
        
        return view('products.productUpload', $data);
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
        $objReader      = new PHPExcel_Reader_Excel2007();
        $objReader->setReadDataOnly(true);
        
        // Get active sheet
        $objPHPExcel    = $objReader->load($request->file('excel')->getPathName());
        $sheet          = $objPHPExcel->getActiveSheet();
                
        // Loop rows to get data
        $rowValue   = 2;
        $rows       = [];
        $rowLoop    = true;
        
        while($rowLoop)
        {
            // Set temporary container
            $tempRow = [];
            
            // Get value from column
            $product_name = $sheet->getCell('A'.$rowValue)->getValue();
            $product_category = $sheet->getCell('C'.$rowValue)->getValue();
            
            // If value not found, then stop the loop
            if ($product_name === null)
            {
                $rowLoop = false;
                break;
            }
            
            $tempRow = [
                'product'   => $product_name,
                'category'  => $product_category
            ];
            
            // Put temporary container to rows if the loop still continue
            if ($rowLoop === true)
            {
                $rows[] = $tempRow;
                $rowValue++;
            }
        }
        
        // Set render data
        $data = [
            'type'          => 'review',
            'rows'          => $rows,
        ];

        return view('products.productUpload', $data);

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
            'product'     => 'required',
            'category'    => 'required'
        ];
        
        // Set messages
        $messages = [
            'product.required'    => 'General error',
            'values.required'   => 'General error'
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
        
        $category = $this->product_category->getCategory($input['category']);

        if($category)
        {
            $product = $this->model->getByProduct($category->ID, $input['product']);
            
            if(!$product)
            {
                // Save data
                $this->model->create($category->ID, $input['product'], 0);
            }
            else
            {
                return response()->json('Already');
            }
        }
        
        return response()->json('Saved');
    }
}
