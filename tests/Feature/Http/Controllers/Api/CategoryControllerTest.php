<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;
    private $genre;
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('api.categories.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('api.categories.show', ['category'=>$this->category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());

    }

    public function testInvalidationData()
    {
        $data = [
            'name'=> ''
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max'=>255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max'=>255]);

        $data = [
            'is_active' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

    }

    public function testStore()
    {
        $data = [
            'name'=>'test'
        ];
        $response = $this->assertStore($data + ['genres_id'=>[$this->genre->id]],$data + ['description' => null, 'is_active' => true, 'deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $data = [
            'name'=>'test',
            'is_active'=>false,
            'description'=>'lorem'
        ];
        $this->assertStore($data + ['genres_id'=>[$this->genre->id]],$data + ['description' => 'lorem', 'is_active' => false, 'deleted_at'=>null]);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'testUpdate',
            'description' => 'ipsum',
            'is_active' => true
        ];
        $response = $this->assertUpdate($data + ['genres_id'=>[$this->genre->id]], $data + ['deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data = [
            'name' => 'testUpdate2',
            'description' => ''
        ];

        $response = $this->assertUpdate($data + ['genres_id'=>[$this->genre->id]], array_merge($data, ['description' => null]));
        $response->assertJsonStructure([
                'created_at',
                'updated_at'
        ]);

        $data['description'] = 'test';
        $this->assertUpdate($data + ['genres_id'=>[$this->genre->id]], array_merge($data, ['description' => 'test']));

        $data['description'] = null;
        $this->assertUpdate($data + ['genres_id'=>[$this->genre->id]], array_merge($data, ['description' => null]));
    }

    public function testDelete()
    {
        $response = $this->json('DELETE',route('api.categories.destroy',['category'=>$this->category->id]));
        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertEmpty(Category::find($this->category)->toArray());
        $this->assertNotNull(Category::withoutTrashed()->find($this->category));
    }

    public function testSyncGenres()
    {
        $genresIds = factory(Genre::class,3)->create()->pluck('id')->toArray();

        $sendData = [
            'name'=>'test',
            'genres_id'=>[$genresIds[0]]
        ];

        $response = $this->json('POST',$this->routeStore(),$sendData);

        $this->assertDatabaseHas('category_genre',
            [
                'category_id'=>$response->json('id'),
                'genre_id'=>$genresIds[0]
            ]);

        $sendData = [
            'name'=>'test',
            'genres_id'=>[$genresIds[1],$genresIds[2]]
        ];

        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category'=>$response->json('id')]),
            $sendData
        );
        $this->assertDatabaseMissing('category_genre',[
            'category_id'=>$response->json('id'),
            'genre_id'=>$genresIds[0],
        ]);

        $this->assertDatabaseHas('category_genre',[
            'category_id'=>$response->json('id'),
            'genre_id'=>$genresIds[1],
        ]);

        $this->assertDatabaseHas('category_genre',[
            'category_id'=>$response->json('id'),
            'genre_id'=>$genresIds[2],
        ]);

    }


    public function routeStore()
    {
        return route('api.categories.store');
    }

    public function routeUpdate()
    {
        return route('api.categories.update',['category'=>$this->category->id]);
    }

    public function model()
    {
        return Category::class;
    }
}
