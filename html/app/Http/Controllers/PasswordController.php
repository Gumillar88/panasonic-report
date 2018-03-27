<?php

namespace App\Http\Controllers;

use App;
use Hash;
use Mail;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\UserMetaModel;
use App\Http\Models\UserModel;
use App\Http\Models\LogModel;

class PasswordController extends Controller
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
        $this->user         = new UserModel();
        $this->log          = new LogModel();
        $this->user_meta    = new UserMetaModel();

    }
    /**
     * Render forgot page email
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function forgotRender()
    {
        return view('passwords.passwordForgot');
    }

    /**
     * Handle forgot email POST request
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function forgotHandle(Request $request)
    {

        $input  = $request->all();

        // set validator input
        $validator = Validator::make($input, [
        	'email'	=> 'required|email|exists:users,email'
        ]);

        if ($validator->fails())
        {
        	return back()->withErrors($validator)->withInput();
        }

        // get email by user
        $user 		= $this->user->getByEmail($input['email']);

        // set code
        $code = str_random(32);
        // save access key to account meta
        $this->user_meta->set($user->ID, 'token', $code);

        // set email data
        $emailData = [
            'link'  => env('APP_HOME_URL').'/password/reset?token='.$code
        ];

        Mail::send('dashboard.email', $emailData, function ($m) use ($user) 
        {
            $m->from('havas.jakarta@havas.com', 'Havas Jakarta');
            $m->to($user->email, $user->fullname)->subject('Panasonic Report Dashboard Access');
        });


        // log record
        $dataLog = [
            'user'      => $user,
            'emailData' => $emailData
        ];
        $action = 'Forgot password';
        $this->log->record($user->ID, $action, $dataLog);

        // set flash notification
        $request->session()->flash('forgot-notes', '');

        return back();
    }

    /**
     * Render Form reset page
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function resetRender(Request $request)
    {
        // check code
        $token = $request->get('token', false);

        if (!$token)
        {
            return App::abort(404);
        }
        $meta = $this->user_meta->getByContent($token);
        
        if (!$meta)
        {
            return App::abort(404);
        }
        // check time
        if ((time() - (int) $meta->created) > 3600)
        {
            $this->user_meta->remove($meta->user_ID, 'token');

            $request->session()->flash('expired', '');

            return redirect(env('APP_ADMIN_SECTION').'/password/forgot');
        }

        $user = $this->user->getOne($meta->user_ID);
        
        if (!$user)
        {
            return App::abort(404);
        }

        // get data user
        $data = [
            'token' => $token,
            'user'  => $this->user->getOne($meta->user_ID)
        ];

        return view('passwords.passwordReset', $data);
    }

    /**
     * Reset Handle 
     *
     * @access public
     * @param Request
     * @return Response
     */
    public function resetHandle(Request $request)
    {   
        $input = $request->all();
    
        // validation
        $validator = Validator::make($input, [
            'password' => 'required|min:8|confirmed'
        ]);
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        $token = $request->get('token', false);

        if (!$token)
        {   
            return App::abort(404);
        }

        // Get user_ID user_meta
        $user_meta = $this->user_meta->getByContent($token);

        // ID user meta
        $ID        = $user_meta->ID;

        // ID users
        $userID    = $user_meta->user_ID;
        
        $data = [
            'password'   => Hash::make($input['password'])
        ];

        // update data
        $this->user->update($userID, $data);
        
        // remove token
        $this->user_meta->remove($userID, $token);

        // log data record
        $dataLog = [
            'userID'    => $userID
        ];

        $action     = 'Reset password';
        
        $this->log->record($userID, $action, $dataLog);
        
        return redirect(env('APP_ADMIN_SECTION').'/login?status=success');
    }
}