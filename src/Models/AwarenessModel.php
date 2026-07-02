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
public function getAllAwarenessByBranch($my_branch, $page = 1) {
    try {       
        $limit = 20;
        $page = (int)$page > 0 ? (int)$page : 1;
        $offset = ($page - 1) * $limit;

        // 1. በአዲሱ እና ይበልጥ ፈጣን በሆነው የ INNER JOIN መዋቅር ዋናውን ዳታ ማምጣት
        $sql = "SELECT a.*, b.name AS branch_name
                FROM awareness_creation_other a
                INNER JOIN branches b ON a.branch_id = b.internal_id
                INNER JOIN branches root ON root.internal_id = :my_branch
                WHERE b.path LIKE CONCAT(root.path, '%')
                ORDER BY a.tbleid DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $my_branch);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. የገጽ ቁጥር መቁጠሪያውንም (Count SQL) በተመሳሳይ ፈጣን የ JOIN መዋቅር መተካት
        $countSql = "SELECT COUNT(*) 
                     FROM awareness_creation_other a
                     INNER JOIN branches b ON a.branch_id = b.internal_id
                     INNER JOIN branches root ON root.internal_id = :my_branch
                     WHERE b.path LIKE CONCAT(root.path, '%')";
                          
        $countStmt = $this->db->prepare($countSql);
        $countStmt->bindValue(':my_branch', $my_branch);
        $countStmt->execute();
        
        $totalRecords = (int)$countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // 3. ውጤቱን በ Array መልሶ መላክ
        return [
            'data' => $records,
            'total_pages' => $totalPages,
            'current_page' => $page
        ];

    } catch (\PDOException $e) { 
        // 🔒 የደህንነት ማስታወሻ፦ የSQL ስህተቱን ለተጠቃሚ ሳናሳይ በምስጢር ሲስተም ሎግ ላይ እናስቀምጣለን
        error_log("Error fetching awareness records (Optimized Hierarchical): " . $e->getMessage());
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
WHERE b.path LIKE CONCAT(
        (SELECT path FROM branches WHERE internal_id = :branch_id), '%'
      )
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
 *//**
 * 🔍 ስራ ፈላጊዎችን በስም፣ በስልክ፣ በLabor ID ወይም በ Job Seeker ID መፈለጊያ
 *//**
 * 🔍 ስራ ፈላጊዎችን በስም፣ በስልክ፣ በLabor ID ወይም በ Job Seeker ID መፈለጊያ
 * 💡 ማሳሰቢያ፦ SQLSTATE[HY093] ስህተትን ለማስቀረት ለእያንዳንዱ መስመር የተለየ ፓራሜትር ተሰጥቷል
 *//**
 * 🔍 ስራ ፈላጊዎችን በቅርንጫፍ እና ግንዛቤ ባልወሰዱበት (awareness = 0) ሁኔታ በPrepared Statement መፈለጊያ
 */public function searchJobSeekersForAwareness($branch_id, $search, $awareness = '0') {
    try {
        $search = trim($search);
        
        // 💡 `awareness` አምድ በ SELECT ውስጥ ተጨምሯል፤ እንዲሁም ከታች ያለው `AND awareness = 0` ተነስቷል
        $sql = "SELECT `id`, `branch_id`, `job_seeker_id`, `first_name`, `father_name`, `last_name`, `Labor_ID`, `phone_number`, `awareness`
                FROM `job_seekers` 
                WHERE `branch_id` = :branch_id AND `awareness` = :awareness
                  AND (
                       `job_seeker_id` LIKE :search1
                    OR `first_name` LIKE :search2 
                    OR `father_name` LIKE :search3 
                    OR `last_name` LIKE :search4 
                    OR `phone_number` LIKE :search5 
                    OR `Labor_ID` LIKE :search6 
                    OR `g8id` LIKE :search7
                  )
                LIMIT 10";
                
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%" . $search . "%";
        
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt->bindParam(':search1', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search2', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search3', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search4', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search5', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search6', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search7', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':awareness', $awareness, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("Error in searchJobSeekersForAwareness: " . $e->getMessage());
        return [];
    }
}

/**
 * 🔄 የተመረጠውን ሰው awareness ወደ 1 ማዘመሪያ (Update)
 */ public function jobseekersawarenessupdatestatus($job_seeker_id,$awareness = 1) {
     // የስራ ፈላጊውን awareness ወደ 1 ማዘመሪያ
    try {
        $sql = "UPDATE `job_seekers` 
                SET `awareness` = :awareness
                WHERE `id` = :job_seeker_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':awareness', $awareness, PDO::PARAM_STR);
        $stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_STR); // Keep as PDO::PARAM_STR for UUID

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }

        return false;

    } catch (\PDOException $e) {
        error_log("Error in jobseekersawarenessupdatestatus: " . $e->getMessage());
        return false;
    }
}public function getAllJobSeekersByBranch($branch_id, $page = 1, $awareness = '1') {
    try {
        $limit = 20;
        $page = (int)$page > 0 ? (int)$page : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT `id`, `branch_id`, `job_seeker_id`, `first_name`, `father_name`, `last_name`, `phone_number`, `awareness`, `gender`, `age`, `employment_status`, `Labor_ID`, `FAN`
                FROM `job_seekers` 
                WHERE `branch_id` = :branch_id AND `awareness` = :awareness
                ORDER BY `job_seeker_id` DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        // 🔒 በ bindValue እና በ PARAM_STR መተካት (ምክንያቱም enum ስለሆነ)
        $stmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt->bindValue(':awareness', (string)$awareness, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM `job_seekers` WHERE `branch_id` = :branch_id AND `awareness` = :awareness";
        $countStmt = $this->db->prepare($countSql);
        
        $countStmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
        $countStmt->bindValue(':awareness', (string)$awareness, PDO::PARAM_STR);
        
        $countStmt->execute();
        $totalPages = ceil($countStmt->fetchColumn() / $limit);

        return [
            'data' => $records,
            'total_pages' => $totalPages,
            'current_page' => $page
        ];

    } catch (\PDOException $e) {
        error_log("Error fetching job seeker awareness list: " . $e->getMessage());
        return [
            'data' => [],
            'total_pages' => 0,
            'current_page' => 1
        ];
    }
}
public function updateAwarenessToZero($job_seeker_id) {
    try {
        // 🔒 እሴቱን ወደ '0' የመቀየሪያ SQL ጥያቄ
        $sql = "UPDATE `job_seekers` 
                SET `awareness` = :awareness,
                    `updated_at` = CURRENT_TIMESTAMP
                WHERE `id` = :job_seeker_id";
                
        $stmt = $this->db->prepare($sql);
        
        // enum('0','1') ስለሆነ በ PARAM_STR ማሰር እጅግ አስተማማኝ ነው
        $stmt->bindValue(':awareness', '0', PDO::PARAM_STR);
        $stmt->bindValue(':job_seeker_id', $job_seeker_id, PDO::PARAM_STR); 
        
        return $stmt->execute();
        
    } catch (\PDOException $e) {
        error_log("Error updating awareness to zero: " . $e->getMessage());
        return false;
    }
}
}