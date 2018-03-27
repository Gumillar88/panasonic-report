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

class DashboardDataController extends Controller
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
     * Handle download report
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function downloadReport(Request $request)
    {   
        // Get code
        $code = $request->get('code', false);
        $type = $request->get('type', false);
        
        if (!$type)
        {
            return App::abort(404);
        }
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }
        
        // Set initial parameter
        $params = [];
        $name   = 'All branch';
        
        // Collect month
        if ($request->has('month'))
        {
            $params['month'] = $request->get('month', date('Y-m'));
        }
        
        // Get spreadsheet name
        if ($type === 'branch')
        {
            $params['branch_ID'] = $request->get('branchID' , 0);
            
            // Get branch ID
            $branch = $this->branch->getOne($params['branch_ID']);

            if (!$branch)
            {
                return App::abort(404);
            }

            $name = $branch->name;
        }
        else if ($type === 'region')
        {
            $params['region_ID'] = $request->get('regionID', 0);
            
            $region = $this->region->getOne($params['region_ID']);
            
            if (!$region)
            {
                return App::abort(404);
            }
            
            $name = $region->name;
        }
        else if ($type === 'account')
        {
            $params['account_ID'] = $request->get('accountID', 0);
            
            $account = $this->dealer_account->getOne($params['account_ID']);
            
            if (!$account)
            {
                return App::abort(404);
            }
            
            $name = $account->name;
        }
        else if ($type === 'account-all')
        {
            $params['account_name'] = $request->get('accountID', '');
            
            if (!$params['account_name'])
            {
                return App::abort(404);
            }
            
            $name = ucwords(str_replace('-', ' ', $params['account_name'])).' - All Nation';
        }

        $objPHPExcel = new PHPExcel();

        // Set aplhabet for looping the column
        $alphabet = range('A', 'Z');
        $time = $request->get('month', date('01-m-Y'));
        
        // Set title
        $title = ' - '.$name.' - '.date('F Y',strtotime($time));
        
        // Set currency code
        $currencyCode = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)';

        // Set sheet name
        $objPHPExcel->getActiveSheet()->setTitle('Sell Out');

        // Set title in sheet 1
        $objPHPExcel->getActiveSheet()->setCellValue('a2', 'Panasonic Promoter'.$title);
        $objPHPExcel->getActiveSheet()->getStyle("a2")->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle("a2")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->mergeCells('a2:r2');


        $columnTitle = [];
        $columnTitle[1] = "Branch";
        $columnTitle[2] = "Team Leader";
        $columnTitle[3] = "Periods";
        $columnTitle[4] = "Week";
        $columnTitle[5] = "Date";
        $columnTitle[6] = "Cust. Name";
        $columnTitle[7] = "Address";
        $columnTitle[8] = "Cust Phone No.";
        $columnTitle[9] = "Promotors Name";
        $columnTitle[10] = "Promotors Employee No.";
        $columnTitle[11] = "Promotors Join Date";
        $columnTitle[12] = "Dealers Name.";
        $columnTitle[13] = "Store Name";
        $columnTitle[14] = "Product Category";
        $columnTitle[15] = "Product Model Name";
        $columnTitle[16] = "Product Model Incentive";
        $columnTitle[17] = "Qty";
        $columnTitle[18] = "Selling Price Promotors";
        $columnTitle[19] = "Total";

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
                'color' => array('rgb' => 'FFFF00')
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

        // Data shhet 1
        $data = $this->data->report($params);

        $row = 4; // 1-based index

        // Add array to sheet
        foreach ($data as $key => $value) {
            
            // Account all nation fallback
            if ($type === 'account-all')
            {
                $value->dealer_account_name = ucwords(str_replace('-', ' ', $params['account_name']));
            }
            
            // Check product name
            $productName = $value->product_name;
            
            if (!$productName)
            {
                $productName = $value->custom_product_name;
            }
            
            // Generate week number
            $weekNumber = Carbon::parse($value->date_string)->weekOfMonth;
            
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $value->branch_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $value->team_leader);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $value->period);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $weekNumber);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $value->date_string);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $value->customer_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $value->customer_phone);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $value->promotor_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $value->promotor_ID);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $value->dealer_account_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $value->dealer_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $value->category_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $productName);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $value->incentive);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $value->qty);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $value->price);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $value->qty*$value->price);
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



        /*===============================================
         *
         * Data Pivot Sheet Sheet
         *
         *===============================================*/
        
        // Sheet 2
        $objPHPExcel->createSheet();

        // Move to new Sheet
        $objPHPExcel->setActiveSheetIndex(1);
        
        $activeSheet = $objPHPExcel->getActiveSheet();
        
        // Rename 2nd sheet
        $activeSheet->setTitle('Pivot Table');

        // Set Title Sheet 
        $activeSheet->setCellValue('a2', 'Store Data'.$title);
        $activeSheet->getStyle("a2")->getFont()->setSize(18);
        $activeSheet->getStyle("a2")->getFont()->setBold(true);
        $activeSheet->mergeCells('a2:f2');

        // Name content 
        $columnTitlePivot = [];
        $columnTitlePivot[1] = "STORE";
        $columnTitlePivot[2] = "Sum Of Qty";
        $columnTitlePivot[3] = "Sum of Selling Price Promotor";
        $columnTitlePivot[4] = "Total Promotor";
        $columnTitlePivot[5] = "Target Promotor";
        $columnTitlePivot[6] = "Achievement (%)";

        $row = 3; // 1-based index
        $col = 0;

        // Add data 
        foreach ($columnTitlePivot as $key => $value) {
            $activeSheet->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }

        // Style sheet 2
        $stylePivot = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ff8080')
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

        // Apply Style
        $activeSheet->getStyle("A3:".$alphabet[count($columnTitlePivot)-1]."3")->applyFromArray($stylePivot);
        $activeSheet->getRowDimension('3')->setRowHeight(40);

        // Data Sheet 2
        $dataPivot = $this->data->reportPivot($params);
        $dataTarget = $this->data->reportPromotorTarget($params);
        
        $compiledPivotData = [];
        $promotorData = [];
        
        
        // Compile sellout data to as pivot data
        foreach ($data as $key => $value) {
            
            if(!array_key_exists($value->dealer_name, $compiledPivotData))
            {
                $compiledPivotData[$value->dealer_name] = [
                    'dealer_name'       => $value->dealer_name,
                    'sum_qty'           => 0,
                    'sum_selling_price' => 0,
                    'total_promotor'    => 0,
                    'total_target'      => 0
                ];
            }
            
            $compiledPivotData[$value->dealer_name]['sum_qty'] += $value->qty;
            $compiledPivotData[$value->dealer_name]['sum_selling_price'] += ($value->qty*$value->price);
            
            // Compile promotor data
            $promotorData[$value->promotor_ID] = $value->dealer_name;
        }

        // Compile target
        foreach ($dataPivot['target'] as $key => $value) {
            if(array_key_exists($value->dealer_name, $compiledPivotData))
            {
                $compiledPivotData[$value->dealer_name]['total_target'] = $value->total_target;
            }
        }
        
        // Compile promotor
        foreach ($promotorData as $promotor_ID => $dealer_name) 
        {
            if(array_key_exists($dealer_name, $compiledPivotData))
            {
                $compiledPivotData[$dealer_name]['total_promotor']++;
            }
        }
        
        // Sort compiled data
        ksort($compiledPivotData);
        
        $row = 4; // 1-based index

        // Add array to sheet
        foreach ($compiledPivotData as $key => $value) 
        {
            $activeSheet->setCellValue('A'.$row, $value['dealer_name']);
            $activeSheet->setCellValue('B'.$row, $value['sum_qty']);
            $activeSheet->setCellValue('C'.$row, $value['sum_selling_price']);
            $activeSheet->setCellValue('D'.$row, $value['total_promotor']);
            $activeSheet->setCellValue('E'.$row, $value['total_target']);
            $activeSheet->setCellValue('F'.$row,'=(C'.$row.'/E'.$row.')');
            
            // Accounting format
            $activeSheet->getStyle('C'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('E'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            // Percentage format
            $activeSheet->getStyle('F'.$row)
                        ->getNumberFormat()->applyFromArray( 
                            array( 
                                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                            )
                        );
            $row++;
        }

        $activeSheet->setCellValue('A'.$row, 'Grand Total');
        $activeSheet->setCellValue('B'.$row,'=SUM(B4:B'.($row-1).')');
        $activeSheet->setCellValue('C'.$row,'=SUM(C4:C'.($row-1).')');
        $activeSheet->setCellValue('D'.$row,'=SUM(D4:D'.($row-1).')');
        $activeSheet->setCellValue('E'.$row,'=SUM(E4:E'.($row-1).')');
        $activeSheet->setCellValue('F'.$row,'=(C'.$row.'/E'.$row.')');
        

        // Accounting format
        $activeSheet->getStyle('C'.$row)
            ->getNumberFormat()
            ->setFormatCode($currencyCode);

        $activeSheet->getStyle('E'.$row)
            ->getNumberFormat()
            ->setFormatCode($currencyCode);
        
        // Percentage format
        $activeSheet->getStyle('F'.$row)
                    ->getNumberFormat()->applyFromArray( 
                        array( 
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                    );
        
        $activeSheet->getStyle('A'.$row.':'.$alphabet[count($columnTitlePivot)-1].$row)->applyFromArray($stylePivot);
        
        $row++;


        // Set Width Auto
        foreach ($alphabet as $key => $value) {
            if($key <= count($columnTitlePivot)-1)
            {
                $activeSheet->getColumnDimension($value)->setAutoSize(true);
            }
        }


        /*===============================================
         *
         * Strategic Achievement Sheet
         *
         *===============================================*/
        
        // Sheet 3
        $objPHPExcel->createSheet();

        // Move to new Sheet
        $objPHPExcel->setActiveSheetIndex(2);
        
        $activeSheet = $objPHPExcel->getActiveSheet();
        
        // Rename 3rd sheet
        $activeSheet->setTitle('Strategic Achievement');

        // Set Title Sheet 
        $activeSheet->setCellValue('a2', 'Strategic Achievement'.$title);
        $activeSheet->getStyle("a2")->getFont()->setSize(18);
        $activeSheet->getStyle("a2")->getFont()->setBold(true);
        $activeSheet->mergeCells('a2:f2');
        $activeSheet->getDefaultColumnDimension()->setWidth(25);

        // Name content 
        $columnTitleAchievement2 = [];
        $columnTitleAchievement2[1] = "Name";
        $columnTitleAchievement2[2] = "Dealer";
        $columnTitleAchievement2[3] = "Week 1 (1-7)";
        $columnTitleAchievement2[4] = "";
        $columnTitleAchievement2[5] = "Week 2 (8-14)";
        $columnTitleAchievement2[6] = "";
        $columnTitleAchievement2[7] = "Week 3 (15-21)";
        $columnTitleAchievement2[8] = "";
        $columnTitleAchievement2[9] = "Week 4 (22 - end)";
        $columnTitleAchievement2[10] = "";
        $columnTitleAchievement2[11] = "Total";
        $columnTitleAchievement2[12] = "Target";

        $row2 = 3; // 1-based index
        $col = 0;

        // Add data 
        foreach ($columnTitleAchievement2 as $key => $value) {
            $activeSheet->setCellValueByColumnAndRow($col, $row2, $value);
            $col++;
        }

        // Name content 
        $columnTitleAchievement3 = [];
        $columnTitleAchievement3[1] = "";
        $columnTitleAchievement3[2] = "";
        $columnTitleAchievement3[3] = "Sales";
        $columnTitleAchievement3[4] = "Achievement (25%)";
        $columnTitleAchievement3[5] = "Sales";
        $columnTitleAchievement3[6] = "Achievement (25%)";
        $columnTitleAchievement3[7] = "Sales";
        $columnTitleAchievement3[8] = "Achievement (25%)";
        $columnTitleAchievement3[9] = "Sales";
        $columnTitleAchievement3[10] = "Achievement (25%)";
        $columnTitleAchievement3[11] = "";
        $columnTitleAchievement3[12] = "";

        $row3 = 4; // 1-based index
        $col = 0;

        // Add data 
        foreach ($columnTitleAchievement3 as $key => $value) {
            $activeSheet->setCellValueByColumnAndRow($col, $row3, $value);
            $col++;
        }

        // Style sheet 2
        $stylePivot = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '92d050')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );

        // Apply Style
        $activeSheet->getStyle("A3:".$alphabet[count($columnTitleAchievement3)-1]."4")->applyFromArray($stylePivot);
        $activeSheet->mergeCells('A3:A4');
        $activeSheet->mergeCells('B3:B4');
        $activeSheet->mergeCells('C3:D3');
        $activeSheet->mergeCells('E3:F3');
        $activeSheet->mergeCells('G3:H3');
        $activeSheet->mergeCells('I3:J3');
        $activeSheet->mergeCells('K3:K4');
        $activeSheet->mergeCells('L3:L4');

        // Add array to sheet
        $object = [];

        $objectTarget = [];
        
        // Target
        foreach ($dataTarget as $key => $value) 
        {
            $objectTarget[$value->promotor_ID] = $value->total_target;
        }
        
        foreach ($data as $key => $value) 
        {
            // Set initial value
            if (!array_key_exists($value->promotor_ID, $object))
            {
                $object[$value->promotor_ID] = [
                    "promotor"              => $value->promotor_name,
                    "dealer"                => '',
                    "sales_week_1"          => 0,
                    "achievement_week_1"    => 0,
                    "sales_week_2"          => 0,
                    "achievement_week_2"    => 0,
                    "sales_week_3"          => 0,
                    "achievement_week_3"    => 0,
                    "sales_week_4"          => 0,
                    "achievement_week_4"    => 0,
                    "sales_week_total"      => 0,
                    "total_target"          => 0
                ];
            }
            
            // Set target
            if (
                $object[$value->promotor_ID]['total_target'] === 0 && 
                array_key_exists($value->promotor_ID, $objectTarget)
            ) {
                $object[$value->promotor_ID]['total_target'] = $objectTarget[$value->promotor_ID];
            }
            
            // Get date
            $sortdate = (int) date('j', strtotime($value->date));
            
            // First week
            if ($sortdate <= 7)
            {
                $object[$value->promotor_ID]['sales_week_1'] += ($value->price * $value->qty);
            }
            
            // Second week
            if ($sortdate > 7 && $sortdate <= 14)
            {
                $object[$value->promotor_ID]['sales_week_2'] += ($value->price * $value->qty);
            }
            
            // Third week
            if ($sortdate > 14 && $sortdate <= 21)
            {
                $object[$value->promotor_ID]['sales_week_3'] += ($value->price * $value->qty);
            }

            // Third week
            if ($sortdate > 21)
            {
                $object[$value->promotor_ID]['sales_week_4'] += ($value->price * $value->qty);
            }
            
            // All week
            $object[$value->promotor_ID]['sales_week_total'] += ($value->price * $value->qty);
                
            // Reset dealer value
            $object[$value->promotor_ID]['dealer'] = $value->dealer_name;

        }
        
        $styleAchievement = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FAA8A1')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );
        
        // Style sheet 3 Achievement
        $styleAchievementSuccess = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'B4D5F8')
            )
        );
        
        
        $activeSheet = $activeSheet;
        $row = 5;
        
        // Add array to sheet
        foreach ($object as $key => $value) 
        {
            // Set achievement
            if ($value['total_target'] != 0)
            {
                $value['achievement_week_1'] = $value['sales_week_1']/$value['total_target'];
                $value['achievement_week_2'] = $value['sales_week_2']/$value['total_target'];
                $value['achievement_week_3'] = $value['sales_week_3']/$value['total_target'];
                $value['achievement_week_4'] = $value['sales_week_4']/$value['total_target'];
            }
            
            $activeSheet->setCellValue('A'.$row, $value['promotor']); 
            $activeSheet->setCellValue('B'.$row, $value['dealer']);
            $activeSheet->setCellValue('C'.$row, $value['sales_week_1']);
            $activeSheet->setCellValue('D'.$row, $value['achievement_week_1']);
            $activeSheet->setCellValue('E'.$row, $value['sales_week_2']);
            $activeSheet->setCellValue('F'.$row, $value['achievement_week_2']);
            $activeSheet->setCellValue('G'.$row, $value['sales_week_3']);
            $activeSheet->setCellValue('H'.$row, $value['achievement_week_3']);
            $activeSheet->setCellValue('I'.$row, $value['sales_week_4']);
            $activeSheet->setCellValue('J'.$row, $value['achievement_week_4']);
            $activeSheet->setCellValue('K'.$row, $value['sales_week_total']);
            $activeSheet->setCellValue('L'.$row, $value['total_target']);
            
            // Accounting format
            $activeSheet->getStyle('C'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('E'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('G'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('I'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('K'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);

            $activeSheet->getStyle('L'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            // Percentage format
            $activeSheet->getStyle('D'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            $activeSheet->getStyle('F'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            $activeSheet->getStyle('H'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            $activeSheet->getStyle('J'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            // Set style
            $activeSheet->getStyle('D'.$row)->applyFromArray($styleAchievement);
            $activeSheet->getStyle('F'.$row)->applyFromArray($styleAchievement);
            $activeSheet->getStyle('H'.$row)->applyFromArray($styleAchievement);
            $activeSheet->getStyle('J'.$row)->applyFromArray($styleAchievement);
            
            if ($value['achievement_week_1'] >= 0.25)
            {
                $activeSheet->getStyle('D'.$row)->applyFromArray($styleAchievementSuccess);
            }

            if ($value['achievement_week_2'] >= 0.25)
            {
                $activeSheet->getStyle('F'.$row)->applyFromArray($styleAchievementSuccess);
            }

            if ($value['achievement_week_3'] >= 0.25)
            {
                $activeSheet->getStyle('H'.$row)->applyFromArray($styleAchievementSuccess);
            }

            if ($value['achievement_week_4'] >= 0.25)
            {
                $activeSheet->getStyle('J'.$row)->applyFromArray($styleAchievementSuccess);
            }

            $row++;
        }
        
        // Apply border
        $borderStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                 )
            )
        );

        $activeSheet->getStyle("A3:L".($row-1))->applyFromArray($borderStyle);
        
        // Auto size column
        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);



        // Back to Sheet 1
        $objPHPExcel->setActiveSheetIndex(0);

        // Create Excel file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $fileName = 'Panasonic Report '.date('F Y',strtotime($time)).' - '.$name.'.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        return $objWriter->save('php://output');

    }

    /**
     * Get all account data explore
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function dataExplore(Request $request)
    {
        $type = 'Explore';

        if (!$request->ajax())
        {
            return App::abort(404);
        }
        
        // Get code
        $code = $request->get('code', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }


            // Set initial parameter
            $params = [];

            //set initial time 
            $semester = [
                'S1' => ['01', '02', '03', '04', '05', '06'],
                'S2' => ['07', '08', '09', '10', '11', '12'],
            ];

            $quarter = [
                'Q1' => ['01', '02', '03'],
                'Q2' => ['04', '05', '06'],
                'Q3' => ['07', '08', '09'],
                'Q4' => ['10', '11', '12'],
            ];
            
            // Set initial parameter
            $targetMonths   = [];
            $startDate      = date('Y-m').'-01';
            $finishDate     = date('Y-m').'-31';
            
            $type = $request->get('timeType');
            
            if (in_array($type, ['semester','quarter']))
            {
                // Collect month
                $time   = $request->get('timeValue');
                $time   = explode('-', $time);
                $year   = $time[0];
                $month  = $time[1];
            }

            if ($type === 'semester')
            {
                $startDate = $year.'-'.$semester[$month][0].'-01';
                $finishDate = $year.'-'.$semester[$month][5].'-31';
                
                foreach($semester[$month] as $item)
                {
                    $targetMonths[] = $year.'-'.$item;
                }
            }        
            else if ($type === 'quarter')
            {
                $startDate = $year.'-'.$quarter[$month][0].'-01';
                $finishDate = $year.'-'.$quarter[$month][2].'-31';
                
                foreach($quarter[$month] as $item)
                {
                    $targetMonths[] = $year.'-'.$item;
                }
            }
            else if ($type === 'month')
            {
                $monthValue    =  $request->get('timeValue', date('Y-m'));
                
                $startDate = $monthValue.'-01';
                $finishDate = $monthValue.'-31';
                
                $targetMonths[] = $monthValue;
            }
            else if ($type === 'year')
            {
                $startDate = date('Y').'-01-01';
                $finishDate = date('Y').'-12-31';
            }
            
            
            $params = [];

            // Get data based on region id
            if ($request->has('regionID'))
            {
                $params['region_ID'] = $request->get('regionID');
            }
            
            // Get data based on branch id
            if ($request->has('branchID'))
            {
                $params['branch_ID'] = $request->get('branchID');
            }
            
            // Get data based on account id
            if ($request->has('accountID'))
            {
                $params['account_ID'] = $request->get('accountID');
            }

            // Get data based on dealer id
            if ($request->has('dealerID'))
            {   
                $dealerID = $request->get('dealerID');

                if ($dealerID != 0)
                {
                    $params['dealer_ID'] = $request->get('dealerID');
                }
            }

            // Get data based on channel id
            if ($request->has('channelID'))
            {
                $params['dealer_channel_ID'] = $request->get('channelID');
            }

            // Fetch data
            $dataSales          = $this->data->dataExploreSales($startDate, $finishDate, $params);
            $dataTarget         = $this->data->dataExploreTarget($targetMonths);
            $dataSalesPromotor  = $this->data->dataExploreSalesPromotor($startDate, $finishDate, $params);
            $dataTargetPromotor = $this->data->dataExploreTargetPromotor($targetMonths);
            
            // Data container
            $branches   = [];
            $accounts   = [];
            $dealers    = [];
            $promotors  = [];
            
            $dealerTarget   = [];
            $promotorTarget = [];
            
            // Compiled data target
            foreach ($dataTarget as $target)
            {
                $dealerTarget[$target->dealer_ID] = $target->total;
            }
            
            // Compiled sales data
            foreach ($dataSales as $sales) 
            {
                // Push branch
                if (!array_key_exists($sales->branch_ID, $branches))
                {
                    $branches[$sales->branch_ID] = [
                        'name'      => $sales->branch_name,
                        'sales'     => 0,
                        'target'    => 0,
                        'count'     => 0,
                        'accounts'  => [],
                        'dealers'   => [],
                    ];
                }
                
                $branches[$sales->branch_ID]['sales'] += $sales->total;
                
                // Push account
                if (
                    $sales->account_ID !== null &&
                    !array_key_exists($sales->account_ID, $accounts)
                )
                {
                    $accounts[$sales->account_ID] = [
                        'name'      => $sales->account_name,
                        'sales'     => 0,
                        'target'    => 0,
                        'count'     => 0,
                        'branch_ID' => $sales->branch_ID,
                        'dealers'   => [],
                    ];
                }
                
                if (array_key_exists($sales->account_ID, $accounts))
                {
                    $accounts[$sales->account_ID]['sales'] += $sales->total;
                }
                
                // Push dealers
                if (!array_key_exists($sales->dealer_ID, $dealers))
                {
                    $dealers[$sales->dealer_ID] = [
                        'name'      => $sales->dealer_name,
                        'sales'     => 0,
                        'target'    => 0,
                        'count'     => 0,
                        'account_ID'=> $sales->account_ID,
                        'branch_ID' => $sales->branch_ID,
                        'promotors' => [],
                    ];
                }
                
                $dealers[$sales->dealer_ID]['sales'] += $sales->total;
                
                if (array_key_exists($sales->dealer_ID, $dealerTarget))
                {
                    $dealers[$sales->dealer_ID]['target'] = $dealerTarget[$sales->dealer_ID];
                }
                
            }
            
            // Compiled target promotor
            foreach ($dataTargetPromotor as $target)
            {
                $promotorTarget[$target->promotor_ID] = $target->total;
            }
            
            // Compiled data promotor
            foreach ($dataSalesPromotor as $sales)
            {
                $target = 0;
                
                if (array_key_exists($sales->promotor_ID, $promotorTarget))
                {
                    $target = $promotorTarget[$sales->promotor_ID];
                }
                
                $dealers[$sales->dealer_ID]['promotors'][] = [
                    'name'      => $sales->promotor_name,
                    'sales'     => $sales->total,
                    'target'    => $target
                ];
                
                $dealers[$sales->dealer_ID]['count']++;
            }
            
            // Push dealer to branch or account
            foreach ($dealers as $ID => $dealer)
            {
                if ($dealer['account_ID'] !== null)
                {
                    $accounts[$dealer['account_ID']]['dealers'][] = $dealer;
                    $accounts[$dealer['account_ID']]['count'] += $dealer['count'];
                    $accounts[$dealer['account_ID']]['target'] += $dealer['target'];
                }
                else
                {
                    $branches[$dealer['branch_ID']]['dealers'][] = $dealer;
                    $branches[$dealer['branch_ID']]['count'] += $dealer['count'];
                    $branches[$dealer['branch_ID']]['target'] += $dealer['target'];
                }
            }
            
            // Push account to branch
            foreach ($accounts as $ID => $account)
            {
                $branches[$account['branch_ID']]['accounts'][] = $account;
                $branches[$account['branch_ID']]['count'] += $account['count'];
                $branches[$account['branch_ID']]['target'] += $account['target'];
            }
            
            $html = '<thead><tr><th>Branch</th><th>Account</th><th>Dealer</th><th>Promotor</th></tr></thead><tbody>';
            
            foreach ($branches as $branch) 
            {   
                $colspan = 1;
                $branchBackground = '#C5EFF7';
                
                if (count($branch['dealers']) > 0)
                {
                    $colspan = 2;
                }
                
                $branch_achievement = 100;
                
                if ($branch['target'] != 0)
                {
                    $branch_achievement = round(($branch['sales']/$branch['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                }
                
                if ($branch_achievement < 100)
                {
                    $branchBackground = '#f9dad6';
                }

                $html .= '<tr>';
                $html .= '
                    <td style="background:'.$branchBackground.'" rowspan="'.$branch['count'].'" colspan="'.$colspan.'">'.
                        '<strong>'.$branch['name'].'</strong>'.
                        '<br />Sales: Rp.'.number_format($branch['sales']).
                        '<br />Target: Rp.'.number_format($branch['target']).
                        '<br />Achievement: '.$branch_achievement.'%'.
                    '</td>';
                
                if (count($branch['accounts']) > 0)
                {
                    foreach ($branch['accounts'] as $key_account => $account) 
                    {
                        
                        $accountBackground = '#C5EFF7';
                        
                        if ($key_account > 0)
                        {
                            $html .= '<tr>';
                        }
                        
                        $account_achievement = 100;

                        if ($account['target'] !== 0)
                        {
                            $account_achievement = round(($account['sales']/$account['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                        }
                        
                        if ($account_achievement < 100)
                        {
                            $accountBackground = '#f9dad6';
                        }

                        $html .= '
                            <td style="background:'.$accountBackground.'" rowspan="'.$account['count'].'">'.
                                '<strong>'.$account['name'].'</strong>'.
                                '<br />Sales: Rp.'.number_format($account['sales']).
                                '<br />Target: Rp.'.number_format($account['target']).
                                '<br />Achievement: '.$account_achievement.'%'.
                            '</td>';
                        
                        foreach ($account['dealers'] as $key_dealer => $dealer) 
                        {
                            
                            $dealerBackground = '#C5EFF7';
                            
                            if ($key_dealer > 0)
                            {
                                $html .= '<tr>';
                            }
                        
                            $dealer_achievement = 100;

                            if ($dealer['target'] != 0)
                            {
                                $dealer_achievement = round(($dealer['sales']/$dealer['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                            }
                        
                            if ($dealer_achievement < 100)
                            {
                                $dealerBackground = '#f9dad6';
                            }
                            
                            $html .= '
                                <td style="background:'.$dealerBackground.'" rowspan="'.$dealer['count'].'">'.
                                    '<strong>'.$dealer['name'].'</strong>'.
                                    '<br />Sales: Rp.'.number_format($dealer['sales']).
                                    '<br />Target: Rp.'.number_format($dealer['target']).
                                    '<br />Achievement: '.$dealer_achievement.'%'.
                                '</td>';
                            
                            foreach ($dealer['promotors'] as $key_promotor => $promotor)
                            {
                                $promotorBackground = '#C5EFF7';
                            
                                if ($key_promotor > 0)
                                {
                                    $html .= '<tr>';
                                }

                                $promotor_achievement = 100;
                                
                                if ($promotor['target'] != 0)
                                {
                                    $promotor_achievement = round(($promotor['sales']/$promotor['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                                }
                                
                                if ($promotor_achievement < 100)
                                {
                                    $promotorBackground = '#f9dad6';
                                }

                                $html .= '
                                <td style="background:'.$dealerBackground.'">'.
                                    '<strong>'.$promotor['name'].'</strong>'.
                                    '<br />Sales: Rp.'.number_format($promotor['sales']).
                                    '<br />Target: Rp.'.number_format($promotor['target']).
                                    '<br />Achievement: '.$promotor_achievement.'%'.
                                '</td>';

                                $html .= '</tr>';

                            }
                        }
                        
                    }
                }
                else
                {
                    foreach ($branch['dealers'] as $key_dealer => $dealer) 
                    {
                        $dealerBackground = '#C5EFF7';
                            
                        if ($key_dealer > 0)
                        {
                            $html .= '<tr>';
                        }
                        
                        $dealer_achievement = 100;

                        if ($dealer['target'] != 0)
                        {
                            $dealer_achievement = round(($dealer['sales']/$dealer['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                        }
                        
                        if ($dealer_achievement < 100)
                        {
                            $dealerBackground = '#f9dad6';
                        }

                        $html .= '
                            <td style="background:'.$dealerBackground.'" rowspan="'.$dealer['count'].'">'.
                                '<strong>'.$dealer['name'].'</strong>'.
                                '<br />Sales: Rp.'.number_format($dealer['sales']).
                                '<br />Target: Rp.'.number_format($dealer['target']).
                                '<br />Achievement: '.$dealer_achievement.'%'.
                            '</td>';
                        
                        foreach ($dealer['promotors'] as $key_promotor => $promotor)
                        {
                            $promotorBackground = '#C5EFF7';
                            
                            if ($key_promotor > 0)
                            {
                                $html .= '<tr>';
                            }
                            
                            $promotor_achievement = 100;
                            
                            if ($promotor['target'] != 0)
                            {
                                $promotor_achievement = round(($promotor['sales']/$promotor['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                            }
                            
                            if ($promotor_achievement < 100)
                            {
                                $promotorBackground = '#f9dad6';
                            }

                            $html .= '
                            <td style="background:'.$dealerBackground.'">'.
                                '<strong>'.$promotor['name'].'</strong>'.
                                '<br />Sales: Rp.'.number_format($promotor['sales']).
                                '<br />Target: Rp.'.number_format($promotor['target']).
                                '<br />Achievement: '.$promotor_achievement.'%'.
                            '</td>';
                            
                            $html .= '</tr>';
                        
                        }
                        
                    }
                }
            }
            
            $html .= '</tbody>';
            
            $data = [
                'sales'          => $html,
            ];

            return response()->json($data);
    }

    /**
     * Get all account data gender
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function dataGender(Request $request)
    {
        $type = 'Gender';
        
        $gender = $this->data->dataPromoterGender();

        $totalGender = [
            'male'              => 0,
            'female'            => 0,
            'total'             => count($gender),
            'persentase_male'   => 0,
            'persentase_female' => 0,
        ];

        $compiledGender = [];

        foreach ($gender as $key => $value) {

            if(!array_key_exists($value->name, $compiledGender))
            {
                $compiledGender[$value->name]  =  [
                    'female'            => 0,
                    'male'              => 0,
                    'total'             => 0,
                    'persentase_male'   => 0,
                    'persentase_female' => 0,
                ];
            }

            if($value->gender == 'male')
            {
                $totalGender['male']++;
                $compiledGender[$value->name]['male']++;
            }else
            {
                $totalGender['female']++;
                $compiledGender[$value->name]['female']++;
            }
            
            $compiledGender[$value->name]['total']++;
            
        }

        foreach ($compiledGender as $key => $value) {
            $compiledGender[$key]['persentase_male'] = round($value['male']/$value['total'] * 100 , 2, PHP_ROUND_HALF_DOWN);
            $compiledGender[$key]['persentase_female'] = round($value['female']/$value['total'] * 100, 2, PHP_ROUND_HALF_DOWN);
        }

        $totalGender['persentase_male'] = round(($totalGender['male']/$totalGender['total']) * 100, 2, PHP_ROUND_HALF_DOWN);
        $totalGender['persentase_female'] = round(($totalGender['female']/$totalGender['total']) * 100, 2, PHP_ROUND_HALF_DOWN);

        $data = [
            'gender'          => $totalGender,
            'genderBranch'   => $compiledGender,
        ];


        return response()->json($data);
    }
}
