<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Fields that should be masked in activity log properties.
     */
    protected static array $sensitiveFields = [
        'password',
        'password_confirmation',
        'secret',
        'token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::log(
                'created',
                static::getActivityDescription('created', $model),
                $model,
                ['attributes' => static::maskSensitiveFields($model->getAttributes())],
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
                [
                    'old' => static::maskSensitiveFields($old),
                    'new' => static::maskSensitiveFields($changes),
                ],
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

    /**
     * Mask sensitive fields in the given attributes array.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected static function maskSensitiveFields(array $attributes): array
    {
        foreach (static::$sensitiveFields as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = '********';
            }
        }

        return $attributes;
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
