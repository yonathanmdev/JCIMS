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
 
public function getEnterpriseSectorAndSubsector($code003_id) {
        // 1. በመጀመሪያ ከ code003 እና ከ junction_table የ enterprise_type እና አገናኝ IDዎችን እንወስዳለን
        $sql = "SELECT 
                    c.enterprise_type, 
                    j.individual_enterprise_id, 
                    j.team_id 
                FROM code003 c
                INNER JOIN junction_table_individual_and_team j 
                    ON c.junction_table_id = j.table_id
                WHERE c.code003_id = :code003_id 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code003_id' => $code003_id]);
        $junctionData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$junctionData) {
            return null;
        }

        $subSectorId = null;

        // 2. እንደ ኢንተርፕራይዙ አይነት (የግል ወይም ማህበር) sub_sector IDን እንወስዳለን
        if ($junctionData['enterprise_type'] === 'የግል') {
            $sqlInd = "SELECT sub_sector FROM individual_enterprise WHERE individual_ent_id = :ind_id LIMIT 1";
            $stmtInd = $this->db->prepare($sqlInd);
            $stmtInd->execute(['ind_id' => $junctionData['individual_enterprise_id']]);
            $res = $stmtInd->fetch(\PDO::FETCH_ASSOC);
            $subSectorId = $res['sub_sector'] ?? null;

        } else if ($junctionData['enterprise_type'] === 'የማህበር') {
            $sqlGroup = "SELECT sub_sector FROM group_table WHERE table_id = :team_id LIMIT 1";
            $stmtGroup = $this->db->prepare($sqlGroup);
            $stmtGroup->execute(['team_id' => $junctionData['team_id']]);
            $res = $stmtGroup->fetch(\PDO::FETCH_ASSOC);
            $subSectorId = $res['sub_sector'] ?? null;
        }

        if (!$subSectorId) {
            return null;
        }

        // 3. የተገኘውን sub_sector ID በመጠቀም ከ sub_sector ጠረጴዛ ላይ sectorid እና sub_sectorid እንወስዳለን
        $sqlSub = "SELECT sub_sectorid, sectorid FROM sub_sector WHERE sub_sectorid = :sub_id LIMIT 1";
        $stmtSub = $this->db->prepare($sqlSub);
        $stmtSub->execute(['sub_id' => $subSectorId]);
        
        return $stmtSub->fetch(\PDO::FETCH_ASSOC);
    }

    public function registerJobCreation($data) {
        try {
            // 1. Transaction እንጀምራለን
            $this->db->beginTransaction();

            // 2. ቼክ ማድረግ: ይህ ስራ ፈላጊ በቋሚነት (Type 1) ቀድሞ ተቀጥሮ ይሆን?
            $sqlCheck = "SELECT jobseeker_id FROM code003sraedl WHERE jobseeker_id = :js_id AND employment_type = 1";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute(['js_id' => $data['jobseeker_id']]);
            
            if ($stmtCheck->fetch()) {
                throw new Exception("ይህ ስራ ፈላጊ ቀድሞውኑ በቋሚነት ስራ ተፈጥሮለታል፤ ሌላ መዝገብ ማስገባት አይቻልም።");
            }

            // 3. ምክንያቱ 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' ከሆነ ዘርፍና ንዑስ ዘርፉን ከኢንተርፕራይዙ እንፈልጋለን
            if ($data['job_creation_reason'] === "አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ") {
                $entInfo = $this->getEnterpriseSectorAndSubsector($data['code003_id']);
                
                if (!$entInfo) {
                    throw new Exception("የተመረጠው ኢንተርፕራይዝ የዘርፍ እና ንዑስ ዘርፍ መረጃ አልተገኘለትም! እባክዎን የኢንተርፕራይዙን ምዝገባ ያረጋግጡ።");
                }

                $data['subsector'] = $entInfo['sub_sectorid'];
                $data['sector']    = $entInfo['sectorid'];
            }

            // 4. የ job_seekers ሰንጠረዥን እናዘምናለን (የቅጥር ሁኔታውን)
            $sqlUpdate = "UPDATE job_seekers SET employment_status = :status WHERE job_seeker_id = :js_id";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([
                'status' => $data['employment_type'],
                'js_id'  => $data['jobseeker_id']
            ]);

            // 5. የሥራ እድል መረጃን በ code003sraedl ውስጥ እናስገባለን
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

            // 6. Commit እናደርጋለን
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
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
public function searchEnterprisesByBranch(string $search, $branchId, int $limit = 15): array {
        // SQL query: branchid እኩል መሆኑን እናረጋግጣለን
        $sql = "SELECT id, code003_id, enterprisename, tine_number, branch_id 
                FROM code003 
                WHERE branch_id = :branchid 
                  AND (enterprisename LIKE :search OR tine_number LIKE :search)
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $searchTerm = "%" . $search . "%";

        // Parameters Binding (ለደህንነት ሲባል)
        $stmt->bindValue(':branchid', $branchId, PDO::PARAM_STR);
        $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}