<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes,Uuid;
    protected $table = 'genres';
    protected $fillable = [
        'name',
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

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
