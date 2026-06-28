<?php
namespace App\Models;

use PDO;

class Organization {
    private $db;

    /**
     * ኮኔክሽኑን ከውጭ ይቀበላል (Dependency Injection)
     * ይህ በየቦታው አዲስ ኮኔክሽን እንዳይከፈት ይረዳል
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * አዲስ ድርጅት መመዝገቢያ
     * @param string $id በኮንትሮለር የተፈጠረ UUID
     * @param string $name የተመዘገበው የድርጅት ስም
     */
   public function create($id, $orgName, $orgDescription, $registeredBy, $orgAlternateName, $imageName, $postal_code, $phone_number) {
    try {
        $this->db->beginTransaction();

        // 1. ድርጅቱን መመዝገብ (እዚህ ጋር return አታድርግ!)
        $sql = "INSERT INTO organizations (id, name, organization_type) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        // Execute ብቻ አድርግ፣ return አትበል
        $stmt->execute([
            $id,
            $orgName,
            $orgDescription
        ]);
  $orgId = (int) $this->db->lastInsertId();
        // 2. የ Branch ሞዴልን መጥራት
        $branchModel = new \App\Models\Branch($this->db);
        $branchModel->insertBranch([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'org_id' => $orgId,
            'parent_id' => null,
            'name' => $orgName,
            'alt_name' => $orgAlternateName,
            'phone_number' => $phone_number,
            'postal_code' => $postal_code,
            'level' => 1,
            'logo_url' => $imageName,
            'registered_by' => $registeredBy
        ]);

        // 3. አሁን ዳታቤዙ ላይ እንዲጸድቅ commit እናደርጋለን
        $this->db->commit();
        
        return true; // ስራው በሙሉ ሲያልቅ ብቻ return አድርግ

    } catch (\Exception $e) {
        // ስህተት ካለ ወደ ኋላ ይመልሰዋል
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        throw $e;
    }
}
    /**
     * ሁሉንም ድርጅቶች ለዝርዝር ለማምጣት (እንደ ተጨማሪ)
     */
    public function getAll() {
    $sql = "SELECT 
                o.*,
                b.id as branch_id,
                b.organization_id,
                b.alt_name,
                b.phone_number,
                b.postal_code,
                b.logo_url
            FROM organizations o
            LEFT JOIN branches b ON b.organization_id = o.org_id
            WHERE o.status = 'active'
            AND b.level = 1
            ORDER BY o.name ASC";
    
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
     * Find organization by ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM organizations WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
public function updateOrganization($id, $branchId, $name, $description, $orgAlternateName, $imageName, $phone_number, $postal_code) {
    try {
        // Begin transaction — both updates must succeed or both rollback
        $this->db->beginTransaction();

        // 1. Update organization
        $sql = "UPDATE organizations SET name = ?, organization_type = ? WHERE id = ? AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $name,
            $description,
            $id
        ]);

        // 2. Update branch
        $branchModel = new \App\Models\Branch($this->db);
        $branchModel->updateBranch(
            $branchId,
            $name,
            $orgAlternateName,
            $imageName,
            $phone_number,
            $postal_code
        );
        // 3. Commit if both succeeded
        $this->db->commit();
        return true;

    } catch (\PDOException $e) {
        // Rollback both if either failed
        $this->db->rollBack();
        throw $e;
    }
}
   // ============================================================
    // SOFT DELETE
    // ============================================================
    public function softDelete(string $id, string $userId): array
{
    try {
        $this->db->beginTransaction();

        // Snapshot before delete
        $oldRecord = $this->findById($id);
        if (!$oldRecord) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ድርጅቱ አልተገኘም።'];
        }

        // Count affected branches
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM branches 
            WHERE organization_id = ? AND status = 'active' AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        $branchCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Count affected users
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM users 
            WHERE organization_id = ? AND status = 'active'
        ");
        $stmt->execute([$id]);
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // ─── NO BRANCHES → Hard Delete ────────────────────────────────────────
        if ($userCount === 0 && $branchCount === 1) {

            // Hard delete users under this organization (no branches, likely no users either)
            $stmt = $this->db->prepare("
                DELETE FROM branches 
                WHERE organization_id = ?
            ");
            $stmt->execute([$id]);

            // Hard delete the organization itself
            $stmt = $this->db->prepare("
                DELETE FROM organizations 
                WHERE id = ? AND status = 'active' AND deleted_at IS NULL
            ");
            $stmt->execute([$id]);

            $this->db->commit();

            return [
                'status'      => 'success',
                'deleted_type' => 'hard',
                'message'     => 'ድርጅቱ ሙሉ በሙሉ ተሰርዟል።',
                'oldRecord'   => $oldRecord,
                'branchCount' => 0,
                'userCount'   => 0
            ];
        }

        // ─── HAS BRANCHES → Soft Delete + Cascade ─────────────────────────────

        // Soft delete organization
        $stmt = $this->db->prepare("
            UPDATE organizations 
            SET deleted_at = NOW(), status = 'inactive', deleted_by = ?
            WHERE id = ? AND status = 'active' AND deleted_at IS NULL
        ");
        $stmt->execute([$userId, $id]);

        // Cascade soft delete branches
        $stmt = $this->db->prepare("
            UPDATE branches 
            SET deleted_at = NOW(),
                status = 'inactive',
                deleted_by = ?,
                deletion_source = 'TENANT_CASCADE'
            WHERE organization_id = ? AND status = 'active' AND deleted_at IS NULL
        ");
        $stmt->execute([$userId, $id]);

        // Cascade soft delete users
        $stmt = $this->db->prepare("
            UPDATE users 
            SET status = 'inactive',
                deleted_at = NOW(),
                deleted_by = ?,
                deletion_source = 'TENANT_CASCADE'
            WHERE organization_id = ? AND status = 'active'
        ");
        $stmt->execute([$userId, $id]);

        $this->db->commit();

        return [
            'status'       => 'success',
            'deleted_type' => 'soft',
            'message'      => 'ድርጅቱ ተሰርዟል።',
            'oldRecord'    => $oldRecord,
            'branchCount'  => $branchCount,
            'userCount'    => $userCount
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('OrganizationModel::softDelete - ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።'];
    }
}


// app/Models/OrganizationModel.php

// ============================================================
// GET ALL SOFT DELETED
// ============================================================
public function findAllDeleted(): array
{
    $stmt = $this->db->prepare("
        SELECT 
            o.id,
            o.name,
            o.deleted_at,
            o.status,
            CONCAT(u.first_name, ' ', u.father_name) AS deleted_by_name,
            (
                SELECT COUNT(*) FROM branches b 
                WHERE b.organization_id = o.id 
                AND b.deletion_source = 'TENANT_CASCADE'
            ) AS branch_count,
            (
                SELECT COUNT(*) FROM users u2 
                WHERE u2.organization_id = o.id 
                AND u2.deletion_source = 'TENANT_CASCADE'
            ) AS user_count,
            DATEDIFF(DATE_ADD(o.deleted_at, INTERVAL 90 DAY), NOW()) AS days_until_purge

        FROM organizations o
        LEFT JOIN users u ON u.id = o.deleted_by

        WHERE o.status = 'inactive' 
          AND o.deleted_at IS NOT NULL

        ORDER BY o.deleted_at DESC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function restore(string $id, string $userId): array
{
    try {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare("
            SELECT * FROM organizations 
            WHERE id = ? AND deleted_at IS NOT NULL AND status = 'inactive'
        ");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ድርጅቱ አልተገኘም ወይም አልተሰረዘም።'];
        }

        // Count branches to restore
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM branches 
            WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'
        ");
        $stmt->execute([$id]);
        $branchCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Count users to restore
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM users 
            WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'
        ");
        $stmt->execute([$id]);
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Restore organization
        $stmt = $this->db->prepare("
            UPDATE organizations 
            SET deleted_at = NULL, status = 'active', deleted_by = NULL
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        // Restore cascade-deleted branches only
        $stmt = $this->db->prepare("
            UPDATE branches 
            SET deleted_at = NULL,
                status = 'active',
                deletion_source = NULL,
                deleted_by = NULL
            WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'
        ");
        $stmt->execute([$id]);

        // ✅ Restore cascade-inactivated users only
        $stmt = $this->db->prepare("
            UPDATE users 
            SET deleted_at = NULL,
                status = 'active',
                deletion_source = NULL,
                deleted_by = NULL
            WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'
        ");
        $stmt->execute([$id]);

        $this->db->commit();

        return [
            'status'           => 'success',
            'message'          => 'ድርጅቱ ተመልሷል።',
            'oldRecord'        => $record,
            'restoredBranches' => $branchCount,
            'restoredUsers'    => $userCount    // ← for audit log
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('OrganizationModel::restore - ' . $e->getMessage());
        return ['status' => 'error', 'message' =>  'መመለስ አልተቻለም።'];
    }
} 
// ============================================================
// PURGE (hard delete)
// ============================================================
public function purge(string $id, string $archiveId, string $entityType = 'organization'): array
{
    try {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare("SELECT * FROM organizations WHERE id = ?");
        $stmt->execute([$id]);
        $oldRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$oldRecord) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ድርጅቱ አልተገኘም።'];
        }

        $stmt = $this->db->prepare("
            SELECT * FROM branches 
            WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'
        ");
        $stmt->execute([$id]);
        $branchData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'
        ");
        $stmt->execute([$id]);
        $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ✅ Single archive table — not separate per entity
        $stmt = $this->db->prepare("
            INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_at, archived_by, reason)
            VALUES (?, ?, ?, ?, NOW(), ?, 'PURGE')
        ");
        $stmt->execute([
            $archiveId,
            $entityType,
            $id,
            json_encode([
                'organization' => $oldRecord,
                'branches'     => $branchData,
                'users'        => $userData
            ]),
            $_SESSION['user']['id']
        ]);

        

        $this->db->prepare("DELETE FROM users WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'")->execute([$id]);
        $this->db->prepare("DELETE FROM branches WHERE organization_id = ? AND deletion_source = 'TENANT_CASCADE'")->execute([$id]);
        $this->db->prepare("DELETE FROM organizations WHERE id = ?")->execute([$id]);

        $this->db->commit();

        return [
            'status'      => 'success',
            'message'     => 'ድርጅቱ ተመዝግቦ ተሰርዟል።',
            'oldRecord'   => $oldRecord,
            'archiveId'   => $archiveId,
            'branchCount' => count($branchData),
            'userCount'   => count($userData)
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
       // error_log('OrganizationModel::purge - ' . $e->getMessage());
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
// ============================================================
// GET ALL ARCHIVED (for admin UI list)
// ============================================================
public function findAllArchived(): array
{
    $stmt = $this->db->prepare("
        SELECT 
            a.id            as archive_id,
            a.original_id,
            a.archived_at,
            a.restored_at,
            a.reason,
            a.archived_by,
            CONCAT(u.first_name, ' ', u.father_name) as archived_by_name,
            JSON_UNQUOTE(JSON_EXTRACT(a.snapshot, '$.organization.name')) as org_name,
            JSON_LENGTH(JSON_EXTRACT(a.snapshot, '$.branches'))           as branch_count,
            JSON_LENGTH(JSON_EXTRACT(a.snapshot, '$.users'))              as user_count
        FROM data_archive a
        LEFT JOIN users u ON u.id = a.archived_by
        WHERE a.entity_type = 'organization'
        ORDER BY a.archived_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// RESTORE FROM ARCHIVE
// ============================================================
public function restoreFromArchive(string $originalId, string $restoredBy): array
{
    try {
        $this->db->beginTransaction();

        // 1. Get archive snapshot
        $stmt = $this->db->prepare("
            SELECT * FROM data_archive 
            WHERE original_id = ? AND entity_type = 'organization'
            ORDER BY archived_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$originalId]);
        $archive = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$archive) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'Archive አልተገኘም።'];
        }

        $snapshot = json_decode($archive['snapshot'], true);
        $org      = $snapshot['organization'];
        $branches = $snapshot['branches'];
        $users    = $snapshot['users'];

        // 2. Re-insert organization
        // ============================================================
// 2. Re-insert organization — your exact columns
// ============================================================
$stmt = $this->db->prepare("
    INSERT INTO organizations (
        id,
        name,
        organization_type,
        status,
        created_at,
        updated_at,
        deleted_at
    ) VALUES (
        ?, ?, ?,
        'active',
        ?, NOW(), NULL
    )
    ON DUPLICATE KEY UPDATE
        status     = 'active',
        deleted_at = NULL,
        updated_at = NOW()
");
$stmt->execute([
    $org['id'],                // id
    $org['name'],              // name
    $org['organization_type'], // organization_type
    $org['created_at'],        // created_at
]);

        // 3. Re-insert branches
        // ============================================================
// 3. Re-insert branches — your exact columns
// ============================================================
foreach ($branches as $branch) {
    $stmt = $this->db->prepare("
        INSERT INTO branches (
            id,
            organization_id,
            parent_id,
            name,
            level,
            status,
            registered_by,
            created_at,
            updated_at,
            deleted_at,
            deletion_source
        ) VALUES (
            ?, ?, ?, ?, ?,
            'active',
            ?, ?, NOW(), NULL, NULL
        )
        ON DUPLICATE KEY UPDATE
            status          = 'active',
            deleted_at      = NULL,
            deletion_source = NULL,
            updated_at      = NOW()
    ");
    $stmt->execute([
        $branch['id'],               // id
        $branch['organization_id'],  // organization_id
        $branch['parent_id'],        // parent_id
        $branch['name'],             // name
        $branch['level'],            // level
        $branch['registered_by'],    // registered_by
        $branch['created_at'],       // created_at
    ]);
}

    // ============================================================
        // 4. Re-insert users — your exact columns
        // ============================================================
        foreach ($users as $user) {
            $stmt = $this->db->prepare("
                INSERT INTO users (
                    id,
                    organization_id,
                    branch_id,
                    first_name,
                    father_name,
                    grand_father_name,
                    phone,
                    email,
                    password,
                    role,
                    status,
                    registered_by,
                    created_at,
                    updated_at,
                    deleted_at,
                    deletion_source
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                    'active',
                    ?, ?, NOW(), NULL, NULL
                )
                ON DUPLICATE KEY UPDATE
                    status          = 'active',
                    deleted_at      = NULL,
                    deletion_source = NULL,
                    updated_at      = NOW()
            ");
            $stmt->execute([
                $user['id'],                 // id
                $user['organization_id'],    // organization_id
                $user['branch_id'],          // branch_id
                $user['first_name'],         // first_name
                $user['father_name'],        // father_name
                $user['grand_father_name'],  // grand_father_name
                $user['phone'],              // phone
                $user['email'],              // email
                $user['password'],           // password — restored as-is (already hashed)
                $user['role'],               // role
                $user['registered_by'],      // registered_by
                $user['created_at'],         // created_at
            ]);
        }
        // 5. Mark archive as restored so it's not restored twice
        $stmt = $this->db->prepare("
            UPDATE data_archive 
            SET restored_at = NOW(), restored_by = ?
            WHERE id = ?
        ");
        $stmt->execute([$restoredBy, $archive['id']]);

        $this->db->commit();

        return [
            'status'       => 'success',
            'message'      => 'ድርጅቱ ከ archive ተመልሷል።',
            'archiveId'    => $archive['id'],
            'orgName'      => $org['name'],
            'branchCount'  => count($branches),
            'userCount'    => count($users)
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('restoreFromArchive - ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'መልስ አልተቻለም።'];
    }
}
}