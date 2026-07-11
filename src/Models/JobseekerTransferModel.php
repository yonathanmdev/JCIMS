<?php
namespace App\Models;
use PDO;
class JobseekerTransferModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
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
    /**
     * የዝውውር ውሳኔን መዝግቦ የሥራ ፈላጊውን ቅርንጫፍ መለያ ማሻሻያ (Transaction)
     */
    public function updateTransferDecision($transferLogId, $status, $userId, $currentBranchId) {
        try {
            // የዳታቤዝ ትራንዛክሽን መጀመር (ደህንነትን አስተማማኝ ለማድረግ)
            $this->db->beginTransaction();

            // 1. መጀመሪያ የዝውውር መዝገቡን (Transfer Log) ማምጣት (የሥራ ፈላጊውን ID ለማወቅ)
            $stmtFetch = $this->db->prepare("
                SELECT `job_seeker_id`, `recver_branch_id`, `transfer_status` 
                FROM `transfer_logs` 
                WHERE `id` = :id 
                LIMIT 1
            ");
            $stmtFetch->execute(['id' => $transferLogId]);
            $transfer = $stmtFetch->fetch(PDO::FETCH_ASSOC);

            if (!$transfer) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'የዝውውር መረጃው አልተገኘም።'];
            }

            // ዝውውሩ ቀድሞውኑ ውሳኔ ካገኘ በድጋሚ እንዳይቀየር መከላከል
            if ((int)$transfer['transfer_status'] !== 0) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'ይህ ዝውውር ቀድሞውኑ ውሳኔ አግኝቷል።'];
            }

            // 2. የዝውውር ታሪኩን ማሻሻል (Status, Acceptor ID, Date)
            $sqlLog = "UPDATE `transfer_logs` 
                       SET `transfer_status` = :status,
                           `acceptorrejectby` = :user_id,
                           `accentrejectdate` = NOW(),
                           `reciver_view_status` = 1
                       WHERE `id` = :id";
            
            $stmtLog = $this->db->prepare($sqlLog);
            $stmtLog->execute([
                'status'  => (int)$status,
                'user_id' => (int)$userId,
                'id'      => $transferLogId
            ]);

            // 3. ውሳኔው "የጸደቀ" (1) ከሆነ ብቻ የሥራ ፈላጊውን branch_id ማሻሻል
            if ((int)$status === 1) {
                $sqlSeeker = "UPDATE `job_seekers` 
                              SET `branch_id` = :new_branch_id 
                              WHERE `job_seeker_id` = :job_seeker_id"; // ማስታወሻ፦ ከ getTransferTrackingList ጋር ለማጣጣም job_seeker_id ተጠቅሟል
                
                $stmtSeeker = $this->db->prepare($sqlSeeker);
                $stmtSeeker->execute([
                    'new_branch_id' => (int)$currentBranchId,
                    'job_seeker_id' => $transfer['job_seeker_id']
                ]);
            }

            // ሁሉም በተሳካ ሁኔታ ከተከናወነ ማጽደቅ
            $this->db->commit();
            return ['success' => true, 'message' => 'ውሳኔው በተሳካ ሁኔታ ተመዝግቧል።'];

        } catch (\PDOException $e) {
            // ስህተት ካለ ወደ ነበረበት መመለስ (Rollback)
            $this->db->rollBack();
            throw new \Exception("የውሳኔ ምዝገባ ስህተት፦ " . $e->getMessage());
        }
    }
    }

