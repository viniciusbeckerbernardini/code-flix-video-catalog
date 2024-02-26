<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Tests\TestCase;

class CastMemberTest extends TestCase
{
    public function testList()
    {
        factory(CastMember::class,1)->create();
        $castMember = CastMember::all();
        $castMemberKeys = array_keys($castMember->first()->getAttributes());
        $this->assertCount(1,$castMember);

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'type',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $castMemberKeys
        );

    }

    public function testCreate()
    {
        $castMember = CastMember::create([
            'name'=>'test1',
            'type'=>1
        ]);
        $castMember->refresh();

        $this->assertEquals('test1',$castMember->name);
        $this->assertEquals(1,$castMember->name);
    }

    public function testUpdateCastMember()
    {
        $castMember = factory(CastMember::class)->create([
            'type' => 1
        ]);

        $castMember->update(
            [
                'name'=>'updated',
                'type' => 2,
            ]
        );

        $data = [
            'name'=>'updated',
            'type' => 2
        ];

        $castMember->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }

    }

    public function testDeleteCastMember()
    {
        $castMember = factory(CastMember::class)->create();

        $castMember->delete();

        $this->assertEmpty($castMember->find(['id', $castMember['id']])->toArray());
    }

    public function testUuid()
    {
        $castMemberId = factory(CastMember::class)->create()->id;
        $this->assertTrue(Uuid::isValid($castMemberId));
    }
}
