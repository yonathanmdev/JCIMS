<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;

class InformalEnterpreseControrer extends BaseController {

    public function showinterpriseRegisterForm() {
        // NOTE: confirm this should match the role in handleSectorRegistration() below.
        // Currently 'system_admin' here vs 'officer' there — pick one.
         
$sectorModel = new SectorModel($this->db);
$sectors  = $sectorModel->getSectors();
        $data = [
            'title' => 'JCIMS - የዘርፍ መመዝገቢያ',
            'sectors' => $sectors,
        ];

        $this->render('informal-entrerprise-regstration', $data);
    }
}