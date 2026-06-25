<?php

namespace App\Models;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

/**
 * AuditLog — plain-PHP PDO model (no Laravel/Eloquent).
 *
 * Responsibilities:
 *   - Insert a single log row          → log()
 *   - Paginated filtered query         → getLogs()
 *   - Summary counts for stat cards    → getStats()
 *   - Distinct values for dropdowns    → getFilterOptions()
 *   - Single row by ID                 → findById()
 */
class AuditLog
{
    private \PDO   $db;
    private Logger $logger;

    public function __construct(\PDO $db)
    {
        $this->db = $db;

        // Keep Monolog exactly as before
        $this->logger = new Logger('audit');
        $this->logger->pushHandler(
            new StreamHandler(
                __DIR__ . '/../../storage/logs/audit.log',
                Level::Info
            )
        );
    }

    // =========================================================================
    //  WRITE
    // =========================================================================

    /**
     * Insert one audit-log row.
     *
     * Signature is identical to your original AuditLog->log() —
     * every existing call site continues to work without changes.
     */
    public function log(
        ?string $userId,
        string  $action,
        string  $entityType,
        ?string $entityId  = null,
        mixed   $oldValues = null,
        mixed   $newValues = null,
        array   $metadata  = []
    ): bool {
        try {
            $ipAddress = $_SERVER['REMOTE_ADDR']     ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $sql = "INSERT INTO audit_logs
                        (user_id, action, entity_type, entity_id,
                         old_values, new_values, ip_address, user_agent, metadata)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $ok   = $stmt->execute([
                $userId,
                $action,
                $entityType,
                $entityId,
                $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                $ipAddress,
                $userAgent,
                $metadata  !== []   ? json_encode($metadata,  JSON_UNESCAPED_UNICODE) : null,
            ]);

            // Mirror to Monolog file — same as before
            $logData = [
                'user_id'     => $userId,
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'ip_address'  => $ipAddress,
                'timestamp'   => date('Y-m-d H:i:s'),
            ];
            if ($oldValues !== null) $logData['old_values'] = $oldValues;
            if ($newValues !== null) $logData['new_values'] = $newValues;
            if ($metadata  !== [])  $logData['metadata']   = $metadata;

            $this->logger->info($action, $logData);

            return $ok;

        } catch (\Exception $e) {
            // Never crash the main request — same guard as before
            error_log("Audit log error: " . $e->getMessage() .
                      "\nContext: " . json_encode(compact('userId', 'action', 'entityType', 'entityId')));
            return false;
        }
    }

    // =========================================================================
    //  READ — paginated list
    // =========================================================================

    /**
     * Paginated rows with optional filters.
     *
     * Recognised $filters keys (all optional):
     *   user_id, action, entity_type, entity_id,
     *   date_from  (Y-m-d H:i:s),
     *   date_to    (Y-m-d H:i:s),
     *   search     (matched against action, entity_type, entity_id, ip_address)
     *
     * Returns ['data' => [...rows...], 'total' => int]
     */
    public function getLogs(array $filters = [], int $limit = 25, int $offset = 0): array
    {
        [$where, $params] = $this->buildWhere($filters);

        // Total row count for pagination
        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id $where"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Data rows
        $stmt = $this->db->prepare(
            "SELECT
                 al.uuid                    AS id,
                 al.user_id,
                 CONCAT(u.first_name,' ', u.father_name) AS user_name,
                 u.email                    AS user_email,
                 al.action,
                 al.entity_type,
                 al.entity_id,
                 al.old_values,
                 al.new_values,
                 al.ip_address,
                 al.user_agent,
                 al.metadata,
                 al.created_at
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             $where
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([...$params, $limit, $offset]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['old_values']  = $this->decodeJson($row['old_values']);
            $row['new_values']  = $this->decodeJson($row['new_values']);
            $row['metadata']    = $this->decodeJson($row['metadata']);
            $row['has_changes'] = $row['old_values'] !== null || $row['new_values'] !== null;
        }
        unset($row);

        return ['data' => $rows, 'total' => $total];
    }

    // =========================================================================
    //  READ — single row (for detail panel)
    // =========================================================================

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT al.*,
                    CONCAT(u.first_name, ' ', u.father_name) AS user_name,
                    u.email AS user_email
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             WHERE al.uuid = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) return null;

        $row['old_values']  = $this->decodeJson($row['old_values']);
        $row['new_values']  = $this->decodeJson($row['new_values']);
        $row['metadata']    = $this->decodeJson($row['metadata']);
        $row['has_changes'] = $row['old_values'] !== null || $row['new_values'] !== null;

        return $row;
    }

    // =========================================================================
    //  READ — summary stats (4 stat cards)
    // =========================================================================

    /**
     * Matches the existing AuditHelper::getStats($dateFrom) signature.
     */
    public function getStats(?string $dateFrom = null): array
    {
        $where  = $dateFrom ? 'WHERE created_at >= ?' : '';
        $params = $dateFrom ? [$dateFrom] : [];

        $stmt = $this->db->prepare(
            "SELECT
                 COUNT(*)                                                  AS total,
                 SUM(action = 'login_success')                             AS logins,
                 SUM(old_values IS NOT NULL OR new_values IS NOT NULL)     AS changes,
                 SUM(action LIKE '%failed%' OR action LIKE '%error%')      AS failures
             FROM audit_logs $where"
        );
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return [
            'total'    => (int) ($row['total']    ?? 0),
            'logins'   => (int) ($row['logins']   ?? 0),
            'changes'  => (int) ($row['changes']  ?? 0),
            'failures' => (int) ($row['failures'] ?? 0),
        ];
    }

    // =========================================================================
    //  READ — dropdown values
    // =========================================================================

    public function getFilterOptions(): array
    {
        $actions = $this->db
            ->query("SELECT DISTINCT action FROM audit_logs ORDER BY action")
            ->fetchAll(\PDO::FETCH_COLUMN);

        $entityTypes = $this->db
            ->query("SELECT DISTINCT entity_type FROM audit_logs
                     WHERE entity_type IS NOT NULL ORDER BY entity_type")
            ->fetchAll(\PDO::FETCH_COLUMN);

        return ['actions' => $actions, 'entity_types' => $entityTypes];
    }

    // =========================================================================
    //  PRIVATE helpers
    // =========================================================================

    private function buildWhere(array $filters): array
    {
        $clauses = [];
        $params  = [];

        if (!empty($filters['user_id'])) {
            $clauses[] = 'al.user_id = ?';
            $params[]  = $filters['user_id'];
        }
        if (!empty($filters['action'])) {
            $clauses[] = 'al.action = ?';
            $params[]  = $filters['action'];
        }
        if (!empty($filters['entity_type'])) {
            $clauses[] = 'al.entity_type = ?';
            $params[]  = $filters['entity_type'];
        }
        if (!empty($filters['entity_id'])) {
            $clauses[] = 'al.entity_id = ?';
            $params[]  = $filters['entity_id'];
        }
        if (!empty($filters['date_from'])) {
            $clauses[] = 'al.created_at >= ?';
            $params[]  = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $clauses[] = 'al.created_at <= ?';
            $params[]  = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $like      = '%' . $filters['search'] . '%';
            $clauses[] = '(al.action LIKE ? OR al.entity_type LIKE ? OR al.entity_id LIKE ? OR al.ip_address LIKE ?)';
            array_push($params, $like, $like, $like, $like);
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }

    private function decodeJson(?string $value): mixed
    {
        if ($value === null || $value === '') return null;
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
}