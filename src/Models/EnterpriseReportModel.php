<?php 
namespace App\Models;
use App\Helpers\AmharicNormalizer;

use PDO;
class EnterpriseModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
public function getAllEnterprises() {
    $sql = "SELECT 
                c3.code003_id AS id,
                c3.enterprisename AS enterprise_name,
                COALESCE(grp.yetederajubet_akababi, ind.yeminorubet_acababi, '') AS zone,
                '' AS woreda,
                '' AS city,
                COALESCE(grp.kebele, '') AS kebele,
                '' AS house_no,
                COALESCE(grp.manager_phone, '') AS phone,
                YEAR(c3.established_date) AS established_year,
                st.sector_name AS business_field,
                sub.sub_sector_name AS sector,
                c3.enterprise_type AS enterprise_type,
                COALESCE(v.organization_category, 'ያልተለየ') AS organization_type,
                c3.tine_number AS tin_number,
                c3.yeedget_dereja AS growth_stage,
                c3.initial_capital AS initial_capital,
                c3.yehabtu_mnch AS capital_source,
                c3.wektawi_yehabt_meten AS current_capital,
                0 AS init_male_emp,
                0 AS init_female_emp,
                0 AS curr_male_members,
                0 AS curr_female_members,
                0 AS age_15_29,
                0 AS age_30_49,
                0 AS age_50_65,
                0 AS age_above_65,
                0 AS edu_basic,
                0 AS edu_primary,
                0 AS edu_secondary,
                0 AS edu_college_degree,
                0 AS perm_male_jobs,
                0 AS perm_female_jobs,
                0 AS temp_male_jobs,
                0 AS temp_female_jobs,
                c3.yemrt_ayinet AS product_type,
                c3.yemikerb_hager_weys_lewuch AS target_market
            FROM full_enterprise_and_job_seekerdata v
            GROUP BY c3.code003_id
            ORDER BY c3.code003_id DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
}