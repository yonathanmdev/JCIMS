<?php
namespace App\Models;
use App\Helpers\AmharicNormalizer;
use App\Models\EmployeeGuarantor;
use PDO;
class EmployeeRegistration {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
public function assignJob(string $newJobId, string $branchId, ?string $oldJobId = null): void
{
    // 🔒 lock new job
    $stmt = $this->db->prepare("
        SELECT id, allow_multiple, vacancy_count, current_filled
        FROM job_property
        WHERE id = ?
        AND branch_id = ?
        AND is_deleted = 0
        AND status = 'active'
        FOR UPDATE
    ");
    $stmt->execute([$newJobId, $branchId]);
    $newJob = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$newJob) {
        throw new \Exception("Invalid job");
    }

    // 🔁 if changing job → release old job
    if ($oldJobId && $oldJobId !== $newJobId) {

        $stmt = $this->db->prepare("
            UPDATE job_property
            SET current_filled = current_filled - 1
            WHERE id = ?
        ");
        $stmt->execute([$oldJobId]);
    }

    // 🚨 enforce rules BEFORE increment
    if ((int)$newJob['allow_multiple'] === 0) {
        if ($newJob['current_filled'] > 0) {
            throw new \Exception("Job already taken");
        }
    }

    if ((int)$newJob['allow_multiple'] === 1 && $newJob['vacancy_count'] !== null) {
        if ($newJob['current_filled'] >= (int)$newJob['vacancy_count']) {
            throw new \Exception("Job is full");
        }
    }

    // ➕ increment new job
    $stmt = $this->db->prepare("
        UPDATE job_property
        SET current_filled = current_filled + 1
        WHERE id = ?
    ");
    $stmt->execute([$newJobId]);
}
 public function createEmployee(array $data, ?array $guarantorData = null): bool {
        try {
            $fullNameRaw = $data['first_name'] . ' ' . $data['father_name'] . ' ' . $data['last_name'];
            $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);
            // Start transaction
            $this->db->beginTransaction();

$this->assignJob($data['job_property_id'], $data['branch_id']);            // Insert employee
            $sql = "INSERT INTO employees_table (
                uuid, employee_id, first_name, father_name, last_name, mother_name,
                sex, birth_date, phone_number, yegabcha_huneta, organization_id,
                branch_id, job_property_id, date_of_employed, level_of_education,
                department, employment_situation, immidate_boss, experience, pension_number, annual_rest,
                displin_situation, competency_situation, effeciency, level_of_effeciency,
                no_of_files_in_folder, employee_image, employee_file201, remark, reg_by, full_name_normalized
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

            $stmt = $this->db->prepare($sql);
            $result1 = $stmt->execute([
                $data['uuid'],
                $data['employee_id'],
                $data['first_name'],
                $data['father_name'],
                $data['last_name'],
                $data['mother_name'],
                $data['sex'],
                $data['birth_date'],
                $data['phone_number'],
                $data['yegabcha_huneta'],
                $data['organization_id'],
                $data['branch_id'],
                $data['job_property_id'],
                $data['date_of_employed'],
                $data['level_of_education'],
                $data['department'],
                $data['employment_situation'],
                $data['immidate_boss'],
                $data['experience'],
                $data['pension_number'],
                $data['annual_rest'],
                $data['displin_situation'],
                $data['competency_situation'],
                $data['effeciency'],
                $data['level_of_effeciency'],
                $data['no_of_files_in_folder'],
                $data['employee_image'],
                $data['employee_file201'],
                $data['remark'],
                $data['reg_by'],
                $normalizedFullName,
            ]);

            if (!$result1) {
                throw new \Exception("Failed to insert employee");
            }

            /* Update job_property status to 'reserved'
            $updateSql = "UPDATE job_property SET status = 'reserved' WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $result2 = $updateStmt->execute([$data['job_property_id']]);

            if (!$result2) {
                throw new \Exception("Failed to update job status");
            }
*/
 if ($guarantorData !== null) {
            $guarantorModel = new EmployeeGuarantor($this->db);
            $guarantorModel->insertGurantor($guarantorData);
        }
            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Employee registration transaction failed: " . $e->getMessage());
            return false;
        }
    } 

    public function getEmployeesByBranch(string $organizationId, string $branchId) {
        $sql = "
            SELECT 
                e.uuid,
                e.employee_id,
                e.first_name,
                e.father_name,
                e.last_name,
                e.sex,
                e.birth_date,
                e.phone_number,
                e.yegabcha_huneta,
                e.organization_id,
                e.branch_id,
                e.job_property_id,
                jp.job_name,
                jp.status as job_status,
                e.status,
                e.rdate,
                e.is_deleted
            FROM employees_table e
            LEFT JOIN job_property jp ON e.job_property_id = jp.id
            WHERE e.organization_id = ?
              AND e.branch_id = ?
              AND e.status != 'Onboarding' 
            AND e.is_deleted != 2 
            ORDER BY e.rdate DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organizationId, $branchId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getEmployeeByUuid(string $uuid) {
        $sql = "
            SELECT 
                e.*,
                jp.job_name,
                jp.salary,
                jp.dereja,
                jp.job_identifier_no,
                jp.status as job_status
            FROM employees_table e
            INNER JOIN job_property jp ON e.job_property_id = jp.id
            WHERE e.uuid = ? AND e.is_deleted != 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$uuid]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateEmployee(string $uuid, array $data, ?array $guarantorData = null): bool {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Get current employee data for comparison
            $currentEmployee = $this->getEmployeeByUuid($uuid);
            if (!$currentEmployee) {
                throw new \Exception("Employee not found");
            }
            $fullNameRaw = $data['first_name'] . ' ' . $data['father_name'] . ' ' . $data['last_name'];
            $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);
            $oldJobId = $currentEmployee['job_property_id'];
            $newJobId = $data['job_property_id'];
// 🔥 handle job safely
$oldJobId = $currentEmployee['job_property_id'];
$newJobId = $data['job_property_id'];

// ── Only reassign job if it actually changed
if ($oldJobId != $newJobId) {
    $this->assignJob($newJobId, $currentEmployee['branch_id'], $oldJobId);
}
            // Update employee
            $sql = "UPDATE employees_table SET
                employee_id = ?, pension_number = ?, first_name = ?, father_name = ?, last_name = ?, mother_name = ?,
                sex = ?, birth_date = ?, phone_number = ?, yegabcha_huneta = ?, job_property_id = ?,
                date_of_employed = ?, level_of_education = ?, department = ?, employment_situation = ?,
                immidate_boss = ?, experience = ?, annual_rest = ?, displin_situation = ?,
                competency_situation = ?, effeciency = ?, level_of_effeciency = ?,
                no_of_files_in_folder = ?, employee_image = ?, employee_file201 = ?, remark = ?, full_name_normalized = ?
                WHERE uuid = ?";

            $stmt = $this->db->prepare($sql);
            $result1 = $stmt->execute([
                $data['employee_id'],
                $data['pension_number'],
                $data['first_name'],
                $data['father_name'],
                $data['last_name'],
                $data['mother_name'],
                $data['sex'],
                $data['birth_date'],
                $data['phone_number'],
                $data['yegabcha_huneta'],
                $data['job_property_id'],
                $data['date_of_employed'],
                $data['level_of_education'],
                $data['department'],
                $data['employment_situation'],
                $data['immidate_boss'],
                $data['experience'],
                $data['annual_rest'],
                $data['displin_situation'],
                $data['competency_situation'],
                $data['effeciency'],
                $data['level_of_effeciency'],
                $data['no_of_files_in_folder'],
                $data['employee_image'],
                $data['employee_file201'],
                $data['remark'],
                $normalizedFullName,
                $uuid
            ]);

            if (!$result1) {
                throw new \Exception("Failed to update employee");
            }
/*
            // Handle job change if job was changed
            if ($oldJobId != $newJobId) {
                // Set old job back to active
                $updateOldJobSql = "UPDATE job_property SET status = 'Active' WHERE id = ?";
                $updateOldJobStmt = $this->db->prepare($updateOldJobSql);
                $result2 = $updateOldJobStmt->execute([$oldJobId]);

                if (!$result2) {
                    throw new \Exception("Failed to update old job status");
                }

                // Set new job to reserved
                $updateNewJobSql = "UPDATE job_property SET status = 'reserved' WHERE id = ?";
                $updateNewJobStmt = $this->db->prepare($updateNewJobSql);
                $result3 = $updateNewJobStmt->execute([$newJobId]);

                if (!$result3) {
                    throw new \Exception("Failed to update new job status");
                }
            }
*/
            // Commit transaction
          if ($guarantorData !== null) {
            $guarantorModel = new EmployeeGuarantor($this->db);
            $guarantorModel->upsertWithinTransaction($guarantorData);
        }  $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Employee update transaction failed: " . $e->getMessage());
            return false;
        }
    }



   public function countOnboardingEmployees(string $organizationId, string $branchId) {
    $sql = "
        SELECT COUNT(*) as total
        FROM employees_table 
        WHERE organization_id = ?
          AND branch_id = ? 
          AND status = 'Onboarding'
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$organizationId, $branchId]);
    return $stmt->fetchColumn();
}

public function getOnboardingEmployees(string $organizationId, string $branchId) {
        $sql = "
            SELECT 
                e.uuid,
                e.employee_id,
                e.first_name,
                e.father_name,
                e.last_name,
                e.sex,
                e.birth_date,
                e.phone_number,
                e.yegabcha_huneta,
                e.organization_id,
                e.branch_id,
                e.job_property_id,
                jp.job_name,
                jp.status as job_status,
                e.status,
                e.rdate
            FROM employees_table e
            LEFT JOIN job_property jp ON e.job_property_id = jp.id
            WHERE e.organization_id = ?
              AND e.branch_id = ?
              AND  e.status = 'Onboarding'
            ORDER BY e.rdate DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organizationId, $branchId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

   public function approveOnBoardingEmployee(string $uuid): bool {
    $sql = "UPDATE employees_table SET status = 'Active' WHERE uuid = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$uuid]);
}
public function autoSearch(string $term, string $branchId, ?string $source = null) {
    $cleanTerm = AmharicNormalizer::normalize($term);

    // Determine the status condition based on the source
    $statusCondition = ($source === 'onleave') ? "e.status = 'Active'" : "e.status != 'Onboarding'";

    // The query follows the order: branch_id, then status, then is_deleted
    $sql = "SELECT 
                e.uuid, 
                e.first_name, 
                e.father_name, 
                e.last_name, 
                e.employee_id, 
                e.employee_image, 
                e.deletion_source
            FROM employees_table e
            WHERE e.branch_id = ? 
            AND $statusCondition
            AND e.is_deleted != 2
            AND e.full_name_normalized LIKE ? 
           LIMIT 10";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$branchId, "%$cleanTerm%"]);
    
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
// ================================================================
    // STAGE 1 — Officer requests deletion
    // ================================================================
    public function requestDeletion(string $uuid, array $data): bool
    {
        $sql = "UPDATE employees_table SET
                    is_deleted       = 1,
                    deleted_at       = NOW(),
                    deleted_by       = :deleted_by,
                    deletion_reason  = :deletion_reason,
                    deletion_source  = :deletion_source
                WHERE uuid = :uuid
                AND is_deleted != 2";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'uuid'             => $uuid,
            'deleted_by'       => $data['deleted_by'],
            'deletion_reason'  => $data['deletion_reason'],
            'deletion_source'  => $data['deletion_source'],
        ]);
    }
  // Count pending deletions (for navbar badge)
    public function countPendingDeletions(string $branch_id): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM employees_table
             WHERE is_deleted      = 1
             AND   branch_id = :branch_id"
        );
        $stmt->execute(['branch_id' => $branch_id]);
        return (int) $stmt->fetchColumn();
    }

     // Get all pending deletion requests for director
    public function getPendingDeletions(string $branch_id): array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*,
                    CONCAT(u.first_name, ' ', u.father_name)  as deleted_by_name,
                    u.role  as deleted_by_role
             FROM employees_table e
             JOIN users u ON u.id = e.deleted_by
             WHERE e.is_deleted      = 1
             AND   e.branch_id = :branch_id
             ORDER BY e.deleted_at DESC"
        );
        $stmt->execute(['branch_id' => $branch_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
// ================================================================
    // STAGE 2 — Director approves deletion
    // ================================================================
   public function approveDeletion(string $uuid, array $data): bool
{
    try {

        // Only start a transaction if one doesn't already exist
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        } else {
            $startedTransaction = false;
        }

        $stmt = $this->db->prepare("
            SELECT job_property_id, branch_id
            FROM employees_table
            WHERE uuid = ?
            FOR UPDATE
        ");
        $stmt->execute([$uuid]);

        $employee = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$employee) {
            throw new \Exception("Employee not found");
        }

        $stmt = $this->db->prepare("
            UPDATE employees_table
            SET
                is_deleted = 2,
                deletion_approved_by = :approved_by,
                deletion_approved_at = NOW()
            WHERE uuid = :uuid
              AND is_deleted = 1
        ");

        $stmt->execute([
            'uuid' => $uuid,
            'approved_by' => $data['approved_by']
        ]);

        if ($stmt->rowCount() === 0) {
            throw new \Exception("Delete approval failed");
        }

        $stmt = $this->db->prepare("
            UPDATE employees_guarantors
            SET
                deletion_source = 'CASCADE',
                is_deleted = 1
            WHERE employee_id = ?
              AND is_deleted = 0
        ");

        $stmt->execute([$uuid]);

        if ($startedTransaction) {
            $this->db->commit();
        }

        return true;

    } catch (\Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        error_log("Deletion approval failed: " . $e->getMessage());

        return false;
    }
}

    // ================================================================
    // STAGE 3 — Director rejects deletion
    // ================================================================
    public function rejectDeletion(string $uuid, array $data): bool
    {
        $sql = "UPDATE employees_table SET
                    is_deleted                = 3,
                    deletion_approved_by      = :approved_by,
                    deletion_approved_at      = NOW(),
                    deletion_rejection_reason = :rejection_reason
                WHERE uuid = :uuid
                AND is_deleted = 1";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'uuid'             => $uuid,
            'approved_by'      => $data['approved_by'],
            'rejection_reason' => $data['rejection_reason'],
        ]);
    }
// Get approved deletions archive
    public function getApprovedDeletions(string $branchId): array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*,
                    CONCAT(u1.first_name, ' ', u1.father_name) as deleted_by_name,
                    CONCAT(u2.first_name, ' ', u2.father_name) as approved_by_name
             FROM employees_table e
             LEFT JOIN users u1 ON u1.id = e.deleted_by
             LEFT JOIN users u2 ON u2.id = e.deletion_approved_by
             WHERE e.is_deleted      = 2
             AND   e.branch_id = :branch_id
             ORDER BY e.deletion_approved_at DESC"
        );
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get rejected deletions log
    public function getRejectedDeletions(string $branchId): array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*,
                    CONCAT(u1.first_name, ' ', u1.father_name) as deleted_by_name,
                    CONCAT(u2.first_name, ' ', u2.father_name) as approved_by_name
             FROM employees_table e
             LEFT JOIN users u1 ON u1.id = e.deleted_by
             LEFT JOIN users u2 ON u2.id = e.deletion_approved_by
             WHERE e.is_deleted      = 3
             AND   e.branch_id = :branch_id
             ORDER BY e.deletion_approved_at DESC"
        );
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

public function getAllEmployeesCount($branch_id = null) {
        try {
            // Strict condition assignment
            $branchCondition = !empty($branch_id) ? "branch_id = :branch_id" : "1=1";
            
            $query = "SELECT COUNT(*) as total 
                      FROM employees_table 
                      WHERE {$branchCondition} 
                        AND (status != 'inactive' AND status != 'Onboarding') 
                        AND is_deleted != 2";
            
            $stmt = $this->db->prepare($query);
            
            if (!empty($branch_id)) {
                $stmt->bindValue(':branch_id', $branch_id, \PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $row ? (int)$row['total'] : 0;
            
        } catch (\PDOException $e) {
            error_log("Database Error in Employee::getAllEmployeesCount: " . $e->getMessage());
            return 0;
        }
    }

    public function getOnleaveEmployeesCount($branch_id = null) {
        try {
            $branchCondition = !empty($branch_id) ? "branch_id = :branch_id" : "1=1";
            
            $query = "SELECT COUNT(*) as total 
                      FROM employees_table 
                      WHERE {$branchCondition} 
                        AND status = 'On Leave' 
                        AND is_deleted != 2";
            
            $stmt = $this->db->prepare($query);
            
            if (!empty($branch_id)) {
                $stmt->bindValue(':branch_id', $branch_id, \PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $row ? (int)$row['total'] : 0;
            
        } catch (\PDOException $e) {
            error_log("Database Error in Employee::getOnleaveEmployeesCount: " . $e->getMessage());
            return 0;
        }
    }

    public function getStudyleaveEmployeesCount($branch_id = null) {
        try {
            $branchCondition = !empty($branch_id) ? "branch_id = :branch_id" : "1=1";
            
            $query = "SELECT COUNT(*) as total 
                      FROM employees_table 
                      WHERE {$branchCondition} 
                        AND status = 'Study Leave' 
                        AND is_deleted != 2";
            
            $stmt = $this->db->prepare($query);
            
            if (!empty($branch_id)) {
                $stmt->bindValue(':branch_id', $branch_id, \PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $row ? (int)$row['total'] : 0;
            
        } catch (\PDOException $e) {
            error_log("Database Error in Employee::getStudyleaveEmployeesCount: " . $e->getMessage());
            return 0;
        }
    }
    }

