<?php

namespace Database\Seeders;

use App\Models\AlertType;
use App\Models\User;
use App\Models\UserAlertPermission;
use Illuminate\Database\Seeder;

/**
 * For every existing user, create one row per alert_type in their role,
 * defaulted to is_enabled = true (all checked).
 *
 * Uses firstOrCreate on purpose: if a Super Admin has already unchecked
 * something for a user via Master Settings, re-running this seeder will
 * NOT re-check it. It only fills in rows that don't exist yet — e.g. for
 * brand-new users, or newly added alert headings.
 *
 * Role codes confirmed against RoleSeeder (roles.code): AD, HP, SE, ML,
 * FD, AC. Roles with no seeded AlertType rows yet (fd, acc — waiting on
 * their heading lists) are simply skipped, harmlessly.
 */
class UserAlertPermissionSeeder extends Seeder
{
    /** AlertType role slug => roles.code */
    private const ROLE_CODE_MAP = [
        AlertType::ROLE_ADMIN => 'AD',
        AlertType::ROLE_HOP   => 'HP',
        AlertType::ROLE_SE    => 'SE',
        AlertType::ROLE_ML    => 'ML',
        AlertType::ROLE_FD    => 'FD',
        AlertType::ROLE_ACC   => 'AC',
    ];

    public function run(): void
    {
        foreach (self::ROLE_CODE_MAP as $slug => $code) {
            $alertTypes = AlertType::forRole($slug)->get();

            if ($alertTypes->isEmpty()) {
                continue;
            }

            User::whereHas('role', fn ($q) => $q->where('code', $code))
                ->chunk(100, function ($users) use ($alertTypes) {
                    foreach ($users as $user) {
                        foreach ($alertTypes as $alertType) {
                            UserAlertPermission::firstOrCreate(
                                [
                                    'user_id' => $user->id,
                                    'alert_type_id' => $alertType->id,
                                ],
                                [
                                    'is_enabled' => true,
                                ]
                            );
                        }
                    }
                });
        }
    }
}