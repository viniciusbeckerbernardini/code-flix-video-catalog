<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes,Uuid;

    public const TYPE_DIRECTOR = 1;
    public const TYPE_ACTOR = 2;

    protected $fillable = [
        'name',
        'type'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
