<?php
namespace App\Models;

class Code003EnterpriseModel 
{
    private $db;

    //  የዳታቤዝ 
    protected $table = 'full_enterprise_and_job_seekerdata';

    public function __construct($db) 
    {
        $this->db = $db;
    }

    // Recursive ሎጂክን እና DISTINCT tine_number በመጠቀም መረጃዎችን ከቴብሉ ለማምጣት  ፈንክሽን
   public function getEnterpriseReports($branchId = null, $limit = 25, $offset = 0)
    {
        if (!empty($branchId)) {
            $sql = "WITH RECURSIVE SubBranches AS (
                        SELECT b.internal_id
                        FROM branches b
                        INNER JOIN branches root ON root.internal_id = :my_branch
                        WHERE b.path LIKE CONCAT(root.path, '%')
                    )
                    SELECT 
                        e.tine_number,
                        MAX(e.enterprisename) AS enterprisename,
                        MAX(e.jskebele) AS jskebele,
                        MAX(e.manager_phone) AS manager_phone,
                        MAX(e.established_date) AS established_date,
                        MAX(e.sub_sector_name) AS sub_sector_name,
                        MAX(e.sector_name) AS sector_name,
                        MAX(e.yeedget_dereja) AS yeedget_dereja,
                        MAX(e.project_type_or_aderejajet) AS project_type_or_aderejajet,
                        MAX(e.yeedget_dereja) AS yeedget_dereja,
                        MAX(e.initial_capital) AS initial_capital,
                        MAX(e.yehabtu_mnch) AS yehabtu_mnch,
                        MAX(e.wektawi_yehabt_meten) AS wektawi_yehabt_meten,
                        MAX(e.yemrt_ayinet) AS yemrt_ayinet,
                        MAX(e.yemikerb_hager_weys_lewuch) AS yemikerb_hager_weys_lewuch,
                        -- ቲን ነምበሩ አንድ ሆኖ የወንድ፣ ሴት እና የድምር አባላት የሚደመሩበት
                        SUM(CASE WHEN e.gender = 'ወንድ' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS initial_male,
                        SUM(CASE WHEN e.gender = 'ሴት' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1  THEN 1 ELSE 0 END) AS initial_female,
                        COUNT(*) AS initial_total,
                        -- በየዕድሜ ክልሉ የሚመደቡ አባላትን ብዛት ለመቁጠር
                        SUM(CASE WHEN e.jsage BETWEEN 15 AND 29 and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS age_15_29,
                        SUM(CASE WHEN e.jsage BETWEEN 30 AND 49 and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS age_30_49,
                        SUM(CASE WHEN e.jsage BETWEEN 50 AND 65 and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS age_50_65,
                        SUM(CASE WHEN e.jsage > 65 and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' THEN 1 ELSE 0 END) AS age_above_65,
                        COUNT(*) AS total_members,
                        -- በትምህርት ደረጃ የተከፋፈሉ አባላትን ለመቁጠር
                        SUM(CASE WHEN (e.jseducational_level = 'ማንበብና መፃፍ የማይችሉ' OR e.jseducational_level = 'መሰረተ ትምህርት') and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS edu_basic,
                        SUM(CASE WHEN (e.jseducational_level = 'ከ1-7ኛ' OR e.jseducational_level = '8ኛ ያጠናቀቁ') and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS edu_1_8,
                        SUM(CASE WHEN (e.jseducational_level = 'ከ9-10ኛ' OR e.jseducational_level = 'ከ11-12ኛ') and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS edu_9_12,
                        SUM(CASE WHEN (e.jseducational_level IN ('ደረጃ 1', 'ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ')) and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource=1 THEN 1 ELSE 0 END) AS edu_degree,
                        COUNT(*) AS total_edu_members,
                        -- ለቋሚ የሥራ ዕድል
                        SUM(CASE WHEN e.employment_type = '1' AND e.gender = 'ወንድ' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource!=1 THEN 1 ELSE 0 END) AS permanent_male,
                        SUM(CASE WHEN e.employment_type = '1' AND e.gender = 'ሴት' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource!=1 THEN 1 ELSE 0 END) AS permanent_female,
                        SUM(CASE WHEN e.employment_type = '1' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource!=1 THEN 1 ELSE 0 END) AS permanent_total,

                        -- ለጊዜያዊ የሥራ ዕድል
                        SUM(CASE WHEN e.employment_type = '2' AND e.gender = 'ወንድ' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource!=1 THEN 1 ELSE 0 END) AS temporary_male,
                        SUM(CASE WHEN e.employment_type = '2' AND e.gender = 'ሴት' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource!=1 THEN 1 ELSE 0 END) AS temporary_female,
                        SUM(CASE WHEN e.employment_type = '2' and e.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' and e.jcsource!=1 THEN 1 ELSE 0 END) AS temporary_total
                    FROM " . $this->table . " e
                    INNER JOIN SubBranches sb ON e.code003_branch_id = sb.internal_id
                    GROUP BY e.tine_number
                    LIMIT :lim OFFSET :offs";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':my_branch', $branchId, \PDO::PARAM_INT);
            $stmt->bindValue(':lim', (int) $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offs', (int) $offset, \PDO::PARAM_INT);
            $stmt->execute();
        } 

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}