<?php

namespace App\Http\Middleware;

use App;
use Closure;
use App\Http\Models\ProductCategoryModel;

class ProductFilter
{
    /**
     * Category model container
     *
     * @access Protected
     */
    protected $category;

    /**
     * Class constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->category = new ProductCategoryModel();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //CATEGORY
        if($request->get('category_ID'))
        {   
            $category_ID = $request->get('category_ID');

            // Get category data
            $category = $this->category->getOne($category_ID);
            
            if (!$category)
            {
                return App::abort(404);
            }
            
            $request->session()->put('category_ID', $category_ID);

            $request->merge(compact('category_ID'));
        
            view()->share('category_ID',$category_ID);
            view()->share('category_name',$category->name);
        }
        else
        {
            //if on the page except index (for back button)
            $category_ID = $request->session()->get('category_ID');
            $request->merge(compact('category_ID'));
            view()->share('category_ID',$category_ID);
        }

        return $next($request);
        
        
    }
}
