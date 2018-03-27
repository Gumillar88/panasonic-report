<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;
use App\Http\Models\LogModel;

class CompetitorPriceController extends Controller
{
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $brand;

    /**
     * Competitor price model container
     *
     * @access protected
     */
    protected $price;
    
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
        $this->price    = new CompetitorPriceModel();
        $this->log      = new LogModel();
    }

    /**
     * Show list of current price data
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Set month
        $month = $request->get('month', date('Y-m'));
        
        $data = [
            'prices' => $this->price->getCompiledIndex($month)
        ];
        
        return view('competitor-prices.competitorPriceIndex', $data);
    }
    
    /**
     * Render remove price data page
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
        
        // Get price data
        $price = $this->price->getOne($ID);
        
        if (!$price)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('competitor-prices.competitorPriceRemove', ['price' => $price]);
    }
    
    /**
     * Handle remove price data request
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
        
        // Get price data
        $price = $this->price->getOne($ID);
        
        if (!$price)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->price->remove($ID);
        
        // Log request
        $action = 'Remove a competitor price (ID:'.$ID.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('price-removed', '');
        
        // Redirect to region index page
        return redirect('competitor-price');
    }
}
