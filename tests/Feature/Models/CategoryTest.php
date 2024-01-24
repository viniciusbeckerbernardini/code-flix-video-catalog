<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class,1)->create();
        $categories = Category::all();
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertCount(1,$categories);

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $categoryKey
        );

    }

    public function testCreate()
    {
        $category = Category::create([
            'name'=>'test1'
        ]);
        $category->refresh();

        $this->assertEquals('test1',$category->name);
        $this->assertNull($category->description);
        $this->assertTrue((bool)$category->is_active);

        $category = Category::create([
            'name'=>'test1',
            'description'=>'test'
        ]);
        $category->refresh();
        $this->assertEquals('test',$category->description);

        $category = Category::create([
            'name'=>'test1',
            'is_active'=> false
        ]);
        $category->refresh();

        $this->assertFalse((bool)$category->is_active);
    }

    public function testUpdateCategory()
    {
        $category = factory(Category::class)->create([
           'description' => 'test desc'
        ]);

        $category->update(
            [
                'name'=>'updated',
                'description' => 'desc updated',
                'is_active' => false
            ]
        );

        $data = [
            'name'=>'updated',
            'description' => 'desc updated',
            'is_active' => false
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }

    }

    public function testDeleteCategory()
    {
        $category = factory(Category::class)->create();

        $category->delete();

        $this->assertEmpty($category->find(['id', $category['id']])->toArray());
    }

    public function testUuid()
    {
        $categoryId = factory(Category::class)->create()->id;
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid($categoryId));
    }

}
