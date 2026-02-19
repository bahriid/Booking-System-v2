<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request): InertiaResponse
    {
        $query = AuditLog::query()
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->withAction($request->action);
        }

        // Filter by entity type
        if ($request->filled('entity_type')) {
            $query->where('entity_type', 'like', '%' . $request->entity_type . '%');
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(25)->withQueryString();

        // Get unique entity types for filter dropdown
        $entityTypes = AuditLog::query()
            ->select('entity_type')
            ->distinct()
            ->pluck('entity_type')
            ->map(fn($type) => [
                'value' => $type,
                'label' => class_basename($type),
            ]);

        // Get actions for filter dropdown
        $actions = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
        ];

        return Inertia::render('admin/audit-logs/index', [
            'auditLogs' => $auditLogs,
            'entityTypes' => $entityTypes,
            'actions' => $actions,
            'filters' => $request->only(['action', 'entity_type', 'user_id', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Display audit log details.
     */
    public function show(AuditLog $auditLog): InertiaResponse
    {
        $auditLog->load('user');

        return Inertia::render('admin/audit-logs/show', compact('auditLog'));
    }
}
