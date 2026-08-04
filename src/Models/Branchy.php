<?php

class Branch {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * የቅርንጫፎቹን እና የስራቸው ያሉ የወረዳዎች/ንዑስ ቅርንጫፎች አፈጻጸም አጠቃልሎ የሚያሰላ Query
     */
    private function fetchBranchPerformanceQuery($whereCondition, $params) {
        $sql = "
            WITH RECURSIVE SubBranches AS (
                -- 1. ዋና ዋናዎቹን ዞኖች/ከተሞች መለየት
                SELECT 
                    b.id AS root_zone_id,
                    b.id AS current_branch_id,
                    b.internal_id AS current_internal_id
                FROM branches b
                WHERE {$whereCondition}
                  AND (b.is_deleted = 0 OR b.is_deleted IS NULL)

                UNION ALL

                -- 2. በየዞኑ ስር ያሉትን ወረዳዎች እና ንዑስ ቅርንጫፎች በሙሉ መፈለግ (Recursive)
                SELECT 
                    sb.root_zone_id,
                    child.id AS current_branch_id,
                    child.internal_id AS current_internal_id
                FROM branches child
                INNER JOIN SubBranches sb 
                    ON child.parent_id = sb.current_branch_id 
                    OR child.parent_id = CAST(sb.current_internal_id AS CHAR)
                WHERE (child.is_deleted = 0 OR child.is_deleted IS NULL)
            ),
            JobSeekerCounts AS (
                -- 3. በየወረዳው የተመዘገቡትን ስራ ፈላጊዎች ከዞናቸው ጋር ማያያዝ
                SELECT 
                    sb.root_zone_id,
                    
                    -- የተመዘገቡ (በጾታ)
                    COUNT(CASE WHEN j.gender LIKE '%ወንድ%' OR LOWER(TRIM(COALESCE(j.gender, ''))) IN ('m', 'male') THEN 1 END) AS p_m,
                    COUNT(CASE WHEN j.gender LIKE '%ሴት%' OR LOWER(TRIM(COALESCE(j.gender, ''))) IN ('f', 'female') THEN 1 END) AS p_f,
                    COUNT(j.id) AS p_sum,

                    -- ግንዛቤ የተፈጠረላቸው (በጾታ)
                    COUNT(CASE WHEN j.awareness IN (1, '1', 'Yes', 'yes', 'true') AND (j.gender LIKE '%ወንድ%' OR LOWER(TRIM(COALESCE(j.gender, ''))) IN ('m', 'male')) THEN 1 END) AS a_m,
                    COUNT(CASE WHEN j.awareness IN (1, '1', 'Yes', 'yes', 'true') AND (j.gender LIKE '%ሴት%' OR LOWER(TRIM(COALESCE(j.gender, ''))) IN ('f', 'female')) THEN 1 END) AS a_f,
                    COUNT(CASE WHEN j.awareness IN (1, '1', 'Yes', 'yes', 'true') THEN 1 END) AS a_sum

                FROM SubBranches sb
                LEFT JOIN job_seekers j 
                    ON (
                        CAST(j.branch_id AS UNSIGNED) = CAST(sb.current_internal_id AS UNSIGNED)
                        OR j.branch_id = sb.current_branch_id
                    )
                   AND (j.is_deleted = 0 OR j.is_deleted IS NULL)
                GROUP BY sb.root_zone_id
            ),
            Calculated AS (
                SELECT 
                    b.id AS id,
                    b.internal_id AS internal_id,
                    b.name AS name,
                    
                    -- 💡 እቅዶቹ በስታቲክ (Static 50,000) ተደርገዋል
                    50000 AS p_plan,
                    COALESCE(jsc.p_m, 0) AS p_m,
                    COALESCE(jsc.p_f, 0) AS p_f,
                    COALESCE(jsc.p_sum, 0) AS p_sum,

                    50000 AS a_plan,
                    COALESCE(jsc.a_m, 0) AS a_m,
                    COALESCE(jsc.a_f, 0) AS a_f,
                    COALESCE(jsc.a_sum, 0) AS a_sum

                FROM branches b
                LEFT JOIN JobSeekerCounts jsc ON b.id = jsc.root_zone_id
                WHERE {$whereCondition}
                  AND (b.is_deleted = 0 OR b.is_deleted IS NULL)
            )
            SELECT 
                id,
                internal_id,
                name,
                p_plan,
                p_m,
                p_f,
                p_sum,
                ROUND(IF(p_plan > 0, (p_sum / p_plan) * 100, 0), 2) AS p_per,
                DENSE_RANK() OVER (ORDER BY p_sum DESC) AS p_rank,

                a_plan,
                a_m,
                a_f,
                a_sum,
                ROUND(IF(a_plan > 0, (a_sum / a_plan) * 100, 0), 2) AS a_per,
                DENSE_RANK() OVER (ORDER BY a_sum DESC) AS a_rank
            FROM Calculated
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 1. የዞን / የክልል ንዑስ ቅርንጫፎችን አፈጻጸም ማምጫ
     */
    public function getImmediateSubBranches($parentBranchId) {
        $whereCondition = "(
            b.parent_id = :p_id1 
            OR b.parent_id IN (SELECT id FROM branches WHERE id = :p_id2 OR internal_id = :p_id3)
            OR b.parent_id IN (SELECT CAST(internal_id AS CHAR) FROM branches WHERE id = :p_id4 OR internal_id = :p_id5)
        )";

        $params = [
            ':p_id1' => (string)$parentBranchId,
            ':p_id2' => (string)$parentBranchId,
            ':p_id3' => (string)$parentBranchId,
            ':p_id4' => (string)$parentBranchId,
            ':p_id5' => (string)$parentBranchId
        ];

        return $this->fetchBranchPerformanceQuery($whereCondition, $params);
    }

    /**
     * 2. የከተማ አስተዳደር (One Stop Center) ቅርንጫፎች አፈጻጸም ማምጫ
     */
    public function getOneStopCenter($myBranchId) {
        $whereCondition = "(
            (b.parent_id = :m_id1 OR b.id = :m_id2 OR b.internal_id = :m_id3) 
            AND b.ketema_astedader = 1
        )";

        $params = [
            ':m_id1' => (string)$myBranchId,
            ':m_id2' => (string)$myBranchId,
            ':m_id3' => (string)$myBranchId
        ];

        return $this->fetchBranchPerformanceQuery($whereCondition, $params);
    }
}