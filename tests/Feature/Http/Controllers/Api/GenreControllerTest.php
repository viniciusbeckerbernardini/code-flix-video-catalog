<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('api.genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('api.genres.show', ['genre'=>$this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());

    }

    public function testInvalidationData()
    {
        $data = [
            'name'=> ''
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data =   [
            'name'=>str_repeat('a',256)
        ];
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max'=>255]);

        $data =   [
            'is_active'=>'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

    }


    public function testStore()
    {
        $response = $this->json('POST',route('api.genres.store'),
            [
                'name'=>'test',
                'categories_id'=>[$this->category->id]
            ]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST',route('api.genres.store'),
            [
                'name'=>'test',
                'is_active'=>false,
                'categories_id'=>[$this->category->id]
            ]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertFalse($response->json('is_active'));
    }

    public function testUpdate()
    {
        $data =[
            'name'=>'testUpdate',
            'is_active'=>false
        ];

        $response = $this->assertUpdate($data + ['categories_id'=>[$this->category->get('id')]], $data + ['deleted_at'=>null]);

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data =  [
            'name' => 'testUpdate2',
            'is_active' => true
        ];

        $response = $this->assertUpdate($data + ['categories_id'=>[$this->category->get('id')]], array_merge($data));
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
    }

    public function testDelete()
    {
        $response = $this->json('DELETE',route('api.genres.destroy',['genre'=>$this->genre->id]));
        $response
            ->assertStatus(204)
            ->assertNoContent();
        $this->assertEmpty(Genre::find($this->genre)->toArray());
        $this->assertNotNull(Genre::withoutTrashed()->find($this->genre));
    }

    public function routeStore()
    {
        return route('api.genres.store');
    }

    public function routeUpdate()
    {
        return route('api.genres.update',['genre'=>$this->genre->id]);
    }

    public function model()
    {
        return Genre::class;
    }
}
