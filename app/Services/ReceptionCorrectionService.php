<?php

namespace App\Services;

use App\Exceptions\DuplicateFileNumberException;
use App\Models\Reception;
use Illuminate\Database\Eloquent\Collection;

class ReceptionCorrectionService
{
    public function searchByFileNumber(string $fileNumber): Collection
    {
        return Reception::where('file_number', $fileNumber)->get();
    }

    public function findWithDetails(int $id): ?Reception
    {
        return Reception::with(['bases', 'packages'])->find($id);
    }

    public function find(int $id): ?Reception
    {
        return Reception::find($id);
    }

    public function findDetailedByFileNumber(string $fileNumber): ?Reception
    {
        return Reception::with(['bases', 'environment_file', 'packages', 'typedBy', 'productivity'])
            ->where('file_number', $fileNumber)
            ->first();
    }

    /**
     * @throws DuplicateFileNumberException
     * @return string the previous file number
     */
    public function renumber(Reception $reception, string $newFileNumber): string
    {
        $existing = Reception::where('file_number', $newFileNumber)->first();

        if ($existing) {
            throw new DuplicateFileNumberException($newFileNumber);
        }

        $oldNumber = $reception->file_number;
        $reception->file_number = $newFileNumber;
        $reception->save();

        return $oldNumber;
    }
}
