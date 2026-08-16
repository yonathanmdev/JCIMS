<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Code003EnterpriseModel; // የሚጠቀሙበትን ሞዴል ማስገባት (Import ማድረግ)

class code003enterprisecontroller extends BaseController 
{
    protected $db;
    protected $reportModel;

    public function __construct($db)
    {
        // ዳታቤዙ ካልመጣ በራሱ እንዲገናኝ ማድረግ ደህንነቱን ይጠብቃል
        $this->db = $db;
        
        // የእርስዎን ፓተርን በመጠቀም ሞዴሉን ማስጀመር
        $this->reportModel = new Code003EnterpriseModel($this->db);
    }

public function code003Report()
    {
        AuthHelper::checkRole(['team_leader', 'officer']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? '';
        $myBranchName = $_SESSION['user']['branch_name'] ?? ($_SESSION['user']['name'] ?? '');

        // ፎርሙን ብቻ የያዘውን efficiency_statusy.php ቪው ገጽ ይከፍታል
        $this->render('Code003selectform', [
            'defaultBranchId'   => $myBranchId,
            'defaultBranchName' => $myBranchName
        ]);
    }


    public function displayCode003()
    {
        // ሚናውን ማረጋገጥ
        AuthHelper::checkRole(['team_leader', 'officer']);
        
        $myBranchId = $_SESSION['user']['branch_id'] ?? '';
        $myBranchName = $_SESSION['user']['branch_name'] ?? ($_SESSION['user']['name'] ?? '');
        
        // 1. ብራንች አይዲውን በመጠቀም መረጃዎችን ከሞዴሉ ማምጣት (Recursive query እንዲሰራ)
        $data['reports'] = $this->reportModel->getEnterpriseReports($myBranchId);

        // 2. የብራንች መረጃዎችን ወደ ቪው ማስተላለፍ (በጥያቄዎ መሰረት)
        $data['myBranchId'] = $myBranchId;
        $data['myBranchName'] = $myBranchName;

        // ቪውውን መጥራት እና መረጃውን መጫን
        return $this->renderPrintable('code003', $data);
    }
}