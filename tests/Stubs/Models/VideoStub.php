<?php

namespace Tests\Stubs\Models;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VideoStub
{
    protected $table = 'videos_stubs';
    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];

    protected $casts = [
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public static function createTable()
    {
        Schema::create('videos_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            $table->smallInteger('year_launched');
            $table->boolean('opened')->default(false);
            $table->string('rating',3);
            $table->smallInteger('duration');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        Schema::dropIfExists('videos_stubs');
    }
}
