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
        $sql = "INSERT INTO job_seekers (id, branch_id, first_name, father_name, last_name, gender, 
        age, educational_level, educated_dpt, education_trmnet_finsh_year, g8id, CGPA, school_type, 
        physical_condition, physical_condition_desc, mender, kebele_id_no, phone_number, 
        choice_sector1, sub_choose1, choice_sector2, sub_choose2, choice_sector3, sub_choose3, 
        meteleya_huneta, residence_status, srafelagi_huneta, graguation_catagory, maritalstatus, 
       haveexp, workplace, experience, profession, nameofcountry, language,
         wageorself, regstration_level, mothername, Labor_ID, 
         FAN, kebele_id_photo, jsphoto, fiscal_year, agri_business_experience, has_dependents, 
         number_of_dependents, children_under_five, full_name_normalized, registered_by) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $result1 = $stmt->execute([
            $data['uuid'],
            $data['branch_id'],
            $data['first_name'],
            $data['father_name'],
            $data['last_name'],
            $data['gender'],
            $data['age'],
            $data['educational_level'],
            $data['educated_dpt'],
            $data['education_trmnet_finsh_year'],
            $data['g8id'],
            $data['CGPA'],
            $data['school_type'],
            $data['physical_condition'],
            $data['physical_condition_desc'],
            $data['mender'],
            $data['kebele_id_no'],
            $data['phone_number'],
            $data['choice_sector1'],
            $data['sub_choose1'],
            $data['choice_sector2'],
            $data['sub_choose2'],
            $data['choice_sector3'],
            $data['sub_choose3'],
            $data['meteleya_huneta'],
            $data['residence_status'],
            $data['srafelagi_huneta'],
            $data['graguation_catagory'],
            $data['maritalstatus'],
            $data['haveexp'],
            $data['workplace'],
            $data['experience'],
            $data['profession'],
            $data['nameofcountry'],
            $data['language'],
            $data['wageorself'],
            $data['regstration_level'],
            $data['mothername'],
            $data['Labor_ID'],
            $data['FAN'],
            $data['kebele_id_photo'],
            $data['jsphoto'],
            $data['fiscal_year'],
            $data['agri_business_experience'],
            $data['has_dependents'],
            $data['number_of_dependents'],
            $data['children_under_five'],
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
public function getLast24HoursCount(string $myBranchId, string $userId, int $limit = 50, int $offset = 0): array {
   $sql = "SELECT
            js.id,
            js.first_name,
            js.father_name,
            js.last_name,
            js.gender,
            COUNT(*) OVER() AS total_job_seekers
        FROM job_seekers js
        WHERE js.branch_id = :branch_id
          AND js.registered_by = :user_id
        ORDER BY js.created_at DESC
        LIMIT :limit OFFSET :offset";

try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':branch_id', $myBranchId);
    $stmt->bindValue(':user_id', $userId);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    error_log("Get job seekers by hierarchy error: " . $e->getMessage());
    return [];
}
}

public function getJobSeekersByHierarchy(string $myBranchId, int $limit = 50, int $offset = 0): array
{
    $sql = "SELECT js.id,js.job_seeker_id, js.first_name, js.father_name, js.last_name, js.gender, 
                   b.name AS branch_name, b.level AS branch_level
            FROM job_seekers js
            INNER JOIN branches b ON js.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
            ORDER BY js.created_at DESC
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $this->db->prepare($sql);
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
// ለAJAX ተዋረድ ቅርንጫፎችን ማምጫ for transfer 
    public function getBranchesByLevel($level, $parentId = null) {
       // src/Models/JobSeekerModel.php

if ($level === 1) {
    $sql = "SELECT `internal_id`, `name`, `ketema_astedader` 
            FROM `branches` 
            WHERE `level` = 1 
              AND `status` = 'active' 
              AND `is_deleted` = 0 
            ORDER BY `name` ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
} else {
    $sql = "SELECT `internal_id`, `name`, `ketema_astedader` 
            FROM `branches` 
            WHERE `level` = :level 
              AND `parent_id` = :parent_id 
              AND `status` = 'active' 
              AND `is_deleted` = 0 
            ORDER BY `name` ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'level' => (int)$level,
        'parent_id' => $parentId
    ]);
}
return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // የሥራ ፈላጊውን መኖር ማረጋገጫ
    public function findJobSeekerById($id) {
        $stmt = $this->db->prepare("SELECT id, branch_id FROM job_seekers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // የዝውውር ሂደቱን በTransaction ማከናወኛ
public function insertTransferLog($data) {
        try {
            // 💡 የ Foreign Key እና የ Strict Mode ስህተቶችን ሙሉ በሙሉ የሚከላከል የተስተካከለ SQL
            $sql = "INSERT INTO `transfer_logs` (
                        `id`, 
                        `job_seeker_id`, 
                        `sender_branch_id`, 
                        `recver_branch_id`, 
                        `transfer_status`, 
                        `transferby`, 
                        `transferdate`, 
                        `acceptorrejectby`, 
                        `accentrejectdate`, 
                        `reciver_view_status`
                    ) VALUES (
                        :id, 
                        :job_seeker_id, 
                        :sender_branch_id, 
                        :recver_branch_id, 
                        :transfer_status, 
                        :transferby, 
                        NOW(), 
                        :acceptorrejectby,  -- 🔐 0 የነበረው አሁን በፕሌስሆልደር ተተክቷል (NULL እንዲገባ)
                        NULL, 
                        0
                    )";
                    
            $stmt = $this->db->prepare($sql);
            
            // ሁሉንም ዳታዎች በደህንነት ባይንድ አድርጎ ማስፈጸም
            return $stmt->execute([
                'id'                => $data['id'],
                'job_seeker_id'     => $data['job_seeker_id'], 
                'sender_branch_id'  => (int)$data['sender_branch_id'],
                'recver_branch_id'  => (int)$data['recver_branch_id'],
                'transfer_status'   => $data['transfer_status'] ?? '0', // 'pending' እሴትን ኮንትሮለሩ ላይ ከሰጡት ይወስዳል
                'transferby'        => (int)$data['transferby'],
                'acceptorrejectby'  => $data['acceptorrejectby'] // 🔐 እዚህ ጋር NULL ይተላለፋል፤ የ የውጭ ቁልፍ (Foreign Key) ስህተቱን ይፈታል
            ]);

        } catch (\PDOException $e) {
            // ትክክለኛውን የ MySQL ስህተት ፈልቅቆ አውጥቶ ለማሳየት
            throw new \Exception("የዳታቤዝ ስህተት ዝርዝር፦ " . $e->getMessage());
        }
    }
    public function checkDuplicateTransfer($job_seeker_id, $recver_branch_id) {
        $sql = "SELECT COUNT(*) FROM `transfer_logs` 
                WHERE `job_seeker_id` = :job_seeker_id 
                AND `recver_branch_id` = :recver_branch_id 
                AND `transfer_status` = 0"; // 0 ማለት በመጠባበቅ ላይ (Pending) መሆኑን ከምስሉ አይተናል
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_seeker_id'    => $job_seeker_id,
            'recver_branch_id' => $recver_branch_id
        ]);
        
        return $stmt->fetchColumn() > 0; // ካለ true ይመልሳል
    }
   public function getTransferTrackingList($branch_id = null) {
        try {
            // የቅርንጫፍ መለያው ከጠፋ ወይም ባዶ ከሆነ ለደህንነት ሲባል ምንም ዳታ አታምጣ (ባዶ ድርድር መልስ)
            if ($branch_id === null || (int)$branch_id === 0) {
                return [];
            }

            $valid_branch_id = (int)$branch_id;

            // 🔐 SQL ጥንቃቄ፦ ሎግኢን ያደረገው ቅርንጫፍ ላኪ (sender) ወይም ተቀባይ (recver) የሆነበትን ብቻ ማምጫ ማጣሪያ (WHERE)
            $sql = "SELECT 
                        tl.id,
                        tl.transferdate,
                        tl.transfer_status,
                        tl.reciver_view_status,
                        tl.sender_branch_id,
                        tl.recver_branch_id,
                        js.first_name,
                        js.father_name,
                        js.last_name,
                        js.phone_number AS job_seeker_phone,
                        sb.name AS sender_branch_name,
                        rb.name AS receiver_branch_name,
                        u.username AS sender_user_name
                    FROM `transfer_logs` tl
                    JOIN `job_seekers` js ON tl.job_seeker_id = js.job_seeker_id
                    JOIN `branches` sb ON tl.sender_branch_id = sb.internal_id
                    JOIN `branches` rb ON tl.recver_branch_id = rb.internal_id
                    JOIN `users` u ON tl.transferby = u.user_id
                    WHERE tl.sender_branch_id = :sender_branch OR tl.recver_branch_id = :receiver_branch
                    ORDER BY tl.transferdate DESC";
            
            $stmt = $this->db->prepare($sql);
            
            // ሁለቱንም ፕሌስሆልደሮች በሎግኢን ቅርንጫፍ መለያ ማሰር
            $stmt->execute([
                'sender_branch'   => $valid_branch_id,
                'receiver_branch' => $valid_branch_id
            ]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("የመከታተያ ዳታ ማምጣት አልተቻለም፦ " . $e->getMessage());
        }
    }
    }

