<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Entorno;
use App\Models\User;
use App\Models\Package;
use App\Models\Format;

class ProductivitySisco extends Model
{
    public $fillable = [
        'environment_id',
        'typed_by',
        'package_id',
        'format_id',
        'sisco_code',
        'count_users',
        'typed_date',
        'devolution_date',
        'observations',
        'received_by',
    ];

    public function environment()
    {
        return $this->belongsTo(Entorno::class);
    }

    public function typedBy()
    {
        return $this->belongsTo(User::class, 'typed_by');
    }

    public function packages()
    {
        return $this->belongsTo(Package::class);
    }

    public function bases()
    {
        return $this->belongsTo(Format::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
