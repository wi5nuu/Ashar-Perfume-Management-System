<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit('created', $model, $model->toArray());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = array_intersect_key($model->getOriginal(), $changes);
            static::logAudit('updated', $model, ['old' => $original, 'new' => $changes]);
        });

        static::deleted(function ($model) {
            $event = in_array(SoftDeletes::class, class_uses_recursive($model)) && $model->isForceDeleting()
                ? 'force_deleted'
                : 'deleted';
            static::logAudit($event, $model, $model->toArray());
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                static::logAudit('restored', $model, $model->toArray());
            });
        }
    }

    protected static function logAudit(string $action, $model, $newData): void
    {
        if (!Auth::check()) return;

        $excluded = ['password', 'remember_token', 'updated_at', 'created_at'];

        $filter = function ($arr) use ($excluded) {
            if (!is_array($arr)) return ['raw' => (string)$arr];
            return array_diff_key(
                array_filter($arr, fn($v) => !is_null($v) && !is_object($v)),
                array_flip($excluded)
            );
        };

        $filteredData = is_array($newData) && isset($newData['old'])
            ? [
                'old' => $filter($newData['old']),
                'new' => $filter($newData['new']),
            ]
            : $filter($newData);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_model' => get_class($model),
            'target_id' => $model->getKey(),
            'old_data' => is_array($filteredData) && isset($filteredData['old']) ? $filteredData['old'] : null,
            'new_data' => is_array($filteredData) && isset($filteredData['new']) ? $filteredData['new'] : $filteredData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'tags' => class_basename($model),
        ]);
    }
}
