<?php
namespace App\Models;

use PDO;
use Exception;

class InformalTradeModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * የኢ-መደበኛ ንግድ ተሰማሪ መረጃን ወደ ዳታቤዝ ማስገቢያ
     */
    public function registerInformalTrader(array $data): bool {
        $sql = "INSERT INTO `informal_trade_registry` (
                    `branch_id`,
                    `full_name`,
                    `gender`,
                    `age`,
                     
                    `reszone`,
                    `resworeda`,
                    `res_kebele`,
                    `phone`,
                    `trade_area_type`,
                    `has_kebele_id`,
                    `kebele_id_number`,
                    `start_year`,
                    `sub_sector`,
                    `job_position`,
                    `work_branch_id`,
                    `nearby_center_name`,
                    `regby`,
                    `created_at`
                ) VALUES (
                    :branch_id,
                    :full_name,
                    :gender,
                    :age,
                   
                    :reszone,
                    :resworeda,
                    :res_kebele,
                    :phone,
                    :trade_area_type,
                    :has_kebele_id,
                    :kebele_id_number,
                    :start_year,
                    :sub_sector,
                    :job_position,
                    :work_branch_id,
                    :nearby_center_name,
                    :regby,
                    NOW()
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'branch_id'          => $data['branch_id'],
            'full_name'          => $data['full_name'],
            'gender'             => $data['gender'],
            'age'                => $data['age'],
            
            'reszone'            => $data['reszone'],
            'resworeda'          => $data['resworeda'],
            'res_kebele'         => $data['res_kebele'],
            'phone'              => $data['phone'],
            'trade_area_type'    => $data['trade_area_type'],
            'has_kebele_id'      => $data['has_kebele_id'],
            'kebele_id_number'   => $data['kebele_id_number'],
            'start_year'         => $data['start_year'],
            'sub_sector'         => $data['sub_sector'],
            'job_position'       => $data['job_position'],
            'work_branch_id'     => $data['work_branch_id'],
            'nearby_center_name' => $data['nearby_center_name'],
            'regby'              => $data['regby']
        ]);
    }
    /**
     * የተመዘገቡትን የኢ-መደበኛ ንግድ ተሰማሪዎች ዝርዝር ማምጫ
     */
    public function getInformalTradersList($myBranchId) {
        $sql = "SELECT 
                    itr.*,
                    ss.subsector AS sub_sector_name,
                    wb.name AS work_branch_name,
                    u.first_name AS regby_name,
                    u.father_name AS father_name
                FROM informal_trade_registry itr
                LEFT JOIN sub_sector ss ON itr.sub_sector = ss.sub_sectorid
                INNER JOIN branches b ON itr.branch_id = b.internal_id
                INNER JOIN branches root ON root.internal_id = :my_branch
                LEFT JOIN branches wb ON itr.work_branch_id = wb.internal_id
                LEFT JOIN users u ON itr.regby = u.user_id
                WHERE b.path LIKE CONCAT(root.path, '%')
                ORDER BY itr.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['my_branch' => $myBranchId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * መረጃን ወደ data_archive አርካይቭ አድርጎ ከዋናው ሰንጠረዥ ማጥፊያ
     */
     /**
     * መረጃን ወደ data_archive አርካይቭ አድርጎ ከዋናው ሰንጠረዥ ማጥፊያ
     */
    public function archiveAndDeleteTrader($id, $userId, $reason = 'በተጠቃሚው ተሰርዟል') {
        try {
            // PDO ኤክሴፕሽን እንዲጥል ማረጋገጥ (Debugging ለማድረግ)
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Transaction ማስጀመር
            $this->db->beginTransaction();

            // 1. መረጃውን ከመሰረዙ በፊት ከዋናው ሰንጠረዥ ፈልጎ ማግኘት
            $stmt = $this->db->prepare("SELECT * FROM `informal_trade_registry` WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $record = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$record) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                $_SESSION['error'] = "የሚሰረዘው መረጃ በዳታቤዝ ውስጥ አልተገኘም።";
                return false;
            }

            // 2. መረጃውን በሙሉ ወደ JSON snapshot መቀየር
            $snapshot = json_encode($record, JSON_UNESCAPED_UNICODE);

            // 3. ወደ data_archive ሰንጠረዥ ማስገባት
            $archiveSql = "INSERT INTO `data_archive` (
                                `entity_type`, 
                                `original_id`, 
                                `snapshot`, 
                                `archived_at`, 
                                `archived_by`, 
                                `reason`
                           ) VALUES (
                                'informal_trade_registry', 
                                :original_id, 
                                :snapshot, 
                                NOW(), 
                                :archived_by, 
                                :reason
                           )";
            
            $archiveStmt = $this->db->prepare($archiveSql);
            $archiveStmt->execute([
                'original_id' => $id,
                'snapshot'    => $snapshot,
                'archived_by' => !empty($userId) ? $userId : null,
                'reason'      => $reason
            ]);

            // 4. ከዋናው ሰንጠረዥ ማጥፋት
            $deleteStmt = $this->db->prepare("DELETE FROM `informal_trade_registry` WHERE `id` = :id");
            $deleteStmt->execute(['id' => $id]);

            // ሁለቱም ከተሳኩ Transaction ማጽናት
            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            // ስህተት ከተፈጠረ Rollback ማድረግ
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // እውነተኛውን የ PDO ስህተት ለጊዜው እንዲያሳየን እንይዘዋለን
            $_SESSION['error'] = "DB Error: " . $e->getMessage();
            error_log("Archive Delete Error: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $_SESSION['error'] = "General Error: " . $e->getMessage();
            return false;
        }
    }
    public function getTradeById($id) {
        $sql = "SELECT `id`, `branch_id`, `full_name`, `gender`, `age`, `resbranch_id`, `reszone`, `resworeda`, `res_kebele`, `phone`, `trade_area_type`, `has_kebele_id`, `kebele_id_number`, `start_year`, `sub_sector`, `job_position`, `work_branch_id`, `nearby_center_name`, `created_at`, `regby`, `updated_at` 
                FROM `informal_trade_registry` 
                WHERE `id` = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function saveTradeData($data) {
        try {
            if (!empty($data['id'])) {
                // መረጃውን ማሻሻል (Update)
                
                // አዲስ መረጃ መመዝገብ (Insert)
                $sql = "INSERT INTO informal_trade_conversions 
                        (informalid, bugetamet, regby ,nid, trade_area_type, sector, sub_sector, job_position, start_year, has_support, financial_amount, loan_amount, machinery_unit, land_unit, shed_unit, market_amount, other_unit, support_types_json) 
                        VALUES 
                        (:informalid, :bugetamet, :regby, :nid, :trade_area_type, :sector, :sub_sector, :job_position, :start_year, :has_support, :financial_amount, :loan_amount, :machinery_unit, :land_unit, :shed_unit, :market_amount, :other_unit, :support_types_json)";

                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    'informalid'       => $data['id'],
                    ':bugetamet'        => $data['bugetamet'],
                    ':regby'            => $data['regby'],
                    ':nid'              => $data['nid'],
                    ':trade_area_type'  => $data['trade_area_type'],
                    ':sector'           => $data['sector'],
                    ':sub_sector'       => $data['sub_sector'],
                    ':job_position'     => $data['job_position'],
                    ':start_year'       => $data['start_year'],
                    ':has_support'      => $data['has_support'],
                    ':financial_amount' => $data['financial_amount'],
                    ':loan_amount'      => $data['loan_amount'],
                    ':machinery_unit'   => $data['machinery_unit'],
                    ':land_unit'        => $data['land_unit'],
                    ':shed_unit'        => $data['shed_unit'],
                    ':market_amount'    => $data['market_amount'],
                    ':other_unit'       => $data['other_unit'],
                    ':support_types_json' => $data['support_types_json']
                ]);
            }
        } catch (\PDOException $e) {
            // የስህተት መዝገብ (Log error if needed)
            return false;
        }
    }
    public function getAllTradeDetails($myBranchId) {
    try {
        // ቅርንጫፍ (Branch) መሠረት አድርጎ መረጃውን ለማጣራት WHERE ክላውስ ተጨምሯል
        $query = "SELECT * FROM vw_informal_trade_complete_details WHERE registry_branch_id = :branch_id ORDER BY conversion_created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        // SQL Injection ጥቃትን ለመከላከል ቫርያብሉን በደህንነት ማሰር (Binding)
        $stmt->bindParam(':branch_id', $myBranchId, \PDO::PARAM_INT); // እንደ ዳታቤዝዎ ዓይነት PARAM_STR ሊሆን ይችላል
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // መረጃው ባዶ ከሆነ ባዶ አርሬ (Array) መመለሱን ማረጋገጥ
        return is_array($result) ? $result : [];
        
    } catch (\PDOException $e) {
        // የሰለቸውን የውሂብ ማከማቻ ስህተት መዝገብ (Log) ማድረግ
       // error_log("Database Error: " . $e->getMessage());
        return [];
    }
}
public function getTradeDetailById($conversionId) {
        try {
            $query = "SELECT * FROM vw_informal_trade_complete_details WHERE conversion_id = :conversion_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':conversion_id', $conversionId, \PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return is_array($result) ? $result : [];
            
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }
}