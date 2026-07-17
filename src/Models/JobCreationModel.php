<?php
namespace App\Models;
use PDO;
use Exception;
class JobCreationModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // ይህን በModelህ ውስጥ ተጠቀምበት
 
public function registerJobCreation($data) {
    try {
        // 1. Transaction እንጀምራለን
        $this->db->beginTransaction();
// 1. ቼክ ማድረግ: ይህ ስራ ፈላጊ በቋሚነት (Type 1) ቀድሞ ተቀጥሮ ይሆን?
        $sqlCheck = "SELECT jobseeker_id FROM code003sraedl WHERE jobseeker_id = :js_id AND employment_type = 1";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute(['js_id' => $data['jobseeker_id']]);
        
        if ($stmtCheck->fetch()) {
            // መረጃው ከተገኘ Transaction-ውን አቋርጠን Error እንወረውራለን
            throw new Exception("ይህ ስራ ፈላጊ ቀድሞውኑ በቋሚነት ስራ ተፈጥሮለታል፤ ሌላ መዝገብ ማስገባት አይቻልም።");
        }
        // 2. በመጀመሪያ የ job_seekers ሰንጠረዥን እናዘምናለን (የቅጥር ሁኔታውን)
        $sqlUpdate = "UPDATE job_seekers SET employment_status = :status WHERE job_seeker_id = :js_id";
        $stmtUpdate = $this->db->prepare($sqlUpdate);
        $stmtUpdate->execute([
            'status' => $data['employment_type'],
            'js_id'  => $data['jobseeker_id']
        ]);

        // 3. በመቀጠል የሥራ እድል መረጃን በ code003sraedl ውስጥ እናስገባለን
        $sql = "INSERT INTO code003sraedl (
                    uuid, branchid, code003_id, jobseeker_id, sector, subsector, 
                    job_creation_reason, employment_type, employed_institution, 
                    ssuportedname, fiscal_year, registered_by, job_field
                ) VALUES (
                    UNHEX(:uuid), :branchid, :code003_id, :jobseeker_id, :sector, :subsector, 
                    :job_creation_reason, :employment_type, :employed_institution, 
                    :suportedby, :fiscal_year, :registered_by, :job_field
                )";
        
        $stmt = $this->db->prepare($sql);
        
        // UUID v7 ማመንጨት
        $data['uuid'] = $this->generateUuidV7();
        
        // SQL ላይ መጠየቂያዎቹ ከ $data ቁልፎች ጋር ተመሳሳይ እንዲሆኑ ማድረግ
        // (SQL ውስጥ ':suportedby' ስንጠቀም በ $data ውስጥ 'suportedby' መኖሩን ያረጋግጣል)
        $stmt->execute([
            'uuid'                 => $data['uuid'],
            'branchid'             => $data['branchid'],
            'code003_id'           => $data['code003_id'],
            'jobseeker_id'         => $data['jobseeker_id'],
            'sector'               => $data['sector'],
            'subsector'            => $data['subsector'],
            'job_creation_reason'  => $data['job_creation_reason'],
            'employment_type'      => $data['employment_type'],
            'employed_institution' => $data['employed_institution'],
            'suportedby'           => $data['suportedby'],
            'fiscal_year'          => $data['fiscal_year'],
            'registered_by'        => $data['registered_by'],
            'job_field'            => $data['job_field'],
        ]);

        // 4. ሁለቱም በተሳካ ሁኔታ ከተከናወኑ Commit እናደርጋለን
        $this->db->commit();
        return true;

    } catch (\Exception $e) {
        // ማንኛውም ስህተት ከተፈጠረ ሙሉ በሙሉ ወደ ኋላ ይመለሳል (Rollback)
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        // ስህተቱን ወደ ሎግ እንዲጽፍ ወደ ኮንትሮለሩ እናስተላልፋለን
        throw $e;
    }
}

    private function generateUuidV7() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x70);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return bin2hex($data);
    }
    public function getJobCreationsWithDetails($branchId, $offset, $limit) {
    // ትክክለኛዎቹን የሰንጠረዥ ስሞች ተጠቅመን Join አደረግን
    $sql = "SELECT c.*, 
       j.first_name, j.father_name, 
       s.sector, 
       sub.subsector,
       p.pname as ngo_name,
       b.name as branch_name 
FROM code003sraedl c
LEFT JOIN job_seekers j ON c.jobseeker_id = j.job_seeker_id
LEFT JOIN sector_table s ON c.sector = s.sectorid
LEFT JOIN sub_sector sub ON c.subsector = sub.sub_sectorid
LEFT JOIN projectngos p ON c.ssuportedname = p.pid
/* የቅርንጫፍ መዋቅር ለማግኘት branches ቴብልን መጀመሪያ እናያይዛለን */
INNER JOIN branches b ON c.branchid = b.internal_id
/* የሂራርኪካል ማጣሪያውን ለማድረግ root ቅርንጫፍን እናያይዛለን */
INNER JOIN branches root ON root.internal_id = :branchid
/* የሂራርኪ ማጣሪያው፦ የዳታው ቅርንጫፍ (b.path) የroot ቅርንጫፍ (root.path) አካል መሆኑን እናረጋግጣለን */
WHERE b.path LIKE CONCAT(root.path, '%')
ORDER BY c.created_at DESC
LIMIT :offset, :limit";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':branchid', $branchId, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getTotalCount($branchId) {
    $sql = "SELECT COUNT(*) FROM code003sraedl WHERE branchid = :branchid";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['branchid' => $branchId]);
    return $stmt->fetchColumn();
}

public function deletearchiveJobCreation($uuid, $branchId, $jobSeekerId, $reason, $userId) {
    try {
        $this->db->beginTransaction();

        // 1. መረጃውን መጀመሪያ እናምጣው (ለ Snapshot)
        $sqlSelect = "SELECT * FROM code003sraedl WHERE uuid = :uuid AND branchid = :branchid AND jobseeker_id = :js_id FOR UPDATE";
        $stmtSelect = $this->db->prepare($sqlSelect);
        $stmtSelect->execute(['uuid' => $uuid, 'branchid' => $branchId, 'js_id' => $jobSeekerId]);
        $data = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new Exception("መረጃው አልተገኘም ወይም የዚህ ቅርንጫፍ አይደለም።");
        }

        // 2. ወደ አርካይቭ ቴብል ማስገባት
        $sqlArchive = "INSERT INTO data_archive (id, entity_type, original_id, snapshot, archived_at, archived_by, reason) 
                       VALUES (:id, :entity_type, :original_id, :snapshot, NOW(), :archived_by, :reason)";
        
        $stmtArchive = $this->db->prepare($sqlArchive);
        $stmtArchive->execute([
            'id'           => $this->generateUuidV7(), // አዲስ UUID ለ አርካይቭ መዝገቡ
            'entity_type'  => 'code003sraedl',
            'original_id'  => $uuid, // በ BINARY format ሊሆን ይችላል፣ እንደလိုအပ်ቱ ተጠቀምበት
            'snapshot'     => json_encode($data), // መረጃውን እንደ JSON ማስቀመጥ
            'archived_by'  => $userId,
            'reason'       => $reason
        ]);

        // 3. job_seekers ሰንጠረዥን ማዘመን
        $sqlUpdate = "UPDATE job_seekers SET employment_status = '0' WHERE job_seeker_id = :js_id";
        $this->db->prepare($sqlUpdate)->execute(['js_id' => $jobSeekerId]);

        // 4. መዝገቡን መሰረዝ
        $sqlDelete = "DELETE FROM code003sraedl WHERE uuid = :uuid";
        $this->db->prepare($sqlDelete)->execute(['uuid' => $uuid]);

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        error_log("Archive Error: " . $e->getMessage());
        return false;
    }
}
}