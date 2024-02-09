<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('api.genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);

    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('api.genres.show', ['genre'=>$genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());

    }

    public function testInvalidationData()
    {
        $response = $this->json('POST',route('api.genres.store',[]));
        $this->assertInvalidRequired($response);

        $response = $this->json('POST',route('api.genres.store',
            [
                'name'=>str_repeat('a',256),
                'is_active'=>'tutuba'
            ]
        ));
        $this->assertMaxCharacters($response);
        $this->assertInvalidIsActive($response);


        $genre = factory(Genre::class)->create();
        $response = $this->json(
            'PUT',
            route('api.genres.update',
                [
                    'genre'=>$genre->id
                ]),
            [
                'name'=>str_repeat('a',256),
                'is_active'=>'a'
            ]
        );

        $this->assertMaxCharacters($response);
        $this->assertInvalidIsActive($response);
    }
    public function assertInvalidRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute'=>'name'])
            ]);
    }

    public function assertMaxCharacters(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name','is_active'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute'=>'name','max' => 255])
            ]);
    }

    public function assertInvalidIsActive(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name','is_active'])
            ->assertJsonFragment([
                trans('validation.boolean', ['attribute'=>'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->json('POST',route('api.genres.store'),
            [
                'name'=>'test',
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
        $genre = factory(Genre::class)->create([
            'is_active'=>false
        ]);
        $response = $this->json('PUT',route('api.genres.update',['genre'=>$genre->id]),
            [
                'name' => 'testUpdate',
                'is_active' => true
            ]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment( [
                'name' => 'testUpdate',
                'is_active' => true
            ]);

        $response = $this->json('PUT',route('api.genres.update',['genre'=>$genre->id]),
            ['name' => 'testUpdate2']
        );

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment(['name' => 'testUpdate2']);

    }
}
