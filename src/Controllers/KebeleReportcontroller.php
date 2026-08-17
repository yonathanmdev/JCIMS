<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\KebeleReportmodel;

class KebeleReportcontroller extends BaseController
{
    protected $db;
    protected $reportModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->reportModel = new KebeleReportmodel($this->db);
    }

    public function allKebeleReport()
    {
        AuthHelper::checkRole(['team_leader', 'officer']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? '';
        $myBranchName = $_SESSION['user']['branch_name'] ?? ($_SESSION['user']['name'] ?? '');

        // ከሞዴሉ መረጃዎችን ማምጣት
        $reports = $this->reportModel->getReportDataByBranch($myBranchId);

        // ጠቅላላ ድምር (Totals) ማስላት
        $totals = [
            'bookkeeping_seekers' => 0,
            'business_plan_seekers' => 0,
            'job_permanent_sum' => 0,
            'job_temporary_sum' => 0,
            'job_total_sum' => 0,
            'trained_agri_sum' => 0,
            'trained_ind_sum' => 0,
            'trained_serv_sum' => 0,
            'trained_total_sum' => 0,
            'kebele_count' => count($reports)
        ];

        foreach ($reports as $row) {
            $totals['bookkeeping_seekers'] += $row->bookkeeping_seekers;
            $totals['business_plan_seekers'] += $row->business_plan_seekers;
            $totals['job_permanent_sum'] += $row->job_created_permanent;
            $totals['job_temporary_sum'] += $row->job_created_temporary;
            $totals['job_total_sum'] += ($row->job_created_permanent + $row->job_created_temporary);
            
            $totals['trained_agri_sum'] += $row->trained_agriculture;
            $totals['trained_ind_sum'] += $row->trained_industry;
            $totals['trained_serv_sum'] += $row->trained_service;
            $totals['trained_total_sum'] += ($row->trained_agriculture + $row->trained_industry + $row->trained_service);
        }

        // መረጃውን ወደ ቪው መላክ
        return $this->render('all-kebele-report', [
            'defaultBranchId'   => $myBranchId,
            'defaultBranchName' => $myBranchName,
            'reports'           => $reports,
            'totals'            => $totals
        ]);
    }
}