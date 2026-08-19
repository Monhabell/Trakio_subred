<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

// Resources
use App\Http\Resources\V1\PackageResource;
use App\Http\Resources\V1\PackageCollection;

// Modelos
use App\Models\Package;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::with('receptions')->latest()->paginate();

        return new PackageCollection($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        $package = $package->load('receptions');

        return new PackageResource($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
