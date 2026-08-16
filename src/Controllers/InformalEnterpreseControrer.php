<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\SectorModel;
use App\Models\InformalTradeModel;
 
class InformalEnterpreseControrer extends BaseController {

    /**
     * የምዝገባ ፎርሙን ማሳያ ገጽ
     */
    public function showinterpriseRegisterForm() {
        AuthHelper::checkRole(['officer', 'team_leader', 'system_admin']);

        $sectorModel = new SectorModel($this->db);
        $sectors = $sectorModel->getSectors();

        $data = [
            'title'   => 'JCIMS - የኢ-መደበኛ ንግድ መመዝገቢያ',
            'sectors' => $sectors,
        ];

        $this->render('informal-entrerprise-regstration', $data);
    }
        public function showtoformalinterpriseRegisterForm() {
        AuthHelper::checkRole(['officer', 'team_leader', 'system_admin']);
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        $sectorModel = new SectorModel($this->db);
        $sectors = $sectorModel->getSectors();
        // 2. ኢ-መደበኛ ንግድ መረጃውን በ ID ማምጣት
        $tradeModel = new InformalTradeModel($this->db);
        $tradeData = $tradeModel->getTradeById($id);

        if (!$tradeData) {
            die("የተጠየቀው መረጃ አልተገኘም።");
        }
        
        // 3. መረጃዎችን ለ View ማዘጋጀት
        $data = [
            'title'     => 'JCIMS - የኢ-መደበኛ ንግድ ማስተካከያ',
            'sectors'   => $sectors,
            'tradeData' => $tradeData
        ];
       

        $this->render('informal-entrerprise-regstration-to-formal', $data);
    }

    /**
     * ከ View የመጣውን መረጃ አጣርቶ (Validate) መመዝገቢያ ሜተድ
     */
    public function processRegistration() {
        AuthHelper::checkRole(['officer', 'team_leader', 'system_admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: informal-entrerprise-regstration");
            exit;
        }

        // 1. መረጃዎችን መቀበል እና Sanitization መስራት
        $fullName       = trim($_POST['full_name'] ?? '');
        $gender         = trim($_POST['gender'] ?? '');
        $age            = filter_var($_POST['age'] ?? 0, FILTER_VALIDATE_INT);
        $phone          = trim($_POST['phone'] ?? '');
        $hasKebeleId    = filter_var($_POST['has_kebele_id'] ?? 2, FILTER_VALIDATE_INT);
        $kebeleIdNumber = trim($_POST['kebele_id_number'] ?? '');
         
        $resZone        = trim($_POST['res_zone'] ?? '');
        $resWoreda      = trim($_POST['res_wereda'] ?? '');
        $resKebele      = trim($_POST['res_kebele'] ?? '');

        $workBranchId   =$_SESSION['user']['branch_id'] ?? null; 
        $tradeAreaType  = filter_var($_POST['trade_area_type'] ?? 1, FILTER_VALIDATE_INT);
        $sector         = filter_var($_POST['sector'] ?? 0, FILTER_VALIDATE_INT);
        $subSector      = filter_var($_POST['sub_sector'] ?? 0, FILTER_VALIDATE_INT);
        $jobPosition    = trim($_POST['job_position'] ?? '');
        $startYear      = filter_var($_POST['start_year'] ?? 0, FILTER_VALIDATE_INT);
        $nearbyCenter   = trim($_POST['nearby_center_name'] ?? '');

        // Session Data
        $userBranchId   = $_SESSION['user']['branch_id'] ?? null;
        $userId         = $_SESSION['user']['id'] ?? null;

        // 2. SERVER-SIDE VALIDATION
        $errors = [];

        if (empty($fullName)) {
            $errors[] = "እባክዎን ሙሉ ስም ያስገቡ።";
        }

        if (!in_array($gender, ['Male', 'Female'])) {
            $errors[] = "እባክዎን ትክክለኛ ጾታ ይምረጡ።";
        }

        if (!$age || $age < 15 || $age > 65) {
            $errors[] = "እባክዎን ትክክለኛ ዕድሜ (ከ15-100) ያስገቡ።";
        }

        if ($hasKebeleId == 1 && empty($kebeleIdNumber)) {
            $errors[] = "የቀበሌ መታወቂያ አለ ከተባለ የመታወቂያ ቁጥሩን ማስገባት ግዴታ ነው።";
        }

        if (empty($resKebele)) {
            $errors[] = "የመኖሪያ ቀበሌ ያስገቡ።";
        }

        if (!$subSector) {
            $errors[] = "እባክዎን ንዑስ ዘርፍ ይምረጡ።";
        }

        if (empty($jobPosition)) {
            $errors[] = "የሥራ መደብ ማስገባት ግዴታ ነው።";
        }

        if (!$startYear || $startYear < 1950 || $startYear > (int)date('Y') + 8) { // ለኢትዮጵያ ዘመን አቆጣጠር
            $errors[] = "እባክዎን ትክክለኛ የተሰማራበትን ዓመተ ምህረት ያስገቡ።";
        }

        // ስህተት ካለ ወደ ፎርሙ በመመለስ መልዕክት ማሳየት
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            header("informal-entrerprise-regstration");
            exit;
        }

        // 3. ዳታውን ለ Model ማዘጋጀት
        $registrationData = [
            'branch_id'          => $userBranchId,
            'full_name'          => htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'),
            'gender'             => $gender,
            'age'                => $age,
             
            'reszone'            => htmlspecialchars($resZone, ENT_QUOTES, 'UTF-8'),
            'resworeda'          => htmlspecialchars($resWoreda, ENT_QUOTES, 'UTF-8'),
            'res_kebele'         => htmlspecialchars($resKebele, ENT_QUOTES, 'UTF-8'),
            'phone'              => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
            'trade_area_type'    => $tradeAreaType,
            'has_kebele_id'      => $hasKebeleId,
            'kebele_id_number'   => ($hasKebeleId == 1) ? htmlspecialchars($kebeleIdNumber, ENT_QUOTES, 'UTF-8') : null,
            'start_year'         => $startYear,
            'sub_sector'         => $subSector,
            'job_position'       => htmlspecialchars($jobPosition, ENT_QUOTES, 'UTF-8'),
            'work_branch_id'     => $workBranchId ?: $userBranchId,
            'nearby_center_name' => !empty($nearbyCenter) ? htmlspecialchars($nearbyCenter, ENT_QUOTES, 'UTF-8') : null,
            'regby'              => $userId
        ];

        // 4. ወደ ዳታቤዝ ማስገባት
        try {
            $tradeModel = new InformalTradeModel($this->db);
            $saved = $tradeModel->registerInformalTrader($registrationData);

            if ($saved) {
                $_SESSION['success'] = "የኢ-መደበኛ የተሰማሩ ኢ/ዝ መረጃ በተሳካ ሁኔታ ተመዝግቧል!";
            } else {
                $_SESSION['error'] = "መረጃውን መመዝገብ አልተቻለም። እባክዎ እንደገና ይሞክሩ።";
            }
        } catch (\Exception $e) {
            error_log("Informal Trade Reg Error: " . $e->getMessage());
            $_SESSION['error'] = "የሲስተም ስህተት አጋጥሟል! እባክዎ ትንሽ ቆይተው ይሞክሩ።";
        }

        header("Location: informal-entrerprise-regstration");
        exit;
    }
    /**
     * የተመዘገቡትን የኢ-መደበኛ ንግድ ተሰማሪዎች ዝርዝር ገጽ ማሳያ
     */
    public function showInformalTradeList() {
        AuthHelper::checkRole(['officer', 'team_leader', 'system_admin']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? null;

        $tradeModel = new InformalTradeModel($this->db);
        $tradersList = $tradeModel->getInformalTradersList($myBranchId);

        $data = [
            'title'   => 'JCIMS - የኢ-መደበኛ ንግድ ተሰማሪዎች ዝርዝር',
            'traders' => $tradersList
        ];

        $this->render('informal-trade-list', $data);
    }
       public function showFormalTradeList() {
        AuthHelper::checkRole(['officer', 'team_leader', 'system_admin']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? null;

        $tradeModel = new InformalTradeModel($this->db);
        $enterprises = $tradeModel->getAllTradeDetails($myBranchId);
      
        // መረጃውን ከሞዴል መጥራት
 
        $data = [
            'title'   => 'JCIMS - መደበኛ ንግድ ተሰማሪዎች ዝርዝር',
            'enterprises' => $enterprises
        ];

        $this->render('formal-trade-list', $data);
    }
 // የዝርዝር መረጃ ማሳያ ሜቶድ (Detail Action)
    public function showDetails() {
        // ከ URL የተላለፈውን id መቀበል (ለምሳሌ: details.php?id=5)
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header("Location: login.php"); // መለያ ከሌለ ወደ ዋናው ገጽ ይመልስ
            exit();
        }

        $tradeModel = new InformalTradeModel($this->db);
        // መረጃውን ከሞዴል መጥራት
        $enterprise = $tradeModel->getTradeDetailById($id);

        // መረጃው ካልተገኘ
        if (empty($enterprise)) {
            echo "መረጃው አልተገኘም!";
            exit();
        }

        $enterprise = [
            'title' => 'JCIMS - የኢ-መደበኛ ንግድ ተሰማሪ ዝርዝር',
            'enterprise' => $enterprise
        ];

        // መረጃውን ወደ View መላክ
        $this->render('details', $enterprise);
    }   
    /**
     * የኢ-መደበኛ ንግድ መረጃ ማጥፊያ እና አርካይቭ ማድረጊያ Process
     */
public function deleteInformalTrader() {
        AuthHelper::checkRole(['officer', 'team_leader', 'system_admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: informal-trade-list");
            exit;
        }

        $id     = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        $reason = trim($_POST['reason'] ?? 'ምክንያት አልተጠቀሰም');
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "ትክክለኛ የመታወቂያ ቁጥር አልተገኘም።";
            header("Location: informal-trade-list");
            exit;
        }

        $tradeModel = new InformalTradeModel($this->db);
        $deleted = $tradeModel->archiveAndDeleteTrader($id, $userId, $reason);

        if ($deleted) {
            $_SESSION['success'] = "መረጃው በተሳካ ሁኔታ ተሰርዞል!";
        } 
        // እዚህ ላይ else ውስጥ ሌላ መልዕክት አንፅፍም፤ ምክንያቱም Model ውስጥ $_SESSION['error_message'] ላይ እውነተኛውን Exception ምክንያት ይዞ ስለሚወጣ።

        header("Location: informal-trade-list");
        exit;
    }
public function storeOrUpdate() {
        // የጥያቄው ዓይነት ፖስት (POST) መሆኑን ማረጋገጥ
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: login.php");
            exit();
        }

        
        $errors = [];

        // 1. ግብዓቶችን መቀበል እና ማጽዳት
        $id               = isset($_POST['id']) ? trim($_POST['id']) : '';
        $full_name        = trim($_POST['full_name'] ?? '');
        $gender           = trim($_POST['gender'] ?? '');
        $age              = filter_var($_POST['age'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $nid              = trim($_POST['nid'] ?? '');
        $trade_area_type  = filter_var($_POST['trade_area_type'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $sector           = filter_var($_POST['sector'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $sub_sector       = filter_var($_POST['sub_sector'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $job_position     = trim($_POST['job_position'] ?? '');
        $start_year       = filter_var($_POST['start_year'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $has_support      = trim($_POST['has_support'] ?? '');
        $bugetamet        =AuthHelper::checkFiscalYear();
        $regby              = $_SESSION['user']['id'] ?? null;

        // ================= 2. BACKEND VALIDATION (ግንባር ቀደም ማረጋገጫዎች) =================
        

        if (!filter_var($age, FILTER_VALIDATE_INT, ["options" => ["min_range" => 15, "max_range" => 65]])) {
            $errors[] = "ዕድሜ ከ 15 እስከ 65 ዓመት መሆን አለበት።";
        }

        if (!in_array($trade_area_type, [1, 2])) {
            $errors[] = "እባክዎ ንግዱ የሚገኝበትን ትክክለኛ አካባቢ (ከተማ ወይም ገጠር) ይምረጡ።";
        }

        if (empty($sector) || empty($sub_sector)) {
            $errors[] = "የሥራ ዘርፍ እና ንዑስ ዘርፍ መመረጥ አለባቸው።";
        }

        if (empty($job_position)) {
            $errors[] = "የሥራ መስክ መሞላት አለበት።";
        }

        if (!filter_var($start_year, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1950, "max_range" => 2030]])) {
            $errors[] = "እባክዎ ትክክለኛ የግብር መክፈያ ዓመተ ምህረት ያስገቡ።";
        }

        if (!in_array($has_support, ['yes', 'no'])) {
            $errors[] = "እባክዎ ድጋፍ የተደረገ መሆኑን ወይም አለመሆኑን ይምረጡ።";
        }
        if(empty($nid)){
            $errors[] = "እባክዎ የብሔራዊ መታወቂያ ቁጥር ያስገቡ።";
        }


        // የድጋፍ መረጃዎች ማረጋገጫ
        $support_types_json = null;
        $financial_amount = 0.00;
        $loan_amount      = 0.00;
        $machinery_unit   = null;
        $land_unit        = null;
        $shed_unit        = null;
        $market_amount    = 0.00;
        $other_unit       = null;

        if ($has_support === 'yes') {
            if (isset($_POST['support_types']) && is_array($_POST['support_types'])) {
                $support_types_json = json_encode($_POST['support_types'], JSON_UNESCAPED_UNICODE);
            }

            $financial_amount = !empty($_POST['financial_amount']) ? filter_var($_POST['financial_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0.00;
            $loan_amount      = !empty($_POST['loan_amount']) ? filter_var($_POST['loan_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0.00;
            $machinery_unit   = !empty($_project = $_POST['machinery_unit']) ? trim($_POST['machinery_unit']) : null;
            $land_unit        = !empty($_POST['land_unit']) ? trim($_POST['land_unit']) : null;
            $shed_unit        = !empty($_POST['shed_unit']) ? trim($_POST['shed_unit']) : null;
            $market_amount    = !empty($_POST['market_amount']) ? filter_var($_POST['market_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0.00;
            $other_unit       = !empty($_POST['other_unit']) ? trim($_POST['other_unit']) : null;

        }

        // ስህተቶች ካሉ ወደ ፎርሙ በመመለስ ማሳየት
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header("Location: informal-trade-form.php");
            exit();
        }

        // 3. መረጃውን ወደ ሞዴል (Model) መላክ
        $data = [
            'id'               => $id,
            'bugetamet'        => $bugetamet,
            'regby'            => $regby,
            'nid'              => $nid,
            'trade_area_type'  => $trade_area_type,
            'sector'           => $sector,
            'sub_sector'       => $sub_sector,
            'job_position'     => $job_position,
            'start_year'       => $start_year,
            'has_support'      => $has_support,
            'financial_amount' => $financial_amount,
            'loan_amount'      => $loan_amount,
            'machinery_unit'   => $machinery_unit,
            'land_unit'        => $land_unit,
            'shed_unit'        => $shed_unit,
            'market_amount'    => $market_amount,
            'other_unit'       => $other_unit,
            'support_types_json' => $support_types_json
        ];
  $tradeModel = new InformalTradeModel($this->db);
        $result = $tradeModel->saveTradeData($data);

        if ($result) {
            $_SESSION['success'] = "መረጃው በተሳካ ሁኔታ ተመዝግቧል!";
        } else {
            $_SESSION['error'] = "መረጃውን በሚመዘግብበት ጊዜ ስህተት አጋጥሟል። መረጃውን ተደግሙዋል ።";
        }

    header("Location: informal-trade-list");
        exit();
    }
}