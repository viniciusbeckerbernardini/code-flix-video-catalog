<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes,Uuid;
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active'=>'boolean'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
