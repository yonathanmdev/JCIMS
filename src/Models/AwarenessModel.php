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

    }
