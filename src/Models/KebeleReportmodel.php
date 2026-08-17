<?php
namespace App\Models;

use PDO;

class KebeleReportmodel
{
    private $db;

    public function __construct($db) 
    {
        $this->db = $db;
    }

    /**
     * በወረዳው (branch_id) ስር ያሉ ቀበሌዎችን እና የእያንዳንዱን ቀበሌ ሪፖርት መረጃዎች ያመጣል
     */
public function getReportDataByBranch($branchId)
    {
        if (empty($branchId)) {
            //echo print_r($branchId);
            return [];
        }

        // የብራንች ተዋረድ 
        $sql = "WITH RECURSIVE SubBranches AS (
                    SELECT b.internal_id
                    FROM branches b
                    INNER JOIN branches root ON root.internal_id = :branch_id
                    WHERE b.path LIKE CONCAT(root.path, '%')
                )
                SELECT ak.kebele AS kebele_name, ak.branch_id
                FROM allKebeles ak
                INNER JOIN SubBranches sb ON ak.branch_id = sb.internal_id
                GROUP BY ak.kebele, ak.branch_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['branch_id' => $branchId]);
        $kebeles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- 1ኛው ዲበግ: የተገኙ ቀበሌዎችን ማየት ---
        // echo "<pre>Kebeles found: "; print_r($kebeles); echo "</pre>";

        $reportResults = [];

        foreach ($kebeles as $k) {
            $kebeleName = $k['kebele_name'];
            $kebeleBranchId = $k['branch_id'];

            // ሀ. የስራ ፈላጊዎች መረጃ
            $sqlSeekers = "SELECT 
                            COUNT(job_seeker_id) AS bookkeeping_seekers,
                            SUM(CASE WHEN awareness = 1 THEN 1 ELSE 0 END) AS business_plan_seekers,
                            SUM(CASE WHEN employment_status = 1 THEN 1 ELSE 0 END) AS job_created_permanent,
                            SUM(CASE WHEN employment_status = 2 THEN 1 ELSE 0 END) AS job_created_temporary
                           FROM job_seekers 
                           WHERE kebele = :kebele_name AND branch_id = :branch_id";
            
            $stmtSeekers = $this->db->prepare($sqlSeekers);
            $stmtSeekers->execute([
                'kebele_name' => $kebeleName,
                'branch_id' => $kebeleBranchId
            ]);
            $seekerData = $stmtSeekers->fetch(PDO::FETCH_ASSOC);

            // ለ. የኢንተርፕራይዞች መረጃ
            $sqlEnterprise = "SELECT 
                                COUNT(DISTINCT CASE WHEN sector_name LIKE '%ግብርና%' THEN tine_number END) AS trained_agriculture,
                                COUNT(DISTINCT CASE WHEN sector_name LIKE '%ኢንዱስትሪ%' THEN tine_number END) AS trained_industry,
                                COUNT(DISTINCT CASE WHEN sector_name LIKE '%አገልግሎት%' THEN tine_number END) AS trained_service
                              FROM full_enterprise_and_job_seekerdata 
                              WHERE jskebele = :kebele_name AND CAST(code003_branch_id AS CHAR) = :branch_id";

            $stmtEnt = $this->db->prepare($sqlEnterprise);
            $stmtEnt->execute([
                'kebele_name' => $kebeleName,
                'branch_id' => $kebeleBranchId
            ]);
            $entData = $stmtEnt->fetch(PDO::FETCH_ASSOC);

            $reportResults[] = (object)[
                'kebele_name' => $kebeleName,
                'bookkeeping_seekers' => $seekerData['bookkeeping_seekers'] ?? 0,
                'business_plan_seekers' => $seekerData['business_plan_seekers'] ?? 0,
                'job_created_permanent' => $seekerData['job_created_permanent'] ?? 0,
                'job_created_temporary' => $seekerData['job_created_temporary'] ?? 0,
                'trained_agriculture' => $entData['trained_agriculture'] ?? 0,
                'trained_industry' => $entData['trained_industry'] ?? 0,
                'trained_service' => $entData['trained_service'] ?? 0,
            ];
        }

        // --- 2ኛው ዲበግ: የተሰበሰበውን ሙሉ ሪፖርት ማየት ከፈለጉ ከታች ያለውን uncomment ያድርጉት ---
        // echo "<pre>Final Report Data: "; print_r($reportResults); echo "</pre>"; exit;

        return $reportResults;
    }
}