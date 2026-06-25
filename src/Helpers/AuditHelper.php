<?php
namespace App\Helpers;

use App\Models\AuditLog;

/**
 * AuditHelper — thin static facade over AuditLog model.
 *
 * Keeps the existing call signatures used throughout your codebase:
 *
 *   AuditHelper::log($action, $entityType, $entityId, $oldValues, $newValues, $metadata)
 *   AuditHelper::getLogs($filters, $limit, $offset)
 *   AuditHelper::getStats($dateFrom)
 *
 * The DB connection is set once at bootstrap via AuditHelper::init($db).
 */
class AuditHelper
{
    private static ?\PDO      $db    = null;
    private static ?AuditLog  $model = null;

    /**
     * Call this once in your bootstrap / DI setup.
     *
     * Example in index.php:
     *   AuditHelper::init($db);
     */
    public static function init(\PDO $db): void
    {
        self::$db    = $db;
        self::$model = new AuditLog($db);
    }

    // =========================================================================
    //  WRITE — matches existing call signature in your codebase
    // =========================================================================

    /**
     * Named-parameter version (new pattern — used in AuditLogController):
     *
     *   AuditHelper::log(
     *       action:     'debt_suspension_deleted',
     *       entityType: 'debt_suspension',
     *       entityId:   $id,
     *       oldValues:  $row,
     *   );
     *
     * Positional version (old pattern — existing call sites keep working):
     *
     *   AuditHelper::log('login_success', 'auth', $userId)
     */
    public static function log(
        string  $action,
        string  $entityType,
        ?string $entityId  = null,
        mixed   $oldValues = null,
        mixed   $newValues = null,
        array   $metadata  = []
    ): bool {
        $userId = $_SESSION['user']['id'] ?? null;   // adjust to your session key
        return self::model()->log($userId, $action, $entityType, $entityId, $oldValues, $newValues, $metadata);
    }

    /**
     * Convenience for actions that already know the user ID
     * (e.g. login events, where the session may not be set yet).
     */
    public static function logAs(
        ?string $userId,
        string  $action,
        string  $entityType,
        ?string $entityId  = null,
        mixed   $oldValues = null,
        mixed   $newValues = null,
        array   $metadata  = []
    ): bool {
        return self::model()->log($userId, $action, $entityType, $entityId, $oldValues, $newValues, $metadata);
    }

    // =========================================================================
    //  READ — matches existing AuditHelper::getLogs() used in AuditController
    // =========================================================================

    /**
     * Returns ['data' => [...rows...], 'total' => int]
     */
    public static function getLogs(array $filters = [], int $limit = 25, int $offset = 0): array
    {
        return self::model()->getLogs($filters, $limit, $offset);
    }

    public static function getStats(?string $dateFrom = null): array
    {
        return self::model()->getStats($dateFrom);
    }

    public static function getFilterOptions(): array
    {
        return self::model()->getFilterOptions();
    }

    public static function findById(string $id): ?array
    {
        return self::model()->findById($id);
    }

    // =========================================================================
    //  Diff helper — build old/new slices automatically on model update
    // =========================================================================

    /**
     * Example:
     *   $old = $employee;                         // array before update
     *   // ... run UPDATE ...
     *   $new = fetchEmployeeById($id);            // array after update
     *   [$oldSlice, $newSlice] = AuditHelper::diff($old, $new);
     *   AuditHelper::log('employee_updated', 'employee', $id, $oldSlice, $newSlice);
     */
    public static function diff(array $before, array $after): array
    {
        $changed = array_keys(
            array_filter($after, fn($v, $k) => ($before[$k] ?? null) !== $v, ARRAY_FILTER_USE_BOTH)
        );

        $oldSlice = array_intersect_key($before, array_flip($changed));
        $newSlice = array_intersect_key($after,  array_flip($changed));

        return [$oldSlice, $newSlice];
    }

    // =========================================================================
    //  Private
    // =========================================================================

    private static function model(): AuditLog
    {
        if (self::$model === null) {
            throw new \RuntimeException(
                'AuditHelper not initialised. Call AuditHelper::init($db) in your bootstrap.'
            );
        }
        return self::$model;
    }
}