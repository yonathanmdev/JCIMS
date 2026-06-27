<?php
namespace App\Models;
use App\Helpers\AmharicNormalizer;
use App\Models\EmployeeGuarantor;
use PDO;
class JobSeekerModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


 public function createJobseeker(array $data): bool {
        try {
            $fullNameRaw = $data['first_name'] . ' ' . $data['father_name'] . ' ' . $data['g_father_name'];
            $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);
            // Start transaction
            $this->db->beginTransaction();
          // Insert employee
            $sql = "INSERT INTO job_seekers (
                id, first_name, father_name, last_name, gender, organization_id,
                branch_id, full_name_normalized, registered_by
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $result1 = $stmt->execute([
                $data['uuid'],
                $data['first_name'],
                $data['father_name'],
                $data['g_father_name'],
                $data['sex'],
                $data['organization_id'],
                $data['branch_id'],
                $normalizedFullName,
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
public function getJobSeekersByHierarchy(string $myBranchId, string $organizationId, int $limit = 50, int $offset = 0): array {
    $sql = "SELECT js.id, js.first_name, js.father_name, js.last_name,js.gender, b.name AS branch_name, b.level AS branch_level
            FROM job_seekers js
            INNER JOIN branches b ON js.branch_id = b.id
            WHERE js.organization_id = :org_id
              AND b.path LIKE CONCAT(
                    (SELECT path FROM branches WHERE id = :my_branch), '%'
                  )
            ORDER BY js.created_at DESC
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':org_id', $organizationId);
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

