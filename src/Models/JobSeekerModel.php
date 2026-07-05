<?php
namespace App\Models;
use PDO;
class JobSeekerModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function checkDuplicateJobSeeker(array $data, ?string $excludeJobseekerId = null): array
{
    $excludeClause = $excludeJobseekerId
        ? " AND js.id <> :exclude_id "
        : "";

    $sql = "
        SELECT
            js.job_seeker_id,
            js.branch_id,

            b.name AS branch_name,
            b.level AS branch_level,
            b.path,

            (
                SELECT GROUP_CONCAT(bp.name ORDER BY LENGTH(bp.path) SEPARATOR ' → ')
                FROM branches bp
                WHERE b.path LIKE CONCAT(bp.path, '%')
            ) AS branch_hierarchy,

            /* Duplicate Indicators */

            (
                js.branch_id = :branch_id1
                AND js.kebele_id_no = :kebele_id_no1
            ) AS kebele_duplicate,

            (
                :g8id1 <> ''
                AND js.g8id = :g8id2
            ) AS g8id_duplicate,

            (
                :labor_id1 <> ''
                AND js.Labor_ID = :labor_id2
            ) AS labor_duplicate,

            (
                :fan1 <> ''
                AND js.FAN = :fan2
            ) AS fan_duplicate,

            (
                :full_name1 <> ''
                AND :phone1 <> ''
                AND :mother1 <> ''
                AND js.full_name_normalized = :full_name2
                AND js.phone_number = :phone2
                AND js.mothername = :mother2
            ) AS identity_duplicate

        FROM job_seekers js

        INNER JOIN branches b
            ON b.internal_id = js.branch_id

        WHERE

            WHERE (
    (js.branch_id = :branch_id2 AND js.kebele_id_no = :kebele_id_no2)
    OR (:g8id3 <> '' AND js.g8id = :g8id4)
    OR (:labor_id3 <> '' AND js.Labor_ID = :labor_id4)
    OR (:fan3 <> '' AND js.FAN = :fan4)
    OR (:full_name3 <> '' AND :phone3 <> '' AND :mother3 <> '' 
        AND js.full_name_normalized = :full_name4 
        AND js.phone_number = :phone4 
        AND js.mothername = :mother4)
)
{$excludeClause}

        LIMIT 1
    ";

    try {
        $stmt = $this->db->prepare($sql);

        /*
        |--------------------------------------------------------------------------
        | Normalize Input
        |--------------------------------------------------------------------------
        */

        $branchId  = (int) ($data['branch_id'] ?? 0);
        $kebeleId  = trim($data['kebele_id_no'] ?? '');

        $g8id      = trim($data['g8id'] ?? '');
        $laborId   = trim($data['Labor_ID'] ?? '');
        $fan       = trim($data['FAN'] ?? '');

        $fullName  = trim($data['full_name_normalized'] ?? '');
        $phone     = trim($data['phone_number'] ?? '');
        $mother    = trim($data['mothername'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | Branch + Kebele
        |--------------------------------------------------------------------------
        */

        $stmt->bindValue(':branch_id1', $branchId, PDO::PARAM_INT);
        $stmt->bindValue(':branch_id2', $branchId, PDO::PARAM_INT);

        $stmt->bindValue(':kebele_id_no1', $kebeleId);
        $stmt->bindValue(':kebele_id_no2', $kebeleId);

        /*
        |--------------------------------------------------------------------------
        | G8 ID
        |--------------------------------------------------------------------------
        */

        $stmt->bindValue(':g8id1', $g8id);
        $stmt->bindValue(':g8id2', $g8id);
        $stmt->bindValue(':g8id3', $g8id);
        $stmt->bindValue(':g8id4', $g8id);

        /*
        |--------------------------------------------------------------------------
        | Labor ID
        |--------------------------------------------------------------------------
        */

        $stmt->bindValue(':labor_id1', $laborId);
        $stmt->bindValue(':labor_id2', $laborId);
        $stmt->bindValue(':labor_id3', $laborId);
        $stmt->bindValue(':labor_id4', $laborId);

        /*
        |--------------------------------------------------------------------------
        | FAN
        |--------------------------------------------------------------------------
        */

        $stmt->bindValue(':fan1', $fan);
        $stmt->bindValue(':fan2', $fan);
        $stmt->bindValue(':fan3', $fan);
        $stmt->bindValue(':fan4', $fan);

        /*
        |--------------------------------------------------------------------------
        | Identity
        |--------------------------------------------------------------------------
        */

        $stmt->bindValue(':full_name1', $fullName);
        $stmt->bindValue(':full_name2', $fullName);
        $stmt->bindValue(':full_name3', $fullName);
        $stmt->bindValue(':full_name4', $fullName);

        $stmt->bindValue(':phone1', $phone);
        $stmt->bindValue(':phone2', $phone);
        $stmt->bindValue(':phone3', $phone);
        $stmt->bindValue(':phone4', $phone);

        $stmt->bindValue(':mother1', $mother);
        $stmt->bindValue(':mother2', $mother);
        $stmt->bindValue(':mother3', $mother);
        $stmt->bindValue(':mother4', $mother);

        /*
        |--------------------------------------------------------------------------
        | Exclude Current Job Seeker (Edit Mode)
        |--------------------------------------------------------------------------
        */

        if ($excludeJobseekerId !== null) {
            $stmt->bindValue(':exclude_id', $excludeJobseekerId);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    } catch (\PDOException $e) {

        error_log(__METHOD__ . ': ' . $e->getMessage());

        return [];
    }
}

public function logDuplicateAttempt(array $data): void
{
    try {
        $sql = "INSERT INTO jobseeker_duplicate_attempts (
                    id, attempted_by, branch_id, trigger_type, matched_jobseeker_id,
                    attempted_kebele_id_no, attempted_g8id, attempted_labor_id, attempted_fan,
                    attempted_full_name, attempted_phone_number, attempted_mothername,
                    ip_address
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['id'],
            $data['attempted_by'],
            $data['branch_id'],
            $data['trigger_type'],
            $data['matched_jobseeker_id'],
            $data['attempted_kebele_id_no'],
            $data['attempted_g8id'],
            $data['attempted_labor_id'],
            $data['attempted_fan'],
            $data['attempted_full_name'],
            $data['attempted_phone_number'],
            $data['attempted_mothername'],
            $data['ip_address'],
        ]);
    } catch (\PDOException $e) {
        // Never let logging failure block the actual registration flow
        error_log(__METHOD__ . ': ' . $e->getMessage());
    }
}
public function createJobseeker(array $data): bool {
    try {
        
        // Start transaction
        $this->db->beginTransaction();
        // Insert employee
        $sql = "INSERT INTO job_seekers (id, branch_id, first_name, father_name, last_name, gender, 
        age, education_level_category, educational_level, educated_dpt, education_trmnet_finsh_year, g8id, CGPA, school_type, 
        physical_condition, physical_condition_desc, mender, kebele_id_no, phone_number, 
        choice_sector1, sub_choose1, choice_sector2, sub_choose2, choice_sector3, sub_choose3, 
        meteleya_huneta, residence_status, srafelagi_huneta, graguation_catagory, maritalstatus, 
       haveexp, workplace, experience, profession, nameofcountry, language,
         wageorself, regstration_level, mothername, Labor_ID, 
         FAN, kebele_id_photo, jsphoto, fiscal_year, agri_business_experience, has_dependents, 
         number_of_dependents, children_under_five, full_name_normalized, registered_by) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $result1 = $stmt->execute([
            $data['uuid'],
            $data['branch_id'],
            $data['first_name'],
            $data['father_name'],
            $data['last_name'],
            $data['gender'],
            $data['age'],
            $data['education_level_catagory'],
            $data['educational_level'],
            $data['educated_dpt'],
            $data['education_trmnet_finsh_year'],
            $data['g8id'],
            $data['CGPA'],
            $data['school_type'],
            $data['physical_condition'],
            $data['physical_condition_desc'],
            $data['mender'],
            $data['kebele_id_no'],
            $data['phone_number'],
            $data['choice_sector1'],
            $data['sub_choose1'],
            $data['choice_sector2'],
            $data['sub_choose2'],
            $data['choice_sector3'],
            $data['sub_choose3'],
            $data['meteleya_huneta'],
            $data['residence_status'],
            $data['srafelagi_huneta'],
            $data['graguation_catagory'],
            $data['maritalstatus'],
            $data['haveexp'],
            $data['workplace'],
            $data['experience'],
            $data['profession'],
            $data['nameofcountry'],
            $data['language'],
            $data['wageorself'],
            $data['regstration_level'],
            $data['mothername'],
            $data['Labor_ID'],
            $data['FAN'],
            $data['kebele_id_photo'],
            $data['jsphoto'],
            $data['fiscal_year'],
            $data['agri_business_experience'],
            $data['has_dependents'],
            $data['number_of_dependents'],
            $data['children_under_five'],
            $data['normalizedFullName'],
            $data['reg_by']
        ]);

        if (!$result1) {
            throw new \Exception("Failed to insert employee");
        }

        // Commit transaction
        $this->db->commit();
        return true;

    } catch (\Exception $e) {
        // Rollback transaction on error
        $this->db->rollBack();
        error_log("Job seeker registration transaction failed: " . $e->getMessage());
        return false;
    }
}

public function getLast24HoursCount(string $myBranchId, string $userId, int $limit = 50, int $offset = 0): array {
   $sql = "SELECT
            js.id,
            js.first_name,
            js.father_name,
            js.last_name,
            js.gender,
            COUNT(*) OVER() AS total_job_seekers
        FROM job_seekers js
        WHERE js.branch_id = :branch_id
          AND js.registered_by = :user_id
        ORDER BY js.created_at DESC
        LIMIT :limit OFFSET :offset";

try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':branch_id', $myBranchId);
    $stmt->bindValue(':user_id', $userId);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    error_log("Get job seekers by hierarchy error: " . $e->getMessage());
    return [];
}
}
public function findById(string $myBranchId, string $jobseekerId): ?array
{
    $t0 = microtime(true);
    try {
        $sql = "
            SELECT
                js.*,

                s1.id        AS choice_sector1_uuid,
                s1.sector    AS choice_sector1_name,
                ss1.id       AS sub_choose1_uuid,
                ss1.subsector AS sub_choose1_name,

                s2.id        AS choice_sector2_uuid,
                s2.sector    AS choice_sector2_name,
                ss2.id       AS sub_choose2_uuid,
                ss2.subsector AS sub_choose2_name,

                s3.id        AS choice_sector3_uuid,
                s3.sector    AS choice_sector3_name,
                ss3.id       AS sub_choose3_uuid,
                ss3.subsector AS sub_choose3_name,

                CONCAT(u.first_name, ' ', u.father_name, ' ', u.grand_father_name) AS registered_by_name,

                b.name AS branch_name

            FROM job_seekers js

            INNER JOIN branches b ON js.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch

            LEFT JOIN sector_table s1 ON js.choice_sector1 = s1.sectorid
            LEFT JOIN sub_sector ss1 ON js.sub_choose1 = ss1.sub_sectorid
            LEFT JOIN sector_table s2 ON js.choice_sector2 = s2.sectorid
            LEFT JOIN sub_sector ss2 ON js.sub_choose2 = ss2.sub_sectorid
            LEFT JOIN sector_table s3 ON js.choice_sector3 = s3.sectorid
            LEFT JOIN sub_sector ss3 ON js.sub_choose3 = ss3.sub_sectorid
            LEFT JOIN users u ON js.registered_by = u.user_id

            WHERE js.id = :jobseeker_id
              AND b.path LIKE CONCAT(root.path, '%')

            LIMIT 1
        ";

        $t1 = microtime(true);
        $stmt = $this->db->prepare($sql);
        $t2 = microtime(true);

        $stmt->bindValue(':my_branch', $myBranchId);
        $stmt->bindValue(':jobseeker_id', $jobseekerId);
        $stmt->execute();
        $t3 = microtime(true);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $t4 = microtime(true);

        error_log(sprintf(
            "findById breakdown: buildSQL=%.2fms prepare=%.2fms execute=%.2fms fetch=%.2fms TOTAL=%.2fms",
            ($t1-$t0)*1000, ($t2-$t1)*1000, ($t3-$t2)*1000, ($t4-$t3)*1000, ($t4-$t0)*1000
        ));

        return $result ?: null;

    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return null;
    }
}

public function countJobSeekersByHierarchy(string $myBranchId): int
{
    $sql = "SELECT COUNT(*) AS total
            FROM job_seekers js
            INNER JOIN branches b ON js.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    } catch (\PDOException $e) {
        error_log("Count job seekers by hierarchy error: " . $e->getMessage());
        return 0;
    }
}
public function getJobSeekersByHierarchy(string $myBranchId, int $limit, int $offset): array
{
    $sql = "SELECT js.id, js.job_seeker_id, js.first_name, js.father_name, js.last_name, js.gender, 
                   b.name AS branch_name, b.level AS branch_level
            FROM job_seekers js
            INNER JOIN branches b ON js.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
            ORDER BY js.created_at DESC
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Get job seekers by hierarchy error: " . $e->getMessage());
        return [];
    }
}
public function updateJobseeker(array $data): bool
{
    try {
        $this->db->beginTransaction();

        $sql = "UPDATE job_seekers SET first_name = ?, father_name = ?, last_name = ?, gender = ?,
                    age = ?, education_level_category = ?, educational_level = ?, educated_dpt = ?,
                    education_trmnet_finsh_year = ?, g8id = ?, CGPA = ?, school_type = ?,
                    physical_condition = ?, physical_condition_desc = ?, mender = ?,
                    kebele_id_no = ?, phone_number = ?, choice_sector1 = ?, sub_choose1 = ?,
                    choice_sector2 = ?, sub_choose2 = ?, choice_sector3 = ?, sub_choose3 = ?,
                    meteleya_huneta = ?, residence_status = ?, srafelagi_huneta = ?,
                    graguation_catagory = ?, maritalstatus = ?, haveexp = ?, workplace = ?,
                    experience = ?, profession = ?, nameofcountry = ?, language = ?,
                    wageorself = ?, regstration_level = ?, mothername = ?, Labor_ID = ?,
                    FAN = ?, fiscal_year = ?, agri_business_experience = ?, has_dependents = ?,
                    number_of_dependents = ?, children_under_five = ?, full_name_normalized = ?
                WHERE branch_id = ? AND id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['first_name'], $data['father_name'], $data['last_name'],
            $data['gender'], $data['age'], $data['education_level_catagory'], $data['educational_level'],
            $data['educated_dpt'], $data['education_trmnet_finsh_year'], $data['g8id'], $data['CGPA'],
            $data['school_type'], $data['physical_condition'], $data['physical_condition_desc'],
            $data['mender'], $data['kebele_id_no'], $data['phone_number'], $data['choice_sector1'],
            $data['sub_choose1'], $data['choice_sector2'], $data['sub_choose2'], $data['choice_sector3'],
            $data['sub_choose3'], $data['meteleya_huneta'], $data['residence_status'],
            $data['srafelagi_huneta'], $data['graguation_catagory'], $data['maritalstatus'],
            $data['haveexp'], $data['workplace'], $data['experience'], $data['profession'],
            $data['nameofcountry'], $data['language'], $data['wageorself'], $data['regstration_level'],
            $data['mothername'], $data['Labor_ID'], $data['FAN'], $data['fiscal_year'],
            $data['agri_business_experience'], $data['has_dependents'], $data['number_of_dependents'],
            $data['children_under_five'], $data['normalizedFullName'],
            $data['branch_id'], $data['uuid']
        ]);

        if (!$result) {
            throw new \Exception("Failed to update jobseeker");
        }

        $this->db->commit();
        return true;

    } catch (\PDOException $e) {
        $this->db->rollBack();
        error_log("Job seeker update transaction failed: " . $e->getMessage());

        if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'uq_branch_kebele')) {
            throw new \RuntimeException('DUPLICATE_KEBELE');
        }
        return false;

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log("Job seeker update transaction failed: " . $e->getMessage());
        return false;
    }
}
    }

