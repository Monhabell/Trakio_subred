<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatUserDirectoryService
{
    private const TECHNICIAN_ROLE_ID = 12;

    private const DIGITIZER_ROLE_ID = 1;

    public function technicians(): Collection
    {
        return User::with(['entorno', 'role'])
            ->where('role_id', self::TECHNICIAN_ROLE_ID)
            ->where('is_active', 1)
            ->get();
    }

    public function usersSummary(): array
    {
        $users = User::with(['entorno', 'role'])
            ->where('is_active', 1)
            ->get();

        $environments = $users->groupBy('environment_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->entorno->entorno ?? 'Sin entorno',
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->all();

        return [
            'total' => $users->count(),
            'digitizers' => $users->where('role_id', self::DIGITIZER_ROLE_ID)->count(),
            'technicians' => $users->where('role_id', self::TECHNICIAN_ROLE_ID)->count(),
            'environments' => $environments,
        ];
    }
}
