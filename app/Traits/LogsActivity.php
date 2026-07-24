<?php
// app/Traits/LogsActivity.php

namespace App\Traits;
use App\Models\ActivityLog;
trait LogsActivity
{
    // fields never written to the log
    protected static array $logHidden = [
        'password', 'remember_token', 'api_token',
        'created_at', 'updated_at',
    ];

    public static function bootLogsActivity(): void
    {
        static::created(fn($m) => $m->writeActivityLog('created', [], $m->logSafe($m->getAttributes())));

        static::updated(function ($m) {
            $new = $m->logSafe($m->getChanges());
            if (empty($new)) return;                       // nothing meaningful changed
            $old = array_intersect_key($m->getOriginal(), $new);
            $m->writeActivityLog('updated', $old, $new);
        });

        static::deleted(fn($m) => $m->writeActivityLog('deleted', $m->logSafe($m->getOriginal()), []));
    }

    protected function logSafe(array $attrs): array
    {
        return array_diff_key($attrs, array_flip(static::$logHidden));
    }

    protected function writeActivityLog(string $type, array $old, array $new): void
    {
        $model = class_basename($this);

        ActivityLog::create([
            'user_id'       => auth()->id(),
            'module'        => $model,
            'activity_type' => $type,
            'subject_type'  => static::class,
            'subject_id'    => $this->getKey(),
            'description'   => $model . ' #' . $this->getKey() . ' ' . $type,
            'old_values'    => $old ?: null,
            'new_values'    => $new ?: null,
            'ip_address'    => request()->ip(),
            'user_agent'    => substr((string) request()->userAgent(), 0, 500),
        ]);
    }
}