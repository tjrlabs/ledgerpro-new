<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\ActionLog;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ActionLogRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all action logs with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllActionLogs(array $filters = []): Collection
    {
        $query = ActionLog::with(['performedBy']);

        // Filter by resource type
        if (isset($filters['resource_type']) && !empty($filters['resource_type'])) {
            $query->forResourceType($filters['resource_type']);
        }

        // Filter by Company Profile Id
        $companyProfileId = auth()->user()->company_profile_id ?? 1;
        $query->where('company_profile_id', $companyProfileId);

        // Filter by action
        if (isset($filters['action']) && !empty($filters['action'])) {
            $query->forAction($filters['action']);
        }

        // Filter by specific resource
        if (isset($filters['resource_id']) && !empty($filters['resource_id']) &&
            isset($filters['resource_type']) && !empty($filters['resource_type'])) {
            $query->forResource($filters['resource_type'], $filters['resource_id']);
        }

        // Filter by user who performed the action
        if (isset($filters['performed_by']) && !empty($filters['performed_by'])) {
            $query->where('performed_by', $filters['performed_by']);
        }

        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Create a new action log entry
     *
     * @param string $resourceType
     * @param int $resourceId
     * @param string $action
     * @param string|null $actionValue
     * @param int|null $performedBy
     * @return ResponseData
     */
    public function createActionLog(
        string $resourceType,
        int $resourceId,
        string $action,
        ?string $actionValue = null,
        ?string $remarks = null,
        ?int $performedBy = null
    ): ResponseData {
        try {
            $actionLog = ActionLog::create([
                'company_profile_id' => auth()->user()->company_profile_id ?? 1,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'action' => $action,
                'action_value' => $actionValue,
                'remarks' => $remarks,
                'performed_by' => $performedBy ?? auth()->id(),
            ]);

            return new SuccessData($actionLog->toArray());
        } catch (Exception $e) {
            return new ErrorData(['Failed to create action log: ' . $e->getMessage()]);
        }
    }

    /**
     * Get action logs for a specific resource
     *
     * @param string $resourceType
     * @param int $resourceId
     * @return Collection
     */
    public function getResourceActionLogs(string $resourceType, int $resourceId): Collection
    {
        return ActionLog::forResource($resourceType, $resourceId)
                        ->with(['performedBy'])
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    /**
     * Get action logs by action type
     *
     * @param string $action
     * @param int|null $limit
     * @return Collection
     */
    public function getActionsByType(string $action, ?int $limit = null): Collection
    {
        $query = ActionLog::forAction($action)
                          ->with(['performedBy'])
                          ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get recent action logs
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentActionLogs(int $limit = 50): Collection
    {
        return ActionLog::with(['performedBy'])
                        ->orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();
    }

    /**
     * Get action log statistics
     *
     * @param array $filters
     * @return array
     */
    public function getActionLogStatistics(array $filters = []): array
    {
        $query = ActionLog::query();

        // Apply date filters
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_actions' => $query->count(),
            'actions_by_type' => $query->selectRaw('action, count(*) as count')
                                     ->groupBy('action')
                                     ->pluck('count', 'action')
                                     ->toArray(),
            'actions_by_resource_type' => $query->selectRaw('resource_type, count(*) as count')
                                               ->groupBy('resource_type')
                                               ->pluck('count', 'resource_type')
                                               ->toArray(),
            'most_active_users' => $query->selectRaw('performed_by, count(*) as count')
                                        ->groupBy('performed_by')
                                        ->orderBy('count', 'desc')
                                        ->limit(10)
                                        ->pluck('count', 'performed_by')
                                        ->toArray(),
        ];
    }

    /**
     * Log an advance payment action
     *
     * @param int $employeeId
     * @param string $amount
     * @param int|null $performedBy
     * @return ResponseData
     */
    public function logAdvancePayment(int $employeeId, string $amount, ?int $performedBy = null, ?string $remarks = null): ResponseData
    {
        return $this->createActionLog(
            ActionLog::RESOURCE_TYPE_EMPLOYEE,
            $employeeId,
            ActionLog::ACTION_ADVANCE_PAID,
            $amount,
            $remarks,
            $performedBy
        );
    }

    /*
     * Log a salary payment action
     *
     * @param int $employeeId
     * @param string $amount
     * @param int|null $performedBy
     * @return ResponseData
     */
    public function logSalaryPayment(int $employeeId, string $amount, ?int $performedBy = null, ?string $remarks = null): ResponseData
    {
        return $this->createActionLog(
            ActionLog::RESOURCE_TYPE_EMPLOYEE,
            $employeeId,
            ActionLog::ACTION_SALARY_PAID,
            $amount,
            $remarks,
            $performedBy
        );
    }

    /**
     * Log an item price update action
     * @param int $itemId
     * @param string $newPrice
     * @param int|null $performedBy
     * @return ResponseData
     */
    public function logItemPriceUpdate(int $itemId, string $newPrice, ?int $performedBy = null, ?string $remarks = null): ResponseData
    {
        return $this->createActionLog(
            ActionLog::RESOURCE_TYPE_ITEM,
            ActionLog::ACTION_ITEM_PRICE_UPDATED,
            $newPrice,
            $performedBy,
            $remarks,
            $performedBy
        );
    }

    /**
     * Get employee advance payment history
     *
     * @param int $employeeId
     */
    public function getEmployeeAdvanceHistory(int $employeeId): Collection
    {
        return ActionLog::forResource(ActionLog::RESOURCE_TYPE_EMPLOYEE, $employeeId)
                        ->forAction(ActionLog::ACTION_ADVANCE_PAID)
                        ->with(['performedBy'])
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    /**
     * Get employee salary payment history
     *
     * @param int $employeeId
     * @return Collection
     */
    public function getEmployeeSalaryHistory(int $employeeId): Collection
    {
        return ActionLog::forResource(ActionLog::RESOURCE_TYPE_EMPLOYEE, $employeeId)
                        ->forAction(ActionLog::ACTION_SALARY_PAID)
                        ->with(['performedBy'])
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    /**
     * Delete old action logs
     *
     * @param int $daysOld
     * @return ResponseData
     */
    public function deleteOldActionLogs(int $daysOld = 365): ResponseData
    {
        try {
            $cutoffDate = now()->subDays($daysOld);
            $deletedCount = ActionLog::where('created_at', '<', $cutoffDate)->delete();

            return new SuccessData([
                'message' => "Deleted {$deletedCount} old action logs",
                'deleted_count' => $deletedCount
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete old action logs: ' . $e->getMessage());
            return new ErrorData(['Failed to delete old action logs: ' . $e->getMessage()]);
        }
    }

    /**
     * Get action logs with pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedActionLogs(array $filters = [], int $perPage = 15)
    {
        $query = ActionLog::with(['performedBy']);

        // Filter by Company Profile Id
        $companyProfileId = auth()->user()->company_profile_id ?? 1;
        $query->where('company_profile_id', $companyProfileId);

        // Apply filters (same as getAllActionLogs)
        if (isset($filters['resource_type']) && !empty($filters['resource_type'])) {
            $query->forResourceType($filters['resource_type']);
        }

        if (isset($filters['action']) && !empty($filters['action'])) {
            $query->forAction($filters['action']);
        }

        if (isset($filters['resource_id']) && !empty($filters['resource_id']) &&
            isset($filters['resource_type']) && !empty($filters['resource_type'])) {
            $query->forResource($filters['resource_type'], $filters['resource_id']);
        }

        if (isset($filters['performed_by']) && !empty($filters['performed_by'])) {
            $query->where('performed_by', $filters['performed_by']);
        }

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
