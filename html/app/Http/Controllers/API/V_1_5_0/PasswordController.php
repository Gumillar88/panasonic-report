<?php

namespace App\Http\Controllers\API\V_1_5_0;

use Hash;
use Crypt;
use Exception;
use Services_Twilio;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorMetaModel;
use App\Http\Models\TokenModel;

class PasswordController extends Controller
{
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Promotor meta model container
     *
     * @access protected
     */
    protected $meta;
    
    /**
     * Token model container
     *
     * @access protected
     */
    protected $token;

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->promotor = new PromotorModel();
        $this->meta     = new PromotorMetaModel();
        $this->token    = new TokenModel();
    }
    
    /**
     * Handle forgot password request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function forgot(Request $request)
    {
        // Validate parameter
        $phone = $request->get('phone', false);
        
        if (!$phone)
        {
            return response()->json(['error' => 'phone-not-valid']);
        }
        
        // Convert phone number
        if (substr($phone, 0, 1) === '0')
        {
            $phone = preg_replace('/^0/', '+62', $phone);
        }

        $promotor = $this->promotor->getByPhone($phone);

        // If promotor not found show that email is sent
        if (!$promotor)
        {
            return response()->json(['error' => 'phone-not-valid']);
        }
        
        // Check if promotor already requested code or not
        $code = $this->meta->get($promotor->ID, 'reset-password-code');
        
        if (!$code) 
        {
            // Generate 4 digit number
            $code = rand(1000, 9999);

            // Save it to account meta
            $this->meta->set($promotor->ID, 'reset-password-code', $code);
        }
        
        if (env('APP_ENV') === 'production') 
        {
            // Send SMS here
            $client         = new Services_Twilio(env('TWILLIO_ACCOUNT_SID'), env('TWILLIO_ACCOUNT_TOKEN')); 

            $client->account->messages->create(array( 
                'To'    => $promotor->phone, 
                'From'  => env('TWILLIO_ACCOUNT_NUMBER'), 
                'Body'  => 'PANASONIC PROMOTOR REPORT - Kode verifikasi untuk mereset password akun anda adalah '.$code,   
            ));
        }
        
        // Create secret
        $secret = Crypt::encrypt(json_encode(['phone' => $phone, 'time' => time()]));
        
        // Set response
        return response()->json(['result' => $secret]);
    }
    
    /**
     * Check code token for reset password
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function checkCode(Request $request)
    {
        // Validate parameter
        $secret = $request->get('secret', false);
        $code   = $request->get('code', false);
        
        if (!$secret)
        {
            return response()->json(['error' => 'no-secret']);
        }
        
        if (!$code)
        {
            return response()->json(['error' => 'no-code']);
        }
        
        try
        {
            $data = json_decode(Crypt::decrypt($secret), true);
        }
        catch (Exception $e)
        {
            return response()->json(['error' => 'invalid-secret']);
        }
        
        // Check user id
        $promotor = $this->promotor->getByPhone($data['phone']);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-user']);
        }
        
        // Check token
        $promotorCode = $this->meta->get($promotor->ID, 'reset-password-code');
        
        if (!$promotorCode)
        {
            return response()->json(['error' => 'no-code']);
        }
        
        if ($promotorCode !== $code)
        {
            return response()->json(['error' => 'code-invalid']);
        }
        
        // Generate new secret
        $newSecret = Crypt::encrypt(json_encode([
            'code'  => $promotorCode,
            'phone' => $data['phone'], 
            'time'  => time()
        ]));
        
        // Return success
        return response()->json(['result' => $newSecret]);
        
    }
    
    /**
     * Handle reset password request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function reset(Request $request)
    {
        // Validate parameter
        $secret     = $request->get('secret', false);
        $password   = $request->get('password', false);
        $passconf   = $request->get('passconf', false);
        
        
        if (!$secret)
        {
            return response()->json(['error' => 'no-secret']);
        }
        
        if (!$password)
        {
            return response()->json(['error' => 'password-empty']);
        }

        if (!$passconf)
        {
            return response()->json(['error' => 'password-empty']);
        }
        
        if (strlen($password) < 6 || strlen($passconf) < 6 )
        {
            return response()->json(['error' => 'password-min-length']);
        }
        
        if ($password !== $passconf)
        {
            return response()->json(['error' => 'password-not-match']);
        }
        
        try
        {
            $data = json_decode(Crypt::decrypt($secret), true);
        }
        catch (Exception $e)
        {
            return response()->json(['error' => 'invalid-secret']);
        }
        
        // Get email
        $promotor = $this->promotor->getByPhone($data['phone']);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-user']);
        }
        
        // Check code
        $promotorCode = $this->meta->get($promotor->ID, 'reset-password-code');
        
        if (!$promotorCode)
        {
            return response()->json(['error' => 'no-code']);
        }
        
        if ($promotorCode !== $data['code'])
        {
            return response()->json(['error' => 'code-invalid']);
        }
        
        // Hash new password
        $hash = Hash::make($password);
        
        // Update data
        $this->promotor->update($promotor->ID, ['password' => $hash]);
        
        // Remove reset password code
        $this->meta->remove($promotor->ID, 'reset-password-code');
        
        
        //create tokenon
        $ID = $promotor->ID;
        
        if($ID < 10 )
        {
            $ID = '0'.''.$promotor->ID;
        }
        
        $token = $ID.''.str_random(4);
        
        // Return success
        // Give token response
        return response()->json([
            'token' => $this->token->encode($promotor->ID, $token)
        ]);
    }
    
    /**
     * Handle change password request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function change(Request $request)
    {
        // Validate parameter
        $passwordOld    = $request->get('passwordOld', false);
        $passwordNew    = $request->get('passwordNew', false);
        $passwordConf   = $request->get('passwordConf', false);
        $token          = $request->get('token', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        if (!$passwordOld)
        {
            return response()->json(['error' => 'password-old-empty']);
        }
        
        if (!$passwordNew)
        {
            return response()->json(['error' => 'password-new-empty']);
        }

        if (!$passwordConf)
        {
            return response()->json(['error' => 'password-new-empty']);
        }
        
        if (strlen($passwordNew) < 6 || strlen($passwordConf) < 6 )
        {
            return response()->json(['error' => 'password-new-min-length']);
        }
        
        if ($passwordConf !== $passwordNew)
        {
            return response()->json(['error' => 'password-new-not-match']);
        }
        
        
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Compare old password with promotor password
        if (!Hash::check($passwordOld, $promotor->password))
        {
            return response()->json(['error' => 'password-old-error']);
        }
        
        
        // Create hash from password and update promotor password
        $this->promotor->update($promotorID, [
            'password' => Hash::make($passwordNew)
        ]);
        
        return response()->json(['result' => 'success']);
    }

    /**
     * Handle generate password
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function generate(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $password   = $request->get('password', false);
        $passconf   = $request->get('passconf', false);
        
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        if (!$password)
        {
            return response()->json(['error' => 'password-empty']);
        }
        
        if (!$passconf)
        {
            return response()->json(['error' => 'password-empty']);
        }
        
        if (strlen($password) < 6 || strlen($passconf) < 6 )
        {
            return response()->json(['error' => 'password-min-length']);
        }
        
        if ($password !== $passconf)
        {
            return response()->json(['error' => 'password-not-match']);
        }
        // Validate user
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Create hash from password and update promotor password
        $this->promotor->update($promotorID, [
            'password' => Hash::make($password)
        ]);
        
        return response()->json(['result' => 'success']);
    }
}
