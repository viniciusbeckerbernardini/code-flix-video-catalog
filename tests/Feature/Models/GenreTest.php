<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class,1)->create();
        $categories = Genre::all();
        $genreKey = array_keys($categories->first()->getAttributes());
        $this->assertCount(1,$categories);

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $genreKey
        );

    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name'=>'test1'
        ]);
        $genre->refresh();

        $this->assertEquals('test1',$genre->name);
        $this->assertTrue((bool)$genre->is_active);

        $genre = Genre::create([
            'name'=>'test1',
        ]);
        $genre->refresh();

        $genre = Genre::create([
            'name'=>'test1',
            'is_active'=> false
        ]);
        $genre->refresh();

        $this->assertFalse((bool)$genre->is_active);
    }

    public function testUpdateGenre()
    {
        $genre = factory(Genre::class)->create();

        $genre->update(
            [
                'name'=>'updated',
                'is_active' => false
            ]
        );

        $data = [
            'name'=>'updated 2',
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }

    }

    public function testDeleteGenre()
    {
        $genre = factory(Genre::class)->create();

        $genre->delete();

        $this->assertEmpty($genre->find(['id', $genre['id']])->toArray());
    }

    public function testUuid()
    {
        $genreId = factory(Genre::class)->create()->id;
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid($genreId));
    }
}
