<?php

namespace Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;
    public function testList()
    {
        factory(Video::class,1)->create();
        $videos = Video::all();
        $videoKey = array_keys($videos->first()->getAttributes());
        $this->assertCount(1,$videos);

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $videoKey
        );
    }

    public function testCreate()
    {
        factory(Genre::class,1)->create();
        $genre = Genre::inRandomOrder()->first();

        factory(Category::class,1)->create();
        $category =Category::inRandomOrder()->first();

        $video = Video::create([
            'title'=>'video_test_1',
            'description' => 'description_test_1',
            'year_launched' => '2020',
            'opened' => true,
            'rating' => Video::RATING_LIST[0],
            'duration' => 200,
            'genres' => [$genre->id],
            'categories' => [$category->id]
        ]);
        $video->refresh();

        $this->assertEquals('video_test_1', $video->title);
        $this->assertEquals('description_test_1', $video->description);
        $this->assertEquals('2020', $video->year_launched);
        $this->assertTrue((bool)$video->opened);
        $this->assertEquals(Video::RATING_LIST[0], $video->rating);
        $this->assertEquals(200, $video->duration);
    }

    public function testUpdate()
    {
        factory(Genre::class,1)->create();
        $genre = Genre::inRandomOrder()->first();

        factory(Category::class,1)->create();
        $category =Category::inRandomOrder()->first();

        $video = Video::create([
            'title'=>'video_test_1',
            'description' => 'description_test_1',
            'year_launched' => '2020',
            'opened' => true,
            'rating' => Video::RATING_LIST[0],
            'duration' => 200,
            'genres' => [$genre->id],
            'categories' => [$category->id]
        ]);
        $video->refresh();

        $data = [
            'title'=>'video_test_update',
            'description' => 'description_test_update',
            'year_launched' => '2022',
            'opened' => false,
            'rating' => Video::RATING_LIST[1],
            'duration' => 200,
            'genres' => [$genre->id],
            'categories' => [$category->id]
        ];

        $video->update($data);

        $this->assertEquals('video_test_update', $video->title);
        $this->assertEquals('description_test_update', $video->description);
        $this->assertEquals('2022', $video->year_launched);
        $this->assertFalse((bool)$video->opened);
        $this->assertEquals(Video::RATING_LIST[1], $video->rating);
        $this->assertEquals(200, $video->duration);
    }

    public function testDelete()
    {
        $video = factory(Video::class)->create();

        Video::where('id',[$video['id']])->delete();

        $this->assertEmpty($video::find(['id',$video['id']])->toArray());
    }

    public function testUuid()
    {
        $videoId = factory(Video::class)->create()->id;
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid($videoId));
    }

}
