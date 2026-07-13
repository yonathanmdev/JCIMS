<?php
namespace App\Models;
use PDO;
class SolgureModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

public function getJobSeekersByHierarchy(string $myBranchId, int $limit, int $offset): array
{
    $sql = "SELECT 
    dr.id, 
    dr.fullname, 
    dr.national_id, 
    dr.sex, 
    dr.age, 
    dr.phone, 
    dr.education_level, 
    dr.educated_study, 
    dr.additional_skill, 
    dr.flagot, 
    dr.sector, 
    dr.branch_id, 
    dr.kebele, 
    dr.yetemezegebebet, 
    dr.created_at, 
    dr.registered_by,
    b.name AS branch_name, 
    b.level AS branch_level
FROM defense_recruitment dr
INNER JOIN branches b ON dr.branch_id = b.internal_id
INNER JOIN branches root ON root.internal_id = :my_branch
WHERE b.path LIKE CONCAT(root.path, '%')
ORDER BY dr.created_at DESC
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

public function countJobSeekersByHierarchy(string $myBranchId): int
{
    $sql = "SELECT COUNT(*) AS total
            FROM defense_recruitment dr
            INNER JOIN branches b ON dr.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    } catch (\PDOException $e) {
        error_log("Count job seekers by hierarchy error: " . $e->getMessage());
        return 0;
    }
}
public function saveCandidate(array $data) {
    $sql = "INSERT INTO defense_recruitment (
                fullname, national_id, sex, age, phone, 
                education_level, educated_study, additional_skill, sector, branch_id, kebele, registered_by
            ) VALUES (
                :fullname, :national_id, :sex, :age, :phone, 
                :education_level, :educated_study, :additional_skill, 
                 :sector, :branch_id, :kebele, :registered_by
            )";

    try {
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':fullname'          => !empty($data['fullname']) ? trim($data['fullname']) : null,
            ':national_id'       => !empty($data['national_id']) ? trim($data['national_id']) : null,
            ':sex'               => !empty($data['sex']) ? trim($data['sex']) : null,
            ':age'               => !empty($data['age']) ? (int)$data['age'] : 0,
            ':phone'             => !empty($data['phone']) ? trim($data['phone']) : null,
            ':education_level'   => !empty($data['education_level']) ? trim($data['education_level']) : null,
            ':educated_study'    => !empty($data['educated_study']) ? trim($data['educated_study']) : null,
            ':additional_skill'  => !empty($data['additional_skill']) ? trim($data['additional_skill']) : null,
            ':sector'            => !empty($data['sector']) ? trim($data['sector']) : null,
            ':branch_id'         => !empty($data['branch_id']) ? (int)$data['branch_id'] : 0, // እዚህ ጋ አስተካክለነዋል
            ':kebele'            => !empty($data['kebele']) ? trim($data['kebele']) : null,
            ':registered_by'     => !empty($data['registered_by']) ? (int)$data['registered_by'] : null
        ]);
    } catch (\PDOException $e) {
        // ለደህንነት ሲባል ስህተቱን በ log ብቻ መያዝ
        error_log("Save candidate error in Model: " . $e->getMessage());
        return false;
    }
}
public function getDefenseRecordById($id) {
    // ደህንነቱ በተጠበቀ የ PDO Prepared Statement የተሰራ
    $sql = "SELECT dr.*, b.name AS branch_name 
            FROM defense_recruitment dr
            LEFT JOIN branches b ON dr.branch_id = b.internal_id 
            WHERE dr.id = :id 
            LIMIT 1";
            
    $stmt = $this->db->prepare($sql); // እንደ ሲስተምህ የዲቢ አያያዝ $this->db ወይም $pdo ሊሆን ይችላል
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function updateRecruitment(array $data): bool {
        $sql = "UPDATE `defense_recruitment` 
                SET `fullname` = ?, `national_id` = ?, `sex` = ?, `age` = ?, `phone` = ?, 
                    `education_level` = ?, `educated_study` = ?, `additional_skill` = ?, 
                    `sector` = ?, `kebele` = ?
                WHERE `id` = ? AND `branch_id` = ?";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['fullname'],
            !empty($data['national_id']) ? $data['national_id'] : null,
            $data['sex'],
            (int)$data['age'],
            $data['phone'],
            $data['education_level'],
            !empty($data['educated_study']) ? $data['educated_study'] : null,
            !empty($data['additional_skill']) ? $data['additional_skill'] : null,
            $data['sector'],
            $data['kebele'],
            (int)$data['id'],
            (int)$data['branch_id'] // ለደህንነት ሲባል ተጠቃሚው የገዛ ቅርንጫፉን ብቻ እንዲያስተካክል
        ]);
    }
}
