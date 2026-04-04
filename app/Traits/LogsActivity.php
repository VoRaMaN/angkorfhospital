<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::log(
                'created',
                static::getActivityDescription('created', $model),
                $model,
                ['attributes' => $model->getAttributes()],
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $old = collect($changes)->mapWithKeys(fn ($value, $key) => [$key => $model->getOriginal($key)])->toArray();

            ActivityLog::log(
                'updated',
                static::getActivityDescription('updated', $model),
                $model,
                ['old' => $old, 'new' => $changes],
            );
        });

        static::deleted(function ($model) {
            ActivityLog::log(
                'deleted',
                static::getActivityDescription('deleted', $model),
                $model,
            );
        });
    }

    protected static function getActivityDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->full_name ?? $model->title ?? $model->getKey();

        return match ($action) {
            'created' => "{$modelName} \"{$identifier}\" was created",
            'updated' => "{$modelName} \"{$identifier}\" was updated",
            'deleted' => "{$modelName} \"{$identifier}\" was deleted",
            default => "{$modelName} \"{$identifier}\" - {$action}",
        };
    }
}
