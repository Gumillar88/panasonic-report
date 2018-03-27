<?php

namespace App\Http\Controllers\Dashboard;

use App;
use Crypt;
use Hash;
use Mail;
use Carbon\Carbon;

use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Models\DashboardDataModel;
use App\Http\Models\DashboardAccountModel;
use App\Http\Models\DashboardTokenModel;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;


use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_NumberFormat;

class DashboardCompetitorController extends Controller
{
	/**
     * Dashboard data model container
     *
     * @access protected
     */
    protected $data;
    
    /**
     * Dashboard account model container
     *
     * @access protected
     */
    protected $account;
    
    /**
     * Dashboard token model container
     *
     * @access protected
     */
    protected $token;

    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;

    /**
     * branch model container
     *
     * @access protected
     */
    protected $branch;

    /**
     * Dealer Account model container
     *
     * @access protected
     */
    protected $dealer_account;

    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;

    /**
     * Channel model container
     *
     * @access protected
     */
    protected $dealer_channel;
    
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $competitorBrand;
    
    /**
     * Competitor price model container
     *
     * @access protected
     */
    protected $competitorPrice;
    
    /**
     * Static value for account all nation
     *
     * @access protected
     */
    protected $accountAllNation = [
        'Electronic City',
        'Best Denki',
        'Electronic Solution',
        'Hypermart',
        'Carrefour',
        'Lottemart',
        'Courts',
        'Depo Bangunan',
        'Mitra 10',
        'Save Max',
        'White Brown',
        'Giant',
        'Lulu'
    ];

    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
	public function __construct()
    {
        $this->data             = new DashboardDataModel();
        $this->account          = new DashboardAccountModel();
        $this->token            = new DashboardTokenModel();
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealer_account   = new DealerAccountModel();
        $this->dealer           = new DealerModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->competitorBrand  = new CompetitorBrandModel();
        $this->competitorPrice  = new CompetitorPriceModel();
    }

    /**
     * Validate code to dashboard account data
     *
     * @access public
     * @param String $code
     * @return Response
     */
    private function _validateCode($code)
    {
        if (env('APP_ENV') === 'local')
        {
            return true;
        }
        
        if (!$code)
        {
            return false;
        }
        
        // Check code
        $tokenData = $this->token->getByToken($code);
        
        if (!$tokenData)
        {
            return false;
        }
        
        $account = $this->account->getOne($tokenData->dashboard_account_ID);
        
        if (!$account)
        {
            return false;
        }
        
        return true;
    }

    /**
     * Display competitor report data table
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function competitorReport(Request $request)
    {
        // Get code
        $code       = $request->get('code', false);
        $type       = $request->get('type', false);
        $value      = $request->get('value', false);
        $brandID    = $request->get('brandID', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }
        
        if (!$type)
        {
            return App::abort(404);
        }
        
        if (!$value)
        {
            return App::abort(404);
        }
        
        if ($brandID === false)
        {
            return App::abort(404);
        }
        
        // Set month and get data
        $month = $request->get('month', date('Y-m'));
        
        if ($brandID === '0')
        {
            $brandID = false;
        }
        

        $data                   = $this->competitorPrice->getCompiledIndex($month, $brandID, $type, $value);
        $competitorBrand        = $this->competitorBrand->getOne($brandID);
        $branch                 = $this->branch->getOne($value);

        
        // Set brand field
        $brandField = '';
        
        if ($brandID === false)
        {
            $brandField = '<th>Brand</th>';
        }
        
        $accountField = '<th>Account</th>';
        
        if ($type === 'account-all')
        {
            $accountField = '';
        }
        
        // Set html
        $html = '
            <h3>Panasonic Competitor Report | '.$branch->name.' | '.$competitorBrand->name.'</h3>
            <input type="hidden" name="id" id="id" value="'.$brandID.'" />
            <p><a id="competitor-report-download" class="btn">Download</a></p>
            <table border="1" id="table-competitor">

            <thead>
                <tr>
                    <th>Branch</th>
                    '.$accountField.'
                    <th>Dealer</th>
                    '.$brandField.'
                    <th>Competitor Brand</th>
                    <th>Product Category</th>
                    <th>Competitor Model</th>
                    <th>Panasonic Model</th>
                    <th>Normal Price</th>
                    <th>Promo Price</th>
                    <th>Discount (%)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data as $item)
        {
            // Set account
            $account = '(none)';
            
            if ($item->account)
            {
                $account = $item->account;
            }
            
            // Set brand custom 
            $brandField = '';
            
            if ($brandID === false)
            {
                if ($item->brand_custom === '')
                {
                    $item->brand_custom = '-';
                }
                
                $brandField = '<td>'.$item->brand_custom.'</td>';
            }
            
            $accountField = '<td>'.$account.'</td>';
            
            if ($type === 'account-all')
            {
                $accountField = '';
            }
            
            // Set discount
            $discount = floor((($item->price_normal - $item->price_promo) / $item->price_normal)*10000)/100;
            
            $html .= '
                <tr>
                    <td>'.$item->branch.'</td>
                    '.$accountField.'
                    <td>'.$item->dealer.'</td>
                    '.$brandField.'
                    <td>'.$competitorBrand->name.'</td>
                    <td>'.$item->category.'</td>
                    <td>'.$item->model_name.'</td>
                    <td>'.$item->product_model.'</td>
                    <td>'.number_format($item->price_normal).'</td>
                    <td>'.number_format($item->price_promo).'</td>
                    <td>'.$discount.'</td>
                    <td>'.$item->date.'</td>
                </tr>';
        }
        
        // Close html

        $html .= '</tbody>';

        $html .= '</tbody></table>';

        
        // Return response
        return response()->json($html);
    }


    /**
     * Handle download report
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function downloadCompetitorReport(Request $request)
    {   
        // Get code
        $code       = $request->get('code', false);
        $type       = $request->get('type', false);
        $value      = $request->get('value', false);
        $brandID    = $request->get('brandID', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }
        
        if (!$type)
        {
            return App::abort(404);
        }
        
        if (!$value)
        {
            return App::abort(404);
        }
        
        if ($brandID === false)
        {
            return App::abort(404);
        }
        
        // Set month and get data
        $month = $request->get('month', date('Y-m'));
        
        if ($brandID === '0')
        {
            $brandID = false;
        }
        
        // Data sheet 1
        $data                   = $this->competitorPrice->getCompiledIndex($month, $brandID, $type, $value);
        $competitorBrand        = $this->competitorBrand->getOne($brandID);
        $branch                 = $this->branch->getOne($value);

        $objPHPExcel = new PHPExcel();

        // Set aplhabet for looping the column
        $alphabet = range('A', 'Z');
        $time = $request->get('month', date('01-m-Y'));

        
        // Set title
        $title = ' - '.$branch->name.' - '.$competitorBrand->name.' - '.date('F Y',strtotime($time));
        
        // Set currency code
        $currencyCode = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)';

        // Set sheet name
        $objPHPExcel->getActiveSheet()->setTitle('Competitor Report');

        // Set title in sheet 1
        $objPHPExcel->getActiveSheet()->setCellValue('a2', 'Panasonic Competitor Report '.$title);
        $objPHPExcel->getActiveSheet()->getStyle("a2")->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle("a2")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->mergeCells('a2:r2');


        $columnTitle = [];
        $columnTitle[1] = "Branch";
        $columnTitle[2] = "Account";
        $columnTitle[3] = "Dealer";
        $columnTitle[4] = "Custom Brand";
        $columnTitle[5] = "Competitor Brand";
        $columnTitle[6] = "Product Category";
        $columnTitle[7] = "Competitor Model";
        $columnTitle[8] = "Panasonic Model";
        $columnTitle[9] = "Normal Price";
        $columnTitle[10] = "Promo Price";
        $columnTitle[11] = "Discount (%)";
        $columnTitle[12] = "Date";

        $row = 3; // 1-based index
        $col = 0;

        // Add array to sheet
        foreach ($columnTitle as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }

        // Style 
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'A7DBD8')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')

                 )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );

        // Set style
        $objPHPExcel->getActiveSheet()->getStyle("A3:".$alphabet[count($columnTitle)-1]."3")->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(40);

        $row = 4; // 1-based index


        // Add array to sheet
        foreach ($data as $key => $value) {

            // Set account
            $account = '(none)';
            
            if ($value->account)
            {
                $account = $value->account;
            }

            // Set brand custom 
            $brandField = '';
            
            if ($brandID === false)
            {
                if ($value->brand_custom === '')
                {
                    $value->brand_custom = '-';
                }
                
                $brandField = $value->brand_custom;
            }

            // Set discount
            $discount = floor((($value->price_normal - $value->price_promo) / $value->price_normal)*10000)/100;
            
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $value->branch);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $account);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $value->dealer);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $brandField);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $competitorBrand->name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $value->category);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $value->model_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $value->product_model);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, number_format($value->price_normal));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, number_format($value->price_promo));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $discount);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $value->date);
            
            $row++;
        }

        // Set Width Auto
        foreach ($alphabet as $key => $value) 
        {
            if($key <= count($columnTitle)-1)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setAutoSize(true);
            }
        }


        // Back to Sheet 1
        $objPHPExcel->setActiveSheetIndex(0);

        // Create Excel file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $fileName = 'Panasonic Competitor Report '.date('F Y',strtotime($time)).' - '.$branch->name.'.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        return $objWriter->save('php://output');

    }
    
}
