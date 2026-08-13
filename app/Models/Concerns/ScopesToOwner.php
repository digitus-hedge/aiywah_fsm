<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopesToOwner
{
    /** Stamp the creator automatically on insert. */
    public static function bootScopesToOwner(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });
    }

    /** Override per model. Columns that mean "this row is theirs". */
    protected function ownershipColumns(): array
    {
        return ['created_by'];
    }

    public function scopeVisibleTo(Builder $query, ?User $user, string $key): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if (! $user->isFiltered($key)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($user) {
            foreach ($this->ownershipColumns() as $column) {
                $sub->orWhere($this->qualifyColumn($column), $user->id);
            }
        });
    }

    public function isOwnedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        foreach ($this->ownershipColumns() as $column) {
            if ((int) $this->getAttribute($column) === (int) $user->id) {
                return true;
            }
        }

        return false;
    }

}