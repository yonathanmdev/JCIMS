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
    } // 1. $page ን በፓራሜትር አስገባ
public function getallawarenessbybranch($branch_id, $page = 1) {
    try {       
        $limit = 20;
        $page = (int)$page > 0 ? (int)$page : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT a.*, b.name 
                FROM awareness_creation_other a
                INNER JOIN branches b ON a.branch_id = b.internal_id
                WHERE a.branch_id = :branch_id 
                ORDER BY a.tbleid DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        // 2. እነዚህን ሁለቱን እዚህ ጋር ማሰርህን እርግጠኛ ሁን
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. አጠቃላይ ገጾችን ለመቁጠር ይህንን ጨምር
        $countSql = "SELECT COUNT(*) FROM awareness_creation_other WHERE branch_id = :branch_id";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $countStmt->execute();
        $totalPages = ceil($countStmt->fetchColumn() / $limit);

        // 4. በ Array መልሰው
        return [
            'data' => $records,
            'total_pages' => $totalPages,
            'current_page' => $page
        ];

    } catch (\PDOException $e) { 
        
        error_log("Error fetching awareness records: " . $e->getMessage());
        return [
            'data' => [],
            'total_pages' => 0,
            'current_page' => $page
        ];
    }
}
 public function updateAwareness($id, array $data): bool {
        try {
            $sql = "UPDATE awareness_creation_other SET 
                        fullname = :fullname,
                        sex = :sex,
                        yemenoriya_akababi = :yemenoriya_akababi,
                        awareness_type = :awareness_type
                    WHERE tbleid = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fullname', $data['fullname'], PDO::PARAM_STR);
            $stmt->bindParam(':sex', $data['sex'], PDO::PARAM_STR);
            $stmt->bindParam(':yemenoriya_akababi', $data['yemenoriya_akababi'], PDO::PARAM_STR);
            $stmt->bindParam(':awareness_type', $data['awareness_type'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating awareness: " . $e->getMessage());
            return false;
        }
    }
    /**
 * በሙሉ ስም ብቻ ለይቶ ለመፈለግ የተዘጋጀ ራሱን የቻለ ፈንክሽን
 */
public function searchAwarenessByName($branch_id, $searchName, $page = 1) {
    try {   
        $limit = 500;
        $page = (int)$page > 0 ? (int)$page : 1;
        $offset = ($page - 1) * $limit;

        // 1. መረጃውን በስም እና በቅርንጫፍ ለይቶ የሚያመጣው SQL
        $sql = "SELECT a.*, b.name 
                FROM awareness_creation_other a
                INNER JOIN branches b ON a.branch_id = b.internal_id
                WHERE a.branch_id = :branch_id 
                  AND a.fullname LIKE :search_name 
                ORDER BY a.tbleid DESC
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        
        // የ % ምልክቶችን ከደህንነት ጥበቃ (XSS/SQL Injection) ጋር ማዋሃድ
        $searchTerm = "%" . $searchName . "%";
        
        // ፓራሜትሮችን ማሰር (Bind)
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt->bindParam(':search_name', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. ለፍለጋው ውጤት ብቻ የሚሆን ጠቅላላ መዝገብ መቁጠሪያ (Total Pages ማስያዣ)
        $countSql = "SELECT COUNT(*) FROM awareness_creation_other a 
                     WHERE a.branch_id = :branch_id 
                       AND a.fullname LIKE :search_name";
                     
        $countStmt = $this->db->prepare($countSql);
        $countStmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $countStmt->bindParam(':search_name', $searchTerm, PDO::PARAM_STR);
        $countStmt->execute();
        
        $totalRecords = $countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // ውጤቱን በተደራጀ Array መመለስ
        return [
            'data'         => $records,
            'total_pages'  => $totalPages,
            'current_page' => $page
        ];

    } catch (\PDOException $e) {
        error_log("Error in searchAwarenessByName: " . $e->getMessage());
        return ['data' => [], 'total_pages' => 0, 'current_page' => 1];
    }
}
/**
 * ለፍለጋ ምክር (Recommendation) በቅርንጫፍ ውስጥ ያሉ ስሞችን ብቻ ለይቶ ማምጫ
 */
public function getUniqueNamesByBranch($branch_id, $searchName) {
    try {
        $sql = "SELECT DISTINCT a.fullname 
                FROM awareness_creation_other a
                WHERE a.branch_id = :branch_id 
                  AND a.fullname LIKE :search_name 
                ORDER BY a.fullname ASC 
                LIMIT 10"; // ሰርቨሩ እንዳይጨናነቅ 10 ምርጥ ምክሮችን ብቻ ማሳየት
                
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%" . $searchName . "%";
        
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt->bindParam(':search_name', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        
        // ስሞቹን ብቻ ወደ ነጠላ Array መቅየር
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (\PDOException $e) {
        error_log("Error in getUniqueNamesByBranch: " . $e->getMessage());
        return [];
    }
}
/**
 * 🔍 ስራ ፈላጊዎችን በቅርንጫፍ፣ በስም፣ በስልክ፣ በLabor ID ወይም በG8ID መፈለጊያ
 *//**
 * 🔍 ስራ ፈላጊዎችን በስም፣ በስልክ፣ በLabor ID ወይም በ Job Seeker ID መፈለጊያ
 */
public function searchJobSeekersForAwareness($branch_id, $searchName) {
    try {
        $searchName = trim($searchName);
        
        // 💡 የጻፍከው ቃል ንጹህ ቁጥር መሆኑን ማረጋገጥ (ለምሳሌ፡ 123456)
        $isNumeric = is_numeric($searchName);
        $idExact = $isNumeric ? (int)$searchName : 0;

        // 🚀 ቁጥር ከሆነ በቀጥታ በ '=' እንዲፈልግ፣ ጽሑፍ ከሆነ በ 'LIKE' እንዲያነጻጽር የተዋቀረ SQL
        $sql = "SELECT `job_seeker_id`, `first_name`, `father_name`, `last_name`, `Labor_ID`, `phone_number`
                FROM `job_seekers` 
                WHERE (
                       (:is_numeric = 1 AND `job_seeker_id` = :id_exact)
                    OR `job_seeker_id` LIKE :search
                    OR `first_name` LIKE :search 
                    OR `father_name` LIKE :search 
                    OR `last_name` LIKE :search 
                    OR `phone_number` LIKE :search 
                    OR `Labor_ID` LIKE :search 
                    OR `g8id` LIKE :search
                )
                LIMIT 10";
                
        $stmt = $this->db->prepare($sql);
        
        // ፍለጋውን በ % መክበብ
        $searchTerm = "%" . $searchName . "%";
        
        // ማሰሪያዎችን (Bindings) ማደራጀት
        $isNumericFlag = $isNumeric ? 1 : 0;
        $stmt->bindParam(':is_numeric', $isNumericFlag, PDO::PARAM_INT);
        $stmt->bindParam(':id_exact', $idExact, PDO::PARAM_INT);
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Error in searchJobSeekersForAwareness: " . $e->getMessage());
        return [];
    }
}

/**
 * 🔄 የተመረጠውን ሰው awareness ወደ 1 ማዘመሪያ (Update)
 */
public function setAwarenessTrue($job_seeker_id) {
    try {
        $sql = "UPDATE `job_seekers` 
                SET `awareness` = 1 
                WHERE `job_seeker_id` = :job_seeker_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (\PDOException $e) {
        error_log("Error in setAwarenessTrue: " . $e->getMessage());
        return false;
    }
}
}