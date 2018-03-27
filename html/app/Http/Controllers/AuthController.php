<?php

namespace App\Http\Controllers;

use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\UserModel;
use App\Http\Models\LogModel;

class AuthController extends Controller
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
     * Object constructor
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
     * Render login page
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function loginRender(Request $request)
    {
        if ($request->session()->has('user_ID'))
        {
            return redirect('/');
        }

        return view('login');
    }

    /**
     * Handle login POST request
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function loginHandle(Request $request)
    {
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        
        // Get user
        $isValid    = true;
        $user       = $this->user->getByUsername($username);

        // If user not found show error
        if (!$user)
        {
            $isValid = false;
        }

        // Check hash
        if ($isValid)
        {
            $isValid = Hash::check($password, $user->password);
        }

        if (!$isValid)
        {
            $request->session()->flash('login-error', 'login-error');

            return back()->withInput($request->except('password'));
        }

        // Log the record
        $action = 'Login to Admin Panel';
        $this->log->record($user->ID, $action);

        $request->session()->put('user_ID', $user->ID);
        $request->session()->put('user_status', $user->type);
        
        return redirect('/');
    }

    /**
     * Handle logout GET request
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function logout(Request $request)
    {
        // Log the record
        $action = 'Logout from Admin Panel';
        $this->log->record($request->session()->get('user_ID'), $action);

        $request->session()->flush();
        return redirect('login');
    }
}
