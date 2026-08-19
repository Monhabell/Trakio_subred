<?php

namespace App\Exceptions;

use Exception;

class DuplicateFileNumberException extends Exception
{
    public function __construct(public readonly string $fileNumber)
    {
        parent::__construct("El número {$fileNumber} ya existe en otra ficha.");
    }
}
