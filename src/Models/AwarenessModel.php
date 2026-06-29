<?php
namespace App\Models;
use PDO;
class AwarenessModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
public function createAwareness(array $data): bool {
   try {
      
            $this->db->beginTransaction();
          // Insert employee
             $sql = "INSERT INTO awareness_creation_other (
                branch_id, yemenoriya_akababi, fullname, sex, awareness_type, fiscal_year, regby) VALUES (
                ?, ?, ?, ?,?,?, ?
            )";

            $stmt = $this->db->prepare($sql);
            $result1 = $stmt->execute([
                $data['branch_id'],
                $data['yemenoriya_akababi'],
                $data['fullname'],
                $data['sex'],
                $data['awareness_type'],
                $data['fiscal_year'],
                $data['reg_by'],
            ]);

            if (!$result1) {
                throw new \Exception("Failed to insert awareness");
            }

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Awareness registration transaction failed: " . $e->getMessage());
            return false;
        }
    } 
public function getallawarenessbybranch($branch_id) {
        $sql = "SELECT * FROM awareness_creation_other WHERE branch_id = :branch_id ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
public function getawarnessbyid($id) {
        $sql = "SELECT * FROM awareness_creation_other WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
