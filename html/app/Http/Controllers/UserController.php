<?php

namespace App\Http\Controllers;

use App;
use Hash;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\UserModel;
use App\Http\Models\LogModel;

class UserController extends Controller
{
    /**
     * User model container
     *
     * @access protected
     */
    protected $user;

    /**
     * Log model container
     *
     * @access protected
     */
    protected $log;
    
    /**
     * User type list
     *
     * @access protected
     */
    protected $userType = [
        'normal'    => 'Normal',
        'admin'     => 'Admin'
    ];

    /**
     * Class constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->user = new UserModel();
        $this->log  = new LogModel();
    }

    /**
     * Render list of all user
     *
     * @access public
     * @return Response
     */
    public function index()
    {
        $data = $this->user->getAll();
        return view('users.userIndex', ['users' => $data]);
    }

    /**
     * Render create Page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {
        return view('users.userCreate', ['types' => $this->userType]);
    }

    /**
     * Create user handler
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
            'fullname' => 'required|max:255',
            'username' => 'required|max:255|alpha_num|unique:users,username',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        // Encrypt password
        $hash = Hash::make($input['password']);

        // Create user
        $resultID = $this->user->create($input['fullname'], $input['username'], $hash, $input['type']);

        // Set session
        $request->session()->flash('user-created', '');

        // Log the record
        $userID    = $request->session()->get('user_ID');
        $action = 'Create an user ('.$resultID.'|'.$input['username'].')';
        $this->log->record($userID, $action);

        // Redirect back to user index
        return redirect('user');
    }

    /**
     * Render update user page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateRender(Request $request)
    {
        // Set user id
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }

        // Get user data
        $user = $this->user->getOne($ID);

        // Show 404 if user not found
        if (!$user)
        {
            return abort(404);
        }

        // Set data to render
        $data = [
            'user'      => $user,
            'types'     => $this->userType
        ];

        return view('users.userEdit', $data);
    }

    /**
     * Update user handler
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        // Set ID
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Set input
        $input = $request->all();

        // Validate input
        $validator = Validator::make($input, [
            'ID'        => 'required|exists:users,ID',
            'fullname'  => 'required|max:255',
            'username'  => 'required|max:255|alpha_num|unique:users,username,'.$ID.',ID',
            'type'      => 'required',
            'password'  => 'min:6'
        ]);

        if ($validator->fails())
        {
            return back()->withErrors($validator);
        }

        // Setup data to update
        $data = [
            'fullname'  => $input['fullname'],
            'username'  => $input['username'],
            'type'      => $input['type']
        ];

        // If password exist then encrypt password
        if ($request->has('password'))
        {
            $data['password'] = Hash::make($input['password']);
        }

        // Save Data
        $this->user->update($input['ID'], $data);

        // Log the record
        $userID     = $request->session()->get('user_ID');
        $action     = 'Edit an user ('.$ID.'|'.$input['username'].')';
        $this->log->record($userID, $action);

        // Redirect back with flash session
        $request->session()->flash('user-updated', '');
        return back();
    }

    /**
     * Render remove user page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeRender(Request $request)
    {
        // Set user id
        $ID = $request->get('ID', false);
        
        // Show error if there is no uid parameter
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get user information for logging purposes
        $user = $this->user->getOne($ID);

        // Show error if user not found
        if (!$user)
        {
            return App::abort(404);
        }

        return view('users.userRemove', ['user' => $user]);
    }

    /**
     * Remove user handler
     *
     * @param public
     * @param Request $request
     * @return Response
     */
    public function removeHandle(Request $request)
    {
        // Set user id
        $ID = $request->get('ID', false);
        
        // Show error if there is no uid parameter
        if (!$ID)
        {
            return App::abort(404);
        }

        // Cannot remove super admin
        if ($ID == 1)
        {
            return App::abort(404);
        }

        // Get user information for logging purposes
        $user = $this->user->getOne($ID);

        // Show error if user not found
        if (!$user)
        {
            return App::abort(404);
        }

        // Remove user
        $this->user->remove($ID);

        // Log the record
        $userID     = $request->session()->get('user_ID');
        $action     = 'Remove an user ('.$ID.'|'.$user->username.')';
        $this->log->record($userID, $action);

        // Set flash session
        $request->session()->flash('user-deleted', '');

        // Redirect to user index
        return redirect('user');
    }
}
