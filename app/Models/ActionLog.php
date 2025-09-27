<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CompanyProfile;

class ActionLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'action_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'resource_type',
        'resource_id',
        'action',
        'action_value',
        'performed_by',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Resource type constants
     */
    public const RESOURCE_TYPE_EMPLOYEE = 'employee';
    public const RESOURCE_TYPE_SALE = 'sale';
    public const RESOURCE_TYPE_EXPENSE = 'expense';
    public const RESOURCE_TYPE_ITEM = 'item';
    public const RESOURCE_TYPE_CLIENT = 'client';

    /**
     * Action constants
     */
    public const ACTION_ADVANCE_PAID = 'advance_paid';
    public const ACTION_ADVANCE_CLEARED = 'advance_cleared';
    public const ACTION_SALARY_PAID = 'salary_paid';
    public const ACTION_ITEM_PRICE_UPDATED = 'item_price_updated';

    /**
     * Get the user who performed the action.
     *
     * @return BelongsTo
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the related resource (polymorphic-like relationship)
     * This method helps to get the actual model instance based on resource_type and resource_id
     *
     * @return Model|null
     */
    public function getResourceAttribute(): ?Model
    {
        return match ($this->resource_type) {
            self::RESOURCE_TYPE_EMPLOYEE => Employee::find($this->resource_id),
            self::RESOURCE_TYPE_SALE => \App\Models\Sales\Sale::find($this->resource_id),
            self::RESOURCE_TYPE_ITEM => \App\Models\Inventory\Item::find($this->resource_id),
            self::RESOURCE_TYPE_CLIENT => Client::find($this->resource_id),
            default => null,
        };
    }

    /**
     * Scope to filter by resource type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $resourceType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope to filter by action
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by resource
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $resourceType
     * @param int $resourceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForResource($query, string $resourceType, int $resourceId)
    {
        return $query->where('resource_type', $resourceType)
                    ->where('resource_id', $resourceId);
    }

    /**
     * Get a human-readable description of the action
     *
     * @return string
     */
    public function getActionDescriptionAttribute(): string
    {
        $resource = $this->resource;
        $resourceName = $resource ? $this->getResourceName($resource) : "Resource #{$this->resource_id}";

        return match ($this->action) {
            self::ACTION_ADVANCE_PAID => "Advance payment of ₹{$this->action_value} made to {$resourceName}",
            self::ACTION_ADVANCE_CLEARED => "Advance amount cleared for {$resourceName}",
            self::ACTION_SALARY_PAID => "Salary of ₹{$this->action_value} paid to {$resourceName}",
            self::ACTION_ITEM_PRICE_UPDATED => "Price updated to ₹{$this->action_value} for {$resourceName}",
            default => "Action '{$this->action}' performed on {$resourceName}",
        };
    }

    /**
     * Get a human-readable name for the resource
     *
     * @param Model $resource
     * @return string
     */
    private function getResourceName(Model $resource): string
    {
        return match ($this->resource_type) {
            self::RESOURCE_TYPE_EMPLOYEE => "{$resource->first_name} {$resource->last_name}",
            self::RESOURCE_TYPE_ITEM => $resource->name ?? "Item #{$resource->id}",
            self::RESOURCE_TYPE_CLIENT => $resource->name ?? "Client #{$resource->id}",
            default => get_class($resource) . " #{$resource->id}",
        };
    }

    /**
     * Create a new action log entry
     *
     * @param string $resourceType
     * @param int $resourceId
     * @param string $action
     * @param string|null $actionValue
     * @param int $performedBy
     * @param string|null $remarks
     * @return static
     */
    public static function logAction(
        string $resourceType,
        int $resourceId,
        string $action,
        ?string $actionValue = null,
        ?int $performedBy = null,
        ?string $remarks = null,
    ): self {
        return self::create([
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'action_value' => $actionValue,
            'performed_by' => $performedBy ?? auth()->id(),
            'remarks' => $remarks,
        ]);
    }

    /**
     * Get the company profile that owns the payment.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }
}
