<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('api.categories.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);

    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('api.categories.show', ['category'=>$category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());

    }

    public function testInvalidationData()
    {
        $data = [
            'name'=> ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max'=>255]);

        $data = [
            'is_active' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');


        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('api.categories.update',
            [
                'category'=>$category->id
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
        $this->assertInvalidationFields($response,['name'],'required');
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    public function assertMaxCharacters(TestResponse $response)
    {
        $this->assertInvalidationFields($response,['name'],'max.string', ['max' => 255]);
    }

    public function assertInvalidIsActive(TestResponse $response)
    {
        $this->assertInvalidationFields($response,['is_active'],'boolean');
    }

    public function testStore()
    {
        $response = $this->json('POST',route('api.categories.store'),
        [
           'name'=>'test',
        ]);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST',route('api.categories.store'),
            [
                'name'=>'test',
                'is_active'=>false,
                'description'=>'lorem'
            ]);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertFalse($response->json('is_active'));
        $this->assertEquals('lorem',$response->json('description'));
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'is_active'=>false,
            'description'=>'lorem'
        ]);
        $response = $this->json('PUT',route('api.categories.update',['category'=>$category->id]),
            [
                'name' => 'testUpdate',
                'description' => 'ipsum',
                'is_active' => true
            ]);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment( [
                'name' => 'testUpdate',
                'description' => 'ipsum',
                'is_active' => true
            ]);

        $response = $this->json('PUT',route('api.categories.update',['category'=>$category->id]),
            [
                'name' => 'testUpdate2',
                'description' => ''
            ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment( [
                'name' => 'testUpdate2',
                'description' => null
            ]);
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create([
            'is_active'=>false
        ]);
        $response = $this->json('DELETE',route('api.categories.destroy',['category'=>$category->id]));
        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertEmpty(Category::find($category)->toArray());
        $this->assertNotNull(Category::withoutTrashed()->find($category));
    }

    public function routeStore()
    {
        return route('api.categories.store');
    }
}
