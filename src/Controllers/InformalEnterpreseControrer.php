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

        if (!$age || $age < 15 || $age > 100) {
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
}