<?php

namespace App\Exceptions;

use App\Models\Package;
use Exception;

class PackageAlreadyAssignedException extends Exception
{
    public function __construct(public readonly Package $package)
    {
        parent::__construct("El paquete {$package->num_package} ya está asignado a este usuario.");
    }
}
