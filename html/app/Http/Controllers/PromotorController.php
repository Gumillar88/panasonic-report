<?php

namespace App\Http\Controllers;

use App;
use Validator;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorMetaModel;
use App\Http\Models\PromotorTargetModel;
use App\Http\Models\LogModel;

class PromotorController extends Controller
{
    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
    /**
     * Branch model container
     *
     * @access protected
     */
    protected $branch;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Dealer account model container
     *
     * @access protected;
     */
    protected $dealerAccount;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

     /**
     * Promotor_meta model container
     *
     * @access protected
     */
    protected $promotor_meta;
    
    /**
     * Promotor Target model container
     * 
     * @access protected
     */
    protected $promotorTarget;
    
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
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealer           = new DealerModel();
        $this->dealerAccount    = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_meta    = new PromotorMetaModel();
        $this->promotorTarget   = new PromotorTargetModel();
        $this->log              = new LogModel();
    }
    
    /**
     * Get all branch
     * 
     * @access private
     * @return Array
     */
    private function _getAllBranch()
    {
        $result = $this->branch->getAll();
        $data   = [
            '0' => 'none'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }
    
    /**
     * Get all region
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerData()
    {
        $branches   = $this->_getAllBranch();
        $dealers    = $this->dealer->getAll();
        
        $data   = [
            '0' => [
                'branch' => '(none)',
                'name' => '(none)'
            ]
        ];
        
        foreach ($dealers as $item)
        {
            $data[$item->ID] = [
                'branch'    => $branches[$item->branch_ID],
                'name'      => $item->name,
            ];
        }
        
        return $data;
    }

    /**
     * Get all promotor parent
     *
     * @access private
     * @return Array
     */
    private function _getAllPromotorParent()
    {
        // Get promotor parent
        $promotors = $this->promotor->getAllByType(['panasonic', 'arco', 'tl']);        
        
        // Set container
        $parents   = [
            'data' => [],
            'list' => []
        ];
        
        foreach ($promotors as $promotor)
        {
            $key = 'type-'.$promotor->type;
            
            if (!array_key_exists($key, $parents['data']))
            
            {
                $parents['data'][$key] = [];
            }
            
            $parents['data'][$key][] = (int) $promotor->ID;
            
            // Push parent to list
            $parents['list'][$promotor->ID] = $promotor->name .' ('. $promotor->type.')';
        }
        
        return $parents;
    }

    /**
     * Get Dealer lisf for form
     *
     * @access private
     * @return Array
     */
    private function _getDealerList()
    {
        $branches   = $this->branch->getAll();
        $dealers    = $this->dealer->getAll();
        
        // Set branch map and empty dealer
        $branchMap      = [
            '0' => '(none)'
        ];
        $branchDealer   = [];
        
        foreach ($branches as $branch) 
        {
            $branchMap[$branch->ID]         = $branch->name;
            $branchDealer[$branch->name]    = [];
        }
        
        
        // Put dealer into branch dealer container
        foreach ($dealers as $dealer)
        {
            $branchDealer[$branchMap[$dealer->branch_ID]][$dealer->ID] = $dealer->name;
        }
        
        return $branchDealer;
    }

    /**
     * user type
     * @var [type]
     */
    protected $user_type =[
        'panasonic'         => 'Panasonic',
        'branch-manager'    => 'Branch Manager',
        'arco'              => 'Area Coordinator',
        'tl'                => 'Team Leader',
        'promotor'          => 'Promotor',
        'non-active'        => 'Non Active'
    ];
   

    /**
     * Render promotor index page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data = [
            'dealers'           => $this->_getAllDealerData(),
            'promotors'         => $this->promotor->getAll(),
            'user_type'         => $this->user_type,
        ];
        
        return view('promotors.promotorIndex', $data);
    }
    
    /**
     * Render create promotor page
     *
     * @access public
     * @return Response
     */
    public function createRender()
    {   
        $data = [
            'dealers'       => $this->_getDealerList(),
            'user_type'     => $this->user_type,
            'parents'       => $this->_getAllPromotorParent()
        ];

        return view('promotors.promotorCreate', $data);
    }
    
    /**
     * Handle create promotor request
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
            'name'      => 'required|max:255',
            'phone'     => 'required|max:15|unique:promotors,phone',
            'password'  => 'min:6',
            'type'      => 'required',
            'gender'    => 'required',
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Get value from parent ID
        $dealerID  = (int) $request->get('dealer_ID', 0);
        
        // Make dealer id to 0 for leader account
        if ($input['type'] !== 'promotor')
        {
            $dealerID = 0;
        }
        
        // Set default password
        $password = 'havas';
        
        // Hash password
        if ($request->has('password'))
        {
            $password = Hash::make($input['password']);
        }
        
        // Get parent ID
        $parentID = (int) $request->get('parent_ID', 0);
        
        // Save data
        $ID = $this->promotor->create($dealerID, $input['phone'], $password, $input['name'], $input['gender'], $input['type'], $parentID);
        
        // Log request
        $action = 'Create a promotor (ID:'.$ID.'|phone:'.$input['phone'].'|name:'.$input['name'].'|type:'.$input['type'].')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('promotor-created', '');
        return redirect('promotor');
    }
    
    /**
     * Render edit promotor page
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
        
        // Get promotor data
        $promotor = $this->promotor->getOne($ID);
        
        if (!$promotor)
        {
            return App::abort(404);
        }
        
        // Get promotor meta data
        $isBlocked      = false;
        $promotor_meta  = $this->promotor_meta->get($ID, 'block');
        
        if ($promotor_meta)
        {
            $isBlocked  = true;
        }

        // Set data
        $data = [
            'dealers'       => $this->_getDealerList(),
            'promotor'      => $promotor,
            'user_type'     => $this->user_type,
            'parents'       => $this->_getAllPromotorParent(),
            'isBlocked'     => $isBlocked
        ];
        
        return view('promotors.promotorEdit', $data);
    }
    
    /**
     * Handle edit promotor request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get input
        $input = $request->all();
        
        // Validate parameter
        $validator = Validator::make($input, [
            'ID'        => 'required|exists:promotors,ID',
            'name'      => 'required|max:255',
            'phone'     => 'required|max:15|unique:promotors,phone,'.$input['ID'].',ID',
            'type'      => 'required',
            'gender'    => 'required',
            'password'  => 'min:6'
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Set data
        $data = [
            'name'          => $input['name'],
            'phone'         => $input['phone'],
            'dealer_ID'     => (int) $request->get('dealer_ID', 0),
            'type'          => $input['type'],
            'gender'        => $input['gender'],
            'parent_ID'     => (int) $request->get('parent_ID', 0),
        ];
        
        // Make dealer id to 0 for leader account
        if ($input['type'] !== 'promotor')
        {
            $data['dealer_ID'] = 0;
        }
        
        // Hash password
        if ($request->has('password'))
        {
            $data['password'] = Hash::make($input['password']);
        }
        
        // Save data
        $this->promotor->update($input['ID'], $data);
        
        if ($input['type'] === 'promotor')
        {
            // Get ID of the parents
            $dealer     = $this->dealer->getOne($data['dealer_ID']);

            if(!$dealer || !$dealer->dealer_account_ID)
            {
                $accountID    = 0;
                $tlID         = 0;
                $arcoID       = 0;
            }
            elseif (!$data['parent_ID'])
            {
                $accountID    = 0;
                $tlID         = 0;
                $arcoID       = 0;
            }
            else
            {
                $accountID      = $dealer->dealer_account_ID;
                $TL             = $this->promotor->getOne($data['parent_ID']);
                $tlID           = $TL->ID;
                $arcoID         = $TL->parent_ID;
            }
        
            // Update data in promotor target for current month
            $this->promotorTarget->updatePromotorMeta($input['ID'], $data['dealer_ID'], $accountID, $tlID, $arcoID);
        }
        
        // Log request
        $action = 'Update promotor (ID:'.$input['ID'].')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('promotor-updated', '');
        return back();
    }
    
    /**
     * Render remove promotor page
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
        $promotor = $this->promotor->getOne($ID);
        
        if (!$promotor)
        {
            return App::abort(404);
        }
        
        return view('promotors.promotorRemove', ['ID' => $ID]);
    }
    
    /**
     * Handle remove promotor request
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
        $promotor = $this->promotor->getOne($ID);
        
        if (!$promotor)
        {
            return App::abort(404);
        }
        
        // Remove agent
        $this->promotor->remove($ID);
        
        // Log request
        $action = 'Remove promotor (ID:'.$ID.'|name:'.$promotor->name.')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('promotor-removed', '');
        return redirect('promotor');
    }

    /**
     * Handle block promotor request 
     *
     * @access public
     * @param  Request $request 
     * @return response          
     */
    public function blockHandle(Request $request) 
    {
        // Check parameter
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }

        // Check promotor
        $promotor = $this->promotor->getOne($ID);
        
        if (!$promotor)
        {
            return App::abort(404);
        }

        // Get promotor meta data
        $promotor_meta = $this->promotor_meta->get($ID, 'block');
        
        // Set default action log and flash message
        if(!$promotor_meta)
        {
            $this->promotor_meta->set($ID, 'block', 'block');
            
            $flash  = 'promotor-block';
            $action = 'Block promotor (ID:'.$ID.')';
        }
        else
        {
            $this->promotor_meta->remove($ID, 'block');

            $flash = 'promotor-unblock';
            $action = 'Unblock promotor (ID:'.$ID.')';
        }
        
        // Log request
        $this->log->record($request->userID, $action);

        // Set session and redirect
        $request->session()->flash($flash, '');
        return back();
    }
    
    /**
     * Handle logout promoter request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function logoutHandle(Request $request)
    {
        // Check parameter
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Check promotor
        $promotor = $this->promotor->getOne($ID);
        
        if (!$promotor)
        {
            return App::abort(404);
        }
        
        // Update promoter user token
        $this->promotor->update($ID, ['user_token' => '']);
        
        // Redirect back with flash session
        $request->session()->flash('promotor-logout', '');
        return back();
    }

    /**
     * Handle reset promotor request 
     *
     * @access public
     * @param  Request $request 
     * @return response          
     */
    public function resetHandle(Request $request) 
    {
        // Get ID
        $ID = $request->get('ID', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }

        // Check promotor
        $promotor = $this->promotor->getOne($ID);
        
        if (!$promotor)
        {
            return App::abort(404);
        }
        
        // Change promotor password
        $this->promotor->update($ID, ['password' => 'havas']);
        
        // Redirect back with flash
        $request->session()->flash('promotor-reset-password', '');
        return back();
    }
    
    /**
     * Make promotor to be non-active
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function nonActiveHandle(Request $request)
    {
        // Check parameter
        $ID = $request->get('ID', false);

        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Check promotor
        $promotor = $this->promotor->getOne($ID);

        if(!$promotor)
        {
            return App::abort(404);
        }
        
        // Update user data
        $this->promotor->update($ID, [
            'dealer_ID'     => '0',
            'user_token'    => '',
            'password'      => 'none',
            'type'          => 'non-active'
        ]);
        
        // Remove user sales target
        $this->promotorTarget->removeByPromotor($ID, date('Y-m'));
        
        // Redirect back with flash session
        $request->session()->flash('promotor-non-active', '');
        return back();
    }
}