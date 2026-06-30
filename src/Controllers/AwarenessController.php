<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\AwarenessModel;

class AwarenessController extends BaseController {

    public function showRegisterForm() {
        // NOTE: confirm this should match the role in handleSectorRegistration() below.
        // Currently 'system_admin' here vs 'officer' there — pick one.
        AuthHelper::checkRole(['team_leader', 'officer']);
        $data = [
            'title' => 'JCIMS - የግንዛቤ ፈጠራ መመዝገቢያ',
        ];

        $this->render('awareness-registration', $data);
    }

 public function awarenessRegistration(){
    AuthHelper::checkRole(
    ['officer'],
    [3, 4]
);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
        exit();
    }
    $fullname = trim($_POST['fullname'] ?? '');
    $sex =  trim($_POST['sex'] ?? '');
    $yemenoriya_akababi = trim($_POST['yemenoriya_akababi'] ?? '');
    $awareness_type = trim($_POST['awareness_type'] ?? '');
    $reg_by = $_SESSION['user']['id'];
     
    if(empty($fullname) || empty($sex) || empty($yemenoriya_akababi) || empty($awareness_type)){
        $_SESSION['error'] = 'ሁሉንም መረጃ በትክክል ይሙሉ!።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
        exit();
    }
   if($sex !== 'ወንድ' && $sex !== 'ሴት'){
       $_SESSION['error'] = 'ጾታ በትክክል ይሙሉ!።';
       header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
       exit();
   }
    if(is_numeric($fullname) || is_numeric($yemenoriya_akababi) || is_numeric($awareness_type)){
      $_SESSION['error'] = 'ሁሉንም መረጃ በትክክል ይሙሉ!።';
       header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
       exit();
   }
   if($yemenoriya_akababi !== 'ከተማ' && $yemenoriya_akababi !== 'ገጠር'){
       $_SESSION['error'] = 'የመኖሪያ አካባቢ በትክክል ይሙሉ!።';
       header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
       exit();
   }
     if($awareness_type !== 'ለሌሎች ህብረተሰብ ክፍሎች' && $awareness_type !== 'ለስራ ፈላጊ ወላጆች'){
       $_SESSION['error'] = 'የግንዛቤ አይነት በትክክል ይሙሉ!።';
       header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
       exit();
   }
    $data = [
        'fullname' => $fullname,
        'sex' => $sex,
        'yemenoriya_akababi' => $yemenoriya_akababi,
        'awareness_type' => $awareness_type,
        'fiscal_year' => AuthHelper::checkFiscalYear(),
        'reg_by' => $reg_by,
        'branch_id' => $_SESSION['user']['branch_id']
    ];
    $awarenessModel = new AwarenessModel($this->db);
    $result = $awarenessModel->createAwareness($data);
    if($result){
   $_SESSION['success'] = 'የግንዛቤ ፈጠራው በትክክል ተመዝግቧል።';
        
    }else{
  $_SESSION['error'] = 'የግንዛቤ ፈጠራው ምዝገባ አልተሳካም።';
    }
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-registration");
        
 exit();
 }
 public function awarenessList(){
    AuthHelper::checkRole(
        ['team_leader','officer']
    );
    $data = [
        'title' => 'JCIMS - የግንዛቤ ፈጠራ ዝርዝር',
    ];
    $this->render('awareness-list', $data);
 }
  public function showAwarenessList() {
    AuthHelper::checkRole(['team_leader', 'officer']);
    $user = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;
    // $organizationId = $user['organization_id'] ?? null;

    if (!$branchId) {
        $_SESSION['error'] = 'የቅርንጫፍ መረጃ አልተገኘም።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/dashboard");
        exit();
    }

    $awarenessModel = new AwarenessModel($this->db);
  // 1. የገጽ ቁጥሩን ከ URL መያዝ
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 2. ገጹን አብሮ መላክ
$searchName = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$awarenessModel = new AwarenessModel($this->db);

if (!empty($searchName)) {
    // 🔍 ሳጥኑ ላይ ስም ከተጻፈ አዲሱን የፍለጋ ፈንክሽን ብቻ ይጠራል
    $paginationData = $awarenessModel->searchAwarenessByName($branchId, $searchName, $page);
} else {
    // 📋 ባዶ ከሆነ መደበኛውን የቅርንጫፍ ዝርዝር ማምጫ ፈንክሽን ይጠራል
    $paginationData = $awarenessModel->getallawarenessbybranch($branchId, $page);
}

 
// 3. ሙሉ መረጃውን ወደ ቪው መላክ
$data = [
    'title'         => 'JCIMS - የግንዛቤ ፈጠራ ዝርዝር',
    'awarenessList' => $paginationData['data'],
    'totalPages'    => $paginationData['total_pages'],
    'currentPage'   => $paginationData['current_page']
];

    $this->render('awareness-list', $data);
 } 
   
 public function updateAwareness() {
    AuthHelper::checkRole(['officer']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-list");
        exit();
    }

    $tbleid = trim($_POST['tbleid'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $sex = trim($_POST['sex'] ?? '');
    $yemenoriya_akababi = trim($_POST['yemenoriya_akababi'] ?? '');
    $awareness_type = trim($_POST['awareness_type'] ?? '');
    $reg_by = $_SESSION['user']['id'];
    if (empty($tbleid) || empty($fullname) || empty($sex) || empty($yemenoriya_akababi) || empty($awareness_type)) {
        $_SESSION['error'] = 'ሁሉንም መረጃ በትክክል ይሙሉ!።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-list");
        exit();
    }
 $data = [
        'fullname' => $fullname,
        'sex' => $sex,
        'yemenoriya_akababi' => $yemenoriya_akababi,
        'awareness_type' => $awareness_type,
        'fiscal_year' => AuthHelper::checkFiscalYear(),
        'reg_by' => $reg_by,
        'branch_id' => $_SESSION['user']['branch_id']
    ];
    $awarenessModel = new AwarenessModel($this->db);
    $awarenessModelResult = $awarenessModel->updateAwareness($tbleid, $data);
    if ($awarenessModelResult) {    
        
        $_SESSION['success'] = 'የግንዛቤ ፈጠራው በትክክል ተሻሽሏል።';
    } else {
        $_SESSION['error'] = 'የግንዛቤ ፈጠራው ማሻሻያ አልተሳካም።';
    }
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/awareness-list");
    exit();

}public function getNamesRecommendation() {
    // 1. የደህንነትና የባለስልጣን ፍቃድ ማረጋገጫ (role-based security)
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $user = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;
    
    // 2. ተጠቃሚው መፃፍ የጀመረውን ፊደል ከ URL መያዝ (?q=ተወ)
    $search = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    // ቅርንጫፍ ከሌለ ወይም ምንም ካልተጻፈ ባዶ መረጃ መመለስ
    if (!$branchId || empty($search)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([]);
        exit();
    }
    
    // 3. ሞዴሉን ጠርቶ ስሞቹን ማውጣት
    $awarenessModel = new AwarenessModel($this->db);
    $suggestions = $awarenessModel->getUniqueNamesByBranch($branchId, $search);
    
    // 4. ውጤቱን ወደ ጃቫስክሪፕቱ በ JSON ፎርማት መመለስ
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($suggestions);
    exit();
}










public function searchJobSeekersAjax() {
    // 🔒 የደህንነት ፍቃድ ማረጋገጫ
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $user = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;

    // ቅርንጫፍ ከሌለ ባዶ መረጃ መመለስ
    if (!$branchId) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([]);
        exit();
    }

    // ተጠቃሚው መፃፍ የጀመረውን ቃል መያዝ
    $search = isset($_GET['seeker_q']) ? trim($_GET['seeker_q']) : '';

    $awarenessModel = new AwarenessModel($this->db);
    $seekers = $awarenessModel->searchJobSeekersForAwareness($branchId, $search);
    
    // በስተጀርባ የሚላከው ላይ ሌላ ጽሑፍ እንዳይቀላቀል ማጽዳት (Clean Buffer)
    if (ob_get_length()) ob_clean(); 
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($seekers, JSON_UNESCAPED_UNICODE);
    exit();
}
}