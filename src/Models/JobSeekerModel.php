<?php
namespace App\Models;
use PDO;
class JobSeekerModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

public function checkDuplicateJobSeeker(array $data): array
{
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

            (js.branch_id = :branch_id1
                AND js.kebele_id_no = :kebele_id_no1) AS kebele_duplicate,

            (
                js.g8id IS NOT NULL
                AND js.g8id <> ''
                AND :g8id1 <> ''
                AND js.g8id = :g8id2
            ) AS g8id_duplicate,

            (
                js.Labor_ID IS NOT NULL
                AND js.Labor_ID <> ''
                AND :labor_id1 <> ''
                AND js.Labor_ID = :labor_id2
            ) AS labor_duplicate,

            (
                js.FAN IS NOT NULL
                AND js.FAN <> ''
                AND :fan1 <> ''
                AND js.FAN = :fan2
            ) AS fan_duplicate,

            (
                js.full_name_normalized IS NOT NULL
                AND js.full_name_normalized <> ''
                AND js.phone_number IS NOT NULL
                AND js.phone_number <> ''
                AND js.mothername IS NOT NULL
                AND js.mothername <> ''
                AND :full_name_normalized1 <> ''
                AND :phone_number1 <> ''
                AND :mothername1 <> ''
                AND js.full_name_normalized = :full_name_normalized2
                AND js.phone_number = :phone_number2
                AND js.mothername = :mothername2
            ) AS identity_duplicate

        FROM job_seekers js

        INNER JOIN branches b
            ON b.internal_id = js.branch_id

        WHERE

            (
                js.branch_id = :branch_id2
                AND js.kebele_id_no = :kebele_id_no2
            )

            OR

            (
                js.g8id IS NOT NULL
                AND js.g8id <> ''
                AND :g8id3 <> ''
                AND js.g8id = :g8id4
            )

            OR

            (
                js.Labor_ID IS NOT NULL
                AND js.Labor_ID <> ''
                AND :labor_id3 <> ''
                AND js.Labor_ID = :labor_id4
            )

            OR

            (
                js.FAN IS NOT NULL
                AND js.FAN <> ''
                AND :fan3 <> ''
                AND js.FAN = :fan4
            )

            OR

            (
                js.full_name_normalized IS NOT NULL
                AND js.full_name_normalized <> ''
                AND js.phone_number IS NOT NULL
                AND js.phone_number <> ''
                AND js.mothername IS NOT NULL
                AND js.mothername <> ''
                AND :full_name_normalized3 <> ''
                AND :phone_number3 <> ''
                AND :mothername3 <> ''
                AND js.full_name_normalized = :full_name_normalized4
                AND js.phone_number = :phone_number4
                AND js.mothername = :mothername4
            )

        LIMIT 1
    ";

    try {

        $stmt = $this->db->prepare($sql);

        // branch_id / kebele_id_no — used in kebele_duplicate (SELECT) + WHERE
        $stmt->bindValue(':branch_id1', $data['branch_id'], PDO::PARAM_INT);
        $stmt->bindValue(':branch_id2', $data['branch_id'], PDO::PARAM_INT);
        $stmt->bindValue(':kebele_id_no1', $data['kebele_id_no']);
        $stmt->bindValue(':kebele_id_no2', $data['kebele_id_no']);

        // g8id — used twice in SELECT + twice in WHERE
        $g8id = $data['g8id'] ?? '';
        $stmt->bindValue(':g8id1', $g8id);
        $stmt->bindValue(':g8id2', $g8id);
        $stmt->bindValue(':g8id3', $g8id);
        $stmt->bindValue(':g8id4', $g8id);

        // Labor_ID — used twice in SELECT + twice in WHERE
        $laborId = $data['Labor_ID'] ?? '';
        $stmt->bindValue(':labor_id1', $laborId);
        $stmt->bindValue(':labor_id2', $laborId);
        $stmt->bindValue(':labor_id3', $laborId);
        $stmt->bindValue(':labor_id4', $laborId);

        // FAN — used twice in SELECT + twice in WHERE
        $fan = $data['FAN'] ?? '';
        $stmt->bindValue(':fan1', $fan);
        $stmt->bindValue(':fan2', $fan);
        $stmt->bindValue(':fan3', $fan);
        $stmt->bindValue(':fan4', $fan);

        // full_name_normalized / phone_number / mothername
        // — each used twice in SELECT + twice in WHERE
        $fullName = $data['full_name_normalized'];
        $stmt->bindValue(':full_name_normalized1', $fullName);
        $stmt->bindValue(':full_name_normalized2', $fullName);
        $stmt->bindValue(':full_name_normalized3', $fullName);
        $stmt->bindValue(':full_name_normalized4', $fullName);

        $phone = $data['phone_number'];
        $stmt->bindValue(':phone_number1', $phone);
        $stmt->bindValue(':phone_number2', $phone);
        $stmt->bindValue(':phone_number3', $phone);
        $stmt->bindValue(':phone_number4', $phone);

        $mother = $data['mothername'];
        $stmt->bindValue(':mothername1', $mother);
        $stmt->bindValue(':mothername2', $mother);
        $stmt->bindValue(':mothername3', $mother);
        $stmt->bindValue(':mothername4', $mother);

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

public function getJobSeekersByHierarchy(string $myBranchId, int $limit = 50, int $offset = 0): array
{
    $sql = "SELECT js.id, js.first_name, js.father_name, js.last_name, js.gender, 
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
    }

