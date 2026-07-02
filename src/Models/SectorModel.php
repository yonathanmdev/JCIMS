<?php
namespace App\Models;
use App\Helpers\AmharicNormalizer;
use PDO;
class SectorModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


 public function createSector(array $data): bool {
        try {
            $sectorName = $data['sector_name'];
            $normalizedSectorName = AmharicNormalizer::normalizeOnly($sectorName);
            // Start transaction
            $this->db->beginTransaction();
          // Insert employee
            $sql = "INSERT INTO sector_table (
                id, sector, registered_by
            ) VALUES (
                ?, ?, ?
            )";

            $stmt = $this->db->prepare($sql);
            $result1 = $stmt->execute([
                $data['uuid'],
                $normalizedSectorName,
                $data['reg_by']
            ]);

            if (!$result1) {
                throw new \Exception("Failed to insert Sector");
            }


            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Sector registration transaction failed: " . $e->getMessage());
            return false;
        }
    } 

public function getSectors(int $limit = 50, int $offset = 0): array {
    $sql = "SELECT s.id, s.sectorid, s.sector, s.created_at FROM sector_table s
           
            ORDER BY s.created_at DESC
            LIMIT :limit OFFSET :offset";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Get sectors error: " . $e->getMessage());
        return [];
    }
}

public function getSectorById(string $id): ?array
{
    $sql = "SELECT
                id,
                sectorid,
                sector
            FROM sector_table
            WHERE id = :id
            LIMIT 1";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $sector = $stmt->fetch(PDO::FETCH_ASSOC);

        return $sector ?: null;

    } catch (\PDOException $e) {
        error_log("Get sector by ID error: " . $e->getMessage());
        return null;
    }
}

public function createsubSector(array $data): bool {
        try {
            $sectorName = $data['sub_sector'];
            $normalizedSectorName = AmharicNormalizer::normalizeOnly($sectorName);
            // Start transaction
            $this->db->beginTransaction();
          // Insert employee
            $sql = "INSERT INTO sub_sector (
                id, sectorid, subsector, registered_by
            ) VALUES (
                ?, ?, ?, ?
            )";
            $stmt = $this->db->prepare($sql);
            $result1 = $stmt->execute([
                $data['uuid'],
                $data['sectorid'],
                $normalizedSectorName,
                $data['registered_by']
            ]);

            if (!$result1) {
                throw new \Exception("Failed to insert sub sector");
            }


            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Sub sector registration transaction failed: " . $e->getMessage());
            return false;
        }
    } 
public function getSubSectors(int $limit = 50, int $offset = 0): array
{
    $sql = "SELECT
                ss.id,
                ss.sub_sectorid,
                ss.subsector,
                s.sector,
                ss.created_at
            FROM sub_sector ss
            INNER JOIN sector_table s
                ON ss.sectorid = s.sectorid
            ORDER BY ss.created_at DESC
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        error_log('Get sub sectors error: ' . $e->getMessage());
        return [];
    }
}
public function getSubsectorBySectorId(string $sectorId): array
{
    $stmt = $this->db->prepare(
        "SELECT id, subsector FROM sub_sector WHERE sectorid = :sector_id ORDER BY subsector ASC"
    );
    $stmt->execute([':sector_id' => $sectorId]);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
// In SectorModel
public function getSubsectorBigIntIds(string $subsectorUuid): ?array
{
    $stmt = $this->db->prepare(
        "SELECT sub_sectorid, sectorid FROM sub_sector WHERE id = :subsector_uuid"
    );
    $stmt->execute([':subsector_uuid' => $subsectorUuid]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $result ?: null;
}
    }
