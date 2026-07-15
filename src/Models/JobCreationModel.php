<?php
namespace App\Models;
use PDO;
class JobCreationModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // ይህን በModelህ ውስጥ ተጠቀምበት
 
public function registerJobCreation($data) {
        $sql = "INSERT INTO code003sraedl (
                    uuid, branchid, code003_id, jobseeker_id, sector, subsector, 
                    job_creation_reason, employment_type, employed_institution, 
                    ssuportedname, fiscal_year, registered_by
                ) VALUES (
                    UNHEX(:uuid), :branchid, :code003_id, :jobseeker_id, :sector, :subsector, 
                    :job_creation_reason, :employment_type, :employed_institution, 
                    :suportedby, :fiscal_year, :registered_by
                )";
        
        $stmt = $this->db->prepare($sql);
        
        // UUID v7 ማመንጨት
        $data['uuid'] = $this->generateUuidV7();
        
        return $stmt->execute($data);
    }

    private function generateUuidV7() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x70);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return bin2hex($data);
    }
}