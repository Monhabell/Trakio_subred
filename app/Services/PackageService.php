<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Reception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageService
{
    public function updateStatus($package_id)
    {
        try {
            DB::beginTransaction();
            $package_files = Reception::where('package_id', $package_id)
                ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
                ->select(
                    'receptions.package_id',
                    DB::raw('COUNT(receptions.id) AS count'),
                    DB::raw('COUNT(productivity.id) AS productivity_count')
                )
                ->groupBy('receptions.package_id')
                ->first();

            if (!$package_files)
                return false;

            $receptions_count = $package_files->count;
            $productivity_count = $package_files->productivity_count;

            $packageStatus = $productivity_count < $receptions_count ? 2 : 3;

            $updated = Package::where('id', $package_id)->update([
                'status' => $packageStatus
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

        }


        return true;
    }
}