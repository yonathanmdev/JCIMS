<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\JobSeekerModel;
use App\Models\JobseekerTransferModel;
use Ramsey\Uuid\Uuid;
use Exception;
class JobseekerTransferController extends BaseController {     
    public function listofJobseekersfortransfer() {
         AuthHelper::checkRole(['team_leader', 'officer']);
 $myBranchId  = $_SESSION['user']['branch_id'];
 $jobSeekerModel = new JobSeekerModel($this->db);
 $limit = 50;
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($currentPage - 1) * $limit;

    $jobSeekers = $jobSeekerModel->getJobSeekersByHierarchy($myBranchId, $limit, $offset);
    $totalCount = $jobSeekerModel->countJobSeekersByHierarchy($myBranchId);
    $totalPages = (int)ceil($totalCount / $limit);

    $data = [
        'title'       => 'JCIMS - የሰራተኛ መመዝገቢያ',
        'jobSeekers'  => $jobSeekers,
        'offset'      => $offset,        // ADD THIS
        'currentPage' => $currentPage,   // ADD THIS
        'totalPages'  => $totalPages,    // ADD THIS
        'totalCount' => $totalCount
    ];

         $this->render('jobseeker-transfer', $data);
    }
    
    public function getBranchesByHierarchyAjax() {
        header('Content-Type: application/json; charset=utf-8');
        
        // የSession መኖርን ማረጋገጥ
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['success' => false, 'data' => [], 'message' => 'ያልተፈቀደ መዳረሻ!']);
            exit;
        }

        $level = isset($_GET['level']) ? (int)$_GET['level'] : 0;
        // parent_id በሕጋዊ መንገድ መቀበል (string ወይም int ሊሆን ይችላል)
        $parentId = isset($_GET['parent_id']) && $_GET['parent_id'] !== '' ? trim($_GET['parent_id']) : null;

        // የግብዓት ማረጋገጫ ጥንቃቄ
        if ($level <= 0 || ($level > 1 && $parentId === null)) {
            echo json_encode(['success' => false, 'data' => [], 'message' => 'የተሳሳተ የተዋረድ ጥያቄ።']);
            exit;
        }
         $jobSeekerModel = new JobseekerTransferModel($this->db);
        $branches = $jobSeekerModel->getBranchesByLevel($level, $parentId);
        
        echo json_encode(['success' => true, 'data' => $branches]);
        exit;
    }
    private function generateUuidV7() {
        // የአሁኑን ጊዜ በሚሊሰከንድ ማግኘት
        $time = (int) floor(microtime(true) * 1000);
        
        // 10 የዘፈቀደ ባይቶች ማመንጨት
        $randomBytes = random_bytes(10);
        
        $timestampHex = str_pad(dechex($time), 12, '0', STR_PAD_LEFT);
        
        $ver = (7 << 12) | (ord($randomBytes[0]) & 0x0fff);
        $verHex = str_pad(dechex($ver), 4, '0', STR_PAD_LEFT);
        
        $var = (0x8000) | ((ord($randomBytes[1]) << 8) | ord($randomBytes[2])) & 0x3fff;
        $varHex = str_pad(dechex($var), 4, '0', STR_PAD_LEFT);
        
        $restHex = bin2hex(substr($randomBytes, 3));
        
        return sprintf('%s-%s-%s-%s-%s',
            substr($timestampHex, 0, 8),
            substr($timestampHex, 8, 4),
            $verHex,
            $varHex,
            $restHex
        );
    }

    /**
     * የሥራ ፈላጊ ዝውውርን የሚያስተናግድ ዋናው ፈንክሽን
     */
    public function processJobSeekerTransfer() {
    // ምላሹ JSON መሆኑን ማሳወቂያ
    header('Content-Type: application/json; charset=utf-8');

    // 💡 1. ላኪው ቅርንጫፍ (Sender Branch) ከሴሽን መኖሩን ማረጋገጥ
    $sender_branch_id = isset($_SESSION['user']['branch_id']) ? (int)$_SESSION['user']['branch_id'] : 0;
    $user_id          = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
    
    if ($sender_branch_id <= 0 || $user_id <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'የሎግድ-ኢን ተጠቃሚው መረጃ (Branch ID/User ID) በሴሽን ውስጥ አልተገኘም። እባክዎ እንደገና ይግቡ።'
        ]);
        exit;
    }
 
    // 💡 2. ከቪው (POST) የመጡትን መረጃዎች በደህንነት መቀበል
    $job_seeker_id = filter_input(INPUT_POST, 'job_seeker_id', FILTER_VALIDATE_INT);
    $transfer_type = filter_input(INPUT_POST, 'transfer_level_type', FILTER_DEFAULT); // 'center' ወይም 'woreda'

    // የቦታ ተዋረዶችን መቀበልና ወደ Integer መቀየር
    $region_id = isset($_POST['region_id']) ? (int)$_POST['region_id'] : null;
    $zone_id   = isset($_POST['zone_id']) ? (int)$_POST['zone_id'] : null;
    $woreda_id = isset($_POST['woreda_id']) ? (int)$_POST['woreda_id'] : null;

    // 💡 3. የመዳረሻ ቅርንጫፍ (Final Receiver Branch ID) ሎጂክ መወሰኛ
    $final_receiver_branch_id = 0;

    if ($transfer_type === 'center') {
        $destination_branch_id = isset($_POST['destination_branch_id']) ? (int)$_POST['destination_branch_id'] : 0;
        if ($destination_branch_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'እባክዎ ዝውውሩ የሚፈጸምበትን ማዕከል ይምረጡ።']);
            exit;
        }
        $final_receiver_branch_id = $destination_branch_id;
    } elseif ($transfer_type === 'woreda') {
        if (!$woreda_id || $woreda_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'እባክዎ ዝውውሩ የሚፈጸምበትን ወረዳ ይምረጡ።']);
            exit;
        }
        $final_receiver_branch_id = $woreda_id;
    } else {
        echo json_encode(['success' => false, 'message' => 'የማይታወቅ የዝውውር ዓይነት ተመርጧል።']);
        exit;
    }

    // 💡 4. የዳታ ሙሉነት ማረጋገጫ (Validation)
    if (!$job_seeker_id || !$region_id || !$zone_id || $final_receiver_branch_id <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'ያልተሟላ መረጃ ተልኳል። እባክዎ ሁሉንም ሳጥኖች በትክክል መሙላትዎን ያረጋግጡ!'
        ]);
        exit;
    }

    // ⚠️ የነበረው የሙከራ 'echo ... exit;' መስመር ኮዱ ወደ ታች እንዳይወርድ ስለሚያደርገው እዚህ ጋር እንዲወገድ ተደርጓል

    // 💡 5. የዳታቤዝ ስራዎችን በደህንነት ማከናወን
 // 💡 5. የዳታቤዝ ስራዎችን በደህንነት ማከናወን
    try {
        // የ UUID V7 መለያ ማመንጫ
        
 $jobseekerUuidtra = Uuid::uuid7()->toString();
      $uuidV7= $jobseekerUuidtra;
       // 🔐 ከሞዴሉ (insertTransferLog) የፕሌስሆልደር ስሞች ጋር ፍጹም የተጣጣመ አሬይ
        $logData = [
            'id'               => $uuidV7,
            'job_seeker_id'    => $job_seeker_id,
            'sender_branch_id' => $sender_branch_id,
            'recver_branch_id' => $final_receiver_branch_id,
            'transfer_status'  => '0',     // ለቪው ሰንጠረዥ እንዲመች
            'transferby'       => $user_id,        // 💡 ወደ (int)$data['transferby'] የሚቀየረው ቁልፍ
            'acceptorrejectby' => null           // የውጭ ቁልፍ (Foreign Key) ስህተትን ለመከላከል NULL
        ];

        // የሞዴል ኢንስታንስ መፍጠር
        $model = new JobseekerTransferModel($this->db);
        

        // 💡 ሥራ ፈላጊው በተመሳሳይ መዳረሻ ላይ 'pending (0)' ዝውውር ካለው አስቀድሞ መፈተሽ
        $isDuplicate = $model->checkDuplicateTransfer($job_seeker_id, $final_receiver_branch_id);
        
        if ($isDuplicate) {
            echo json_encode([
                'success' => false, 
                'message' => 'ይህ ሥራ ፈላጊ ቀደም ሲል ወደዚህ መዳረሻ የተላከ እና ውሳኔ ያልተሰጠው (Pending) ዝውውር ስላለው በድጋሚ መላክ አይቻልም!'
            ]);
            exit;
        }
        // መረጃውን ማስገባት
        $inserted = $model->insertTransferLog($logData);

        if ($inserted) {
            echo json_encode([
                'success' => true, 
                'message' => 'የሥራ ፈላጊ ዝውውሩ በተሳካ ሁኔታ ተመዝግቧል።'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'ዝውውሩን መመዝገብ አልተቻለም። እባክዎ የዳታቤዝ ግንኙነቱን ያረጋግጡ።'
            ]);
        }

    } catch (Exception $e) {
        // 💡 የጃቫስክሪፕት ስህተትን ለማስቀረት እዚህ ጋርም ቢሆን የ JSON ምላሽ መላክ ግድ ነው
        echo json_encode([
            'success' => false, 
            'message' => 'የሲስተም ስህተት አጋጥሟል፦ ' . $e->getMessage()
        ]);
    }
    
}
public function showTransferTracking() {
        $branch_id = isset($_SESSION['user']['branch_id']) ? (int)$_SESSION['user']['branch_id'] : 0;
        
        $model = new JobseekerTransferModel($this->db);
        $transfers = $model->getTransferTrackingList($branch_id);
      
        $data = [
            'transfers' => $transfers,
            'title' => 'JCIMS - የሥራ ፈላጊ ዝውውር መከታተያ',
        ];
        $this->render('jobseeker-transfers-list', $data);
    }
   /**
     * ከፖፕ-አፕ የሚመጣውን የዝውውር ውሳኔ ማስተናገጃ ዋና ፈንክሽን (AJAX)
     */
    public function submitTransferDecision() {
        // የምላሽ አይነት JSON መሆኑን ማሳወቅ
        header('Content-Type: application/json; charset=utf-8');

        // 1. የሴሽን እና የባለስልጣን ደህንነት ማረጋገጫ
        $currentBranchId = isset($_SESSION['user']['branch_id']) ? (int)$_SESSION['user']['branch_id'] : 0;
        $userId          = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;

        if ($currentBranchId <= 0 || $userId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ያልተፈቀደ መዳረሻ! እባክዎ እንደገና ይግቡ።'
            ]);
            exit;
        }

        // 2. ከፎርሙ የመጡ መረጃዎችን መቀበልና ማጣራት
        $transferLogId = filter_input(INPUT_POST, 'transfer_log_id', FILTER_DEFAULT);
        $actionStatus  = filter_input(INPUT_POST, 'action_status', FILTER_VALIDATE_INT); // 1 = Approve, 2 = Reject

        if (empty($transferLogId) || !$actionStatus || !in_array($actionStatus, [1, 2])) {
            echo json_encode([
                'success' => false,
                'message' => 'የተሳሳተ ወይም ያልተሟላ የውሳኔ መረጃ ተልኳል።'
            ]);
            exit;
        }

        try {
            // 3. የሞዴል ኢንስታንስ መፍጠር እና ሂደቱን ማስፈጸም
            $model = new JobseekerTransferModel($this->db);
            $result = $model->updateTransferDecision($transferLogId, $actionStatus, $userId, $currentBranchId);

            // ውጤቱን ለጃቫስክሪፕቱ መመለስ
            echo json_encode($result);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'የሲስተም ስህተት አጋጥሟል፦ ' . $e->getMessage()
            ]);
            exit;
        }
    }
    }