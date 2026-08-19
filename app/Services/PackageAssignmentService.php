<?php

namespace App\Services;

use App\Exceptions\PackageAlreadyAssignedException;
use App\Models\Assignment;
use App\Models\Package;
use App\Models\Reception;
use App\Models\ReceptionStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PackageAssignmentService
{
    public function availablePackages(int $limit = 10): Collection
    {
        return Package::with(['receptions.bases', 'receptions.environment_file'])
            ->where('status', 0)
            ->limit($limit)
            ->get();
    }

    public function findAssignableByPackageNumber(string $packageNumber): ?Package
    {
        $package = Package::with(['receptions.bases', 'receptions.environment_file'])
            ->where('num_package', $packageNumber)
            ->first();

        if (!$package || !in_array($package->status, [0, 1, 2])) {
            return null;
        }

        return $package;
    }

    public function findActiveReceptionByFileNumber(string $fileNumber): ?Reception
    {
        return Reception::with([
            'packages' => function ($query) {
                $query->whereIn('status', [0, 1, 2]);
            },
        ])->where('file_number', $fileNumber)
            ->where('returned_by', null)
            ->first();
    }

    /**
     * @throws PackageAlreadyAssignedException
     */
    public function assign(User $user, Package $package): Assignment
    {
        $existsAssignment = Assignment::where('digitizer_id', $user->id)
            ->where('package_id', $package->id)
            ->first();

        if ($existsAssignment) {
            throw new PackageAlreadyAssignedException($package);
        }

        $assignment = Assignment::create([
            'digitizer_id' => $user->id,
            'package_id' => $package->id,
            'assigned_at' => now(),
        ]);

        if ($assignment) {
            $this->markReceptionsDeliveredToDigitizer($package->id);
        }

        if ($package->status == 0) {
            $package->update(['status' => 1]);
        }

        return $assignment;
    }

    private function markReceptionsDeliveredToDigitizer(int $packageId): void
    {
        $receptions = Reception::where('package_id', $packageId)->get();
        $updated = Reception::where('package_id', $packageId)->update(['status' => 12]);

        if ($updated) {
            foreach ($receptions as $reception) {
                ReceptionStatusHistory::create([
                    'reception_id' => $reception->id,
                    'previous_status' => $reception->status,
                    'new_status' => 12,
                    'changed_by' => $reception->received_by,
                ]);
            }
        }
    }
}
