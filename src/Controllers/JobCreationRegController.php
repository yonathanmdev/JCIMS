<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;

class JobCreationRegController extends BaseController {

    public function showRegisterForm() {
        AuthHelper::checkRole(['team_leader', 'officer']);
        

 
        $data = [
            'title' => 'JCIMS - ስራ እድል መመዝገቢያ',
            
        ];

        $this->render('job-creation-reg', $data);
    }
}
    ?>