<?php

namespace App\Http\Controllers;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\LogModel;

class DealerChannelController extends Controller
{
    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealer_channel;
    
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
        $this->dealer_channel       = new DealerChannelModel();
        $this->log                  = new LogModel();
    }
    
    
    /**
     * Show list of current dealer channel
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = [
            'dealer_channels' => $this->dealer_channel->getAll(),
        ];

        return view('dealer-channels.dealerChannelIndex',$data);
    }
    
    /**
     * Render create dealer channel page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {	
        return view('dealer-channels.dealerChannelCreate');
    }
    
    /**
     * Handle create dealer channel request from form
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
        $ID = $this->dealer_channel->create($input['name']);
        
        // Log request
        $action = 'Create a dealer channel (ID:'.$ID.'|name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-channel-created', '');
        
        // Redirect to dealer channel index page
        return redirect('dealer-channel');
    }
    
    /**
     * Render update dealer channel page
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
        
        // Get dealer channel
        $dealer_channel = $this->dealer_channel->getOne($ID);
        
        if (!$dealer_channel)
        {
            return App::abort(404);
        }
        
        // Set data
        $data = [
            'dealer_channel'        => $dealer_channel,
        ];
        
        // Render page
        return view('dealer-channels.dealerChannelEdit', $data);
    }
    
    /**
     * Handle update dealer channel data request
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
            'ID'        => 'required|exists:dealer_channels,ID',
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
        $this->dealer_channel->update($input['ID'], $data);
        
        // Log request
        $action = 'Update a dealer channel (ID:'.$input['ID'].'|new_name:'.$input['name'].')';
        $this->log->record($request->userID, $action);
        
        
        // Add flash session
        $request->session()->flash('dealer-channel-updated', '');
        
        // Redirect back
        return back();
    }
    
    /**
     * Render remove dealer channel page
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
        
        // Get dealer channel
        $dealer_channel = $this->dealer_channel->getOne($ID);
        
        if (!$dealer_channel)
        {
            return App::abort(404);
        }
        
        // Render page
        return view('dealer-channels.dealerChannelRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove dealer channel request
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
        
        // Get dealer channel
        $dealer_channel = $this->dealer_channel->getOne($ID);
        
        if (!$dealer_channel)
        {
            return App::abort(404);
        }
        
        // Delete data
        $this->dealer_channel->remove($ID);
        
        // Log request
        $action = 'Remove a dealer channel (ID:'.$ID.'|name:'.$dealer_channel->name.')';
        $this->log->record($request->userID, $action);
        
        // Add flash session
        $request->session()->flash('dealer-channel-removed', '');
        
        // Redirect to dealer index page
        return redirect('dealer-channel');
    }
}
    