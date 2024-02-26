<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CastMemberStub extends Model
{
    protected $table = 'cast_members_stubs';
    protected $fillable = [
        'name',
        'type'
    ];

    public static function createTable()
    {
        Schema::create('categories_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->tinyInteger('type');
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        Schema::dropIfExists('categories_stubs');
    }
}
