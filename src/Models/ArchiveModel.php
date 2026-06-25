<?php
namespace App\Models;
use PDO;

class ArchiveModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($archiveData) {
        // 7 columns = 7 placeholders
        $sql = "INSERT INTO employee_documents 
                    (id, emp_id, owner_type, owner_id, entity_type, file_url, registered_by) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?)";

        try {
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $archiveData['id'],
                $archiveData['employee_id'],
                $archiveData['owner_type'],
                $archiveData['owner_id'],
                $archiveData['cert_type'],
                $archiveData['certificate_file'],
                $archiveData['registered_by']
            ]);

        } catch (\PDOException $e) {
            throw $e;
        }
    }
public function update($editData) {
    try {
        // Build query dynamically — only update file_url if a new file was uploaded
        if (!empty($editData['file_url'])) {
            // Fetch old file url before overwriting (for cleanup in controller)
            $stmt = $this->db->prepare("SELECT file_url FROM employee_documents WHERE id = ?");
            $stmt->execute([$editData['id']]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);
            $editData['old_file_url'] = $existing['file_url'] ?? null;

            $sql = "UPDATE employee_documents 
                    SET entity_type = ?, file_url = ?, updated_by = ?
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $editData['cert_type'],
                $editData['file_url'],
                $editData['updated_by'],
                $editData['id'],
            ]);
        } else {
            // No new file — only update the type
            $sql = "UPDATE employee_documents 
                    SET entity_type = ?, updated_by = ?
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $editData['cert_type'],
                $editData['updated_by'],
                $editData['id'],
            ]);
        }
    } catch (\PDOException $e) {
        throw $e;
    }
}

// Define discipline values as a constant for easy reference
private const DISCIPLINE_VALUES = [
    'የጽሁፍ ማስጠንቀቂያ የተሰጣቸው',
    'እስከ 15 ቀን የሚደርስ የደመወዝ ቅጣት የተቀጡ',
    'እስከ 3 ወር የሚደርስ የደመወዝ ቅጣት የተቀጡ',
    'እስከ 2 ዓመት ለሚደርስ ጊዜ ከደረጃና ከደመወዝ ዝቅ የተደረጉ',
];

public function updateDisciplineIfNeeded(string $employeeUuid, string $certType): bool {
    if (!in_array($certType, self::DISCIPLINE_VALUES)) {
        return false; // Not a discipline type, skip
    }

    $sql = "UPDATE employees_table SET displin_situation = ? WHERE uuid = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$certType, $employeeUuid]);
}
public function findById(string $id) {
    $sql = "SELECT * FROM employee_documents WHERE id = ? ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function deleteRecord(string $id, string $employeeId, string $userId, string $reason, string $deletionSource): array
{
    try {
        $this->db->beginTransaction();

        $oldRecord = $this->findById($id);
        if (!$oldRecord) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'አልተገኘም።'];
        }

        // ✅ Get employee UUID from oldRecord
        $empId = $oldRecord['emp_id'] ?? null; // ← adjust key to match your column name

        if (!$empId) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'የሰራተኛ መለያ አልተገኘም።'];
        }


        // Delete attached documents
        $stmt = $this->db->prepare(
            "UPDATE employee_documents SET
                is_deleted      = 1
             WHERE id   = ?
             AND   is_deleted = 0"
        );
        $stmt->execute([$id]);
        $deletedDocumentCount = $stmt->rowCount();

        // ✅ Update employee status back to Active
        $stmt = $this->db->prepare(
            "UPDATE employees_table SET
                displin_situation      = 'ምንም የቅጣት ሪኮርድ የሌለባቸው',
                updated_by  = ?
             WHERE uuid        = ?"
        );
        $stmt->execute([$userId, $empId]);

        $this->db->commit();

        return [
            'status'               => 'success',
            'deleted_type'         => 'soft',
            'message'              => 'በትክክል ተሰርዟል።',
            'oldRecord'            => $oldRecord,
            'deletedDocumentCount' => $deletedDocumentCount,
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('ScholarshipModel::deleteRecord - ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።'];
    }
}
}