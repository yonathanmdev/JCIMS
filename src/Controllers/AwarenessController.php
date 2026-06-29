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
 public function showAwarenessList($branch_id) {
    // 1. ሚናን ማረጋገጥ
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    // 2. የደህንነት ቁጥጥር (የሌላ ቅርንጫፍ መረጃ እንዳያዩ መከልከል)
    if (isset($_SESSION['branch_id']) && $_SESSION['branch_id'] != $branch_id) {
        // ወደ ኋላ መመለስ ወይም ስህተት ማሳየት
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/dashboard");
        exit();
    }

    $awarenessModel = new AwarenessModel($this->db);
    // ዳታውን ከዳታቤዝ መሳብ
    $awarenessList = $awarenessModel->getallawarenessbybranch($_SESSION['branch_id']);
    
    // መረጃውን ይዞ ወደ ገጹ መሄድ
    $this->render('awareness-list', ['awarenessList' => $awarenessList]);
}
  public function getawarnessbyid($id){
    AuthHelper::checkRole(
        ['team_leader','officer']
    );
    $awarenessModel = new AwarenessModel($this->db);
    $result = $awarenessModel->getawarnessbyid($id);
    header('Content-Type: application/json');
    echo json_encode($result);
 }
}
