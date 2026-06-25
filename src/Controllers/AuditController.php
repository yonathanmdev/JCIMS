<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;

/**
 * AuditController — plain PHP MVC, no Laravel.
 *
 * Routes to wire up in your router:
 *
 *   GET  /admin/audit-logs           → AuditController@index      (HTML page)
 *   GET  /admin/audit-logs/data      → AuditController@data       (JSON — table rows)
 *   GET  /admin/audit-logs/stats     → AuditController@stats      (JSON — stat cards)
 *   GET  /admin/audit-logs/{id}      → AuditController@show       (JSON — single row)
 *   GET  /admin/audit-logs/export    → AuditController@export     (CSV download)
 */
class AuditController extends BaseController
{
    // =========================================================================
    //  HTML page — just renders the Blade/PHP view; JS fetches all data via API
    // =========================================================================

    public function index(): void
    {
        AuthHelper::checkRole(['system_admin']);

        $this->render('audit-logs', [
            'title' => 'የኦዲት ሎግ',   // kept from your original
        ]);
    }

    // =========================================================================
    //  JSON — paginated table data
    // =========================================================================

    public function data(): void
    {
        AuthHelper::checkRole(['system_admin']);

        $filters = $this->buildFilters();
        $perPage = $this->intParam('per_page', 25, 1, 100);
        $page    = $this->intParam('page',     1,  1);
        $offset  = ($page - 1) * $perPage;

        $result  = AuditHelper::getLogs($filters, $perPage, $offset);
        $total   = $result['total'];
        $rows    = $result['data'];

        $this->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
                'from'         => $total === 0 ? 0 : $offset + 1,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    // =========================================================================
    //  JSON — stat cards + filter-dropdown options
    // =========================================================================

    public function stats(): void
    {
        AuthHelper::checkRole(['system_admin']);

        // Stats over last 30 days (matches original behaviour)
        $dateFrom = date('Y-m-d H:i:s', strtotime('-30 days'));
        $stats    = AuditHelper::getStats($dateFrom);
        $options  = AuditHelper::getFilterOptions();

        $this->json([
            'data' => array_merge($stats, $options),
        ]);
    }

    // =========================================================================
    //  JSON — single entry (detail panel)
    // =========================================================================

    public function show($params=[]): void
    {
        AuthHelper::checkRole(['system_admin']);

        $id = $params['id'] ?? $_GET['id'] ?? '';
        if ($id === '') {
            $this->json(['error' => 'Missing id'], 400);
            return;
        }

        $row = AuditHelper::findById($id);

        if (!$row) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

        $this->json(['data' => $row]);
    }

    // =========================================================================
    //  CSV export — streamed, chunked so memory stays flat
    // =========================================================================

    public function export(): void
    {
        AuthHelper::checkRole(['system_admin']);

        // Log the export action itself
        AuditHelper::log(
            action:     'audit_log_exported',
            entityType: 'audit_log',
            metadata:   $_GET
        );

        $filters  = $this->buildFilters();
        $filename = 'audit_log_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store');

        $handle  = fopen('php://output', 'w');
        // UTF-8 BOM so Excel opens it correctly
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, ['ID', 'Timestamp', 'User', 'Action', 'Entity Type', 'Entity ID', 'IP Address', 'Has Changes'], ',', '"', '\\');

        $chunkSize = 500;
        $offset    = 0;

        do {
            $result = AuditHelper::getLogs($filters, $chunkSize, $offset);
            foreach ($result['data'] as $row) {
                fputcsv($handle, [
                    $row['id'],
                    $row['created_at'],
                    $row['user_name'],
                    $row['action'],
                    $row['entity_type'] ?? '',
                    $row['entity_id']   ?? '',
                    $row['ip_address']  ?? '',
                    $row['has_changes'] ? 'Yes' : 'No',
                ], ',', '"', '\\');
            }
            $offset += $chunkSize;
        } while ($offset < $result['total']);

        fclose($handle);
        exit;
    }

    // =========================================================================
    //  Private helpers
    // =========================================================================

    private function buildFilters(): array
    {
        $filters = [];

        if (!empty($_GET['user_id']))     $filters['user_id']     = $_GET['user_id'];
        if (!empty($_GET['audit_action'])) $filters['action']      = $_GET['audit_action'];
        elseif (!empty($_GET['action']))   $filters['action']      = $_GET['action'];
        if (!empty($_GET['entity_type']))  $filters['entity_type'] = $_GET['entity_type'];
        if (!empty($_GET['search']))      $filters['search']      = $_GET['search'];

        if (!empty($_GET['date_from']))
            $filters['date_from'] = $_GET['date_from'] . ' 00:00:00';

        if (!empty($_GET['date_to']))
            $filters['date_to']   = $_GET['date_to']   . ' 23:59:59';

        // Preset date range shortcut (today / week / month)
        if (!empty($_GET['date_preset'])) {
            switch ($_GET['date_preset']) {
                case 'today':
                    $filters['date_from'] = date('Y-m-d') . ' 00:00:00';
                    $filters['date_to']   = date('Y-m-d') . ' 23:59:59';
                    break;
                case 'week':
                    $filters['date_from'] = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';
                    $filters['date_to']   = date('Y-m-d') . ' 23:59:59';
                    break;
                case 'month':
                    $filters['date_from'] = date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';
                    $filters['date_to']   = date('Y-m-d') . ' 23:59:59';
                    break;
            }
        }

        return $filters;
    }

    /** Read an integer GET param safely with optional min/max bounds */
    private function intParam(string $key, int $default, int $min = 0, int $max = PHP_INT_MAX): int
    {
        $v = isset($_GET[$key]) ? (int) $_GET[$key] : $default;
        return max($min, min($max, $v));
    }

    // ── Response helpers (add to BaseController if not already there) ─────────

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}