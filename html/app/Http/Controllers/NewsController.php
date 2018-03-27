<?php

namespace App\Http\Controllers;

use App;
use Validator;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\NewsModel;
use App\Http\Models\DealerNewsModel;
use App\Http\Models\DealerModel;
use App\Http\Models\LogModel;

class NewsController extends Controller
{
    /**
     * News model container
     *
     * @access protected
     */
    protected $news;
    
    /**
     * Dealer news model container
     *
     * @access protected
     */
    protected $dealerNews;

    /**
     * Delaer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Log model container
     *
     * @access protected
     */
    protected $log;
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->news         = new NewsModel();
        $this->dealerNews   = new DealerNewsModel();
        $this->dealer       = new DealerModel();
        $this->log          = new LogModel();
    }
    
    /**
     * Get all dealer
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerData()
    {
        $result = $this->dealer->getAll();
        $data   = [
            '0' => 'All'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }

    /**
     * Render news index page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data = [
            'news'      => $this->news->getAll(),
            'dealers'   => $this->_getAllDealerData(),
        ];
        
        return view('news.newsIndex', $data);
    }
    
    /**
     * Render create News page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {
        $data = [
            'dealers'   => $this->_getAllDealerData(),
        ];
        
        return view('news.newsCreate',$data);
    }
    
    /**
     * Handle create news request
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
            'title'     => 'required|max:255',
            'content'   => 'required',
            'dealer_ID' => 'required'
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Save data
        $ID = $this->news->create($input['title'], $input['content'], 0);
        
        // Save dealer news
        $this->dealerNews->set($ID, $input['dealer_ID']);
        
        // Log activity
        $action = 'Create a news (ID:'.$ID.'|title:'.$input['title'].'|content:'.$input['content'].')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('news-created', '');
        return redirect('news');
    }
    
    /**
     * Render edit news page
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

        // Get news
        $news = $this->news->getOne($ID);
        
        if (!$news)
        {
            return App::abort(404);
        }
        
        // Get news data
        $data = [
            'news' => $this->news->getOne($ID),
            'dealers'   => $this->_getAllDealerData(),
        ];
                
        return view('news.newsEdit', $data);
    }
    
    /**
     * Handle edit news request
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
            'ID'        => 'required|exists:news,ID',
            'title'     => 'required|max:255',
            'content'   => 'required'
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'title'      => $input['title'],
            'content'    => $input['content']
        ];
        
        // Save data
        $this->news->update($input['ID'], $data);
        
        // Log activity
        $action = 'Update a news (ID:'.$input['ID'].'|new_title:'.$input['title'].'|new_content:'.$input['content'].')';
        $this->log->record($request->userID, $action);
       
        // Set session and redirect
        $request->session()->flash('news-updated', '');
        return back();
    }
    
    /**
     * Render remove news page
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
        
        // Get promotor
        $news = $this->news->getOne($ID);
        
        if (!$news)
        {
            return App::abort(404);
        }
        
        return view('news.newsRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove news request
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
        
        // Get agent
        $news = $this->news->getOne($ID);
        
        if (!$news)
        {
            return App::abort(404);
        }
        
        // Remove agent
        $this->news->remove($ID);
        
        // Log activity
        $action = 'Remove a news (ID:'.$ID.'|title:'.$news->title.'|content:'.$news->content.')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('news-removed', '');
        return redirect('news');
    }
}