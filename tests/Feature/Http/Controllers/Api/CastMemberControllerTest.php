<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('api.cast-members.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->castMember->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('api.cast-members.show', ['cast_member'=>$this->castMember->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->castMember->toArray());

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


    }

    public function testStore()
    {
        $data = [
            'name'=>'test',
        ];
        $response = $this->assertStore($data,$data + ['deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $data = [
            'name'=>'test'
        ];
        $this->assertStore($data,$data + ['deleted_at'=>null]);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'testUpdate'
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data = [
            'name' => 'testUpdate2'
        ];

        $response = $this->assertUpdate($data, $data);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
    }

    public function testDelete()
    {
        $response = $this->json('DELETE',route('api.cast-members.destroy',['cast_member'=>$this->castMember->id]));
        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertEmpty(CastMember::find($this->castMember)->toArray());
        $this->assertNotNull(CastMember::withoutTrashed()->find($this->castMember));
    }

    public function routeStore()
    {
        return route('api.cast-members.store');
    }

    public function routeUpdate()
    {
        return route('api.cast-members.update',['cast_member'=>$this->castMember->id]);
    }

    public function model()
    {
        return CastMember::class;
    }

}
