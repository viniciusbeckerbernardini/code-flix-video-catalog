<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GenreStub
{
    protected $table = 'genres_stubs';
    protected $fillable = [
        'name',
        'is_active'
    ];

    public static function createTable()
    {
        Schema::create('categories_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('is_active')->default(true);
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        Schema::dropIfExists('categories_stubs');
    }
}
