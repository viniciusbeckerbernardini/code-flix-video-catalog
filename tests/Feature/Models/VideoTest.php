<?php

namespace Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'title'=>'title',
            'description'=>'description',
            'year_launched'=>2010,
            'rating'=>Video::RATING_LIST[0],
            'duration'=>90
        ];
    }

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

    /**
     * @return void
     * @deprecated
     * @ignore
     */
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

    public function testCreateWithBasicFields(){
        $video = Video::create($this->data);
        $video->refresh();

        $this->assertEquals(36,strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos',$this->data + ['opened'=>false]);

        $video = Video::create($this->data + ['opened'=>true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos',$this->data + ['opened'=>true]);
    }

    public function testCreatedWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ]);
        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testUpdateWithBasicFields(){
        $video = Video::create($this->data + ['opened'=>false]);
        $video->refresh();
        $video->update($this->data);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos',$this->data + ['opened'=>false]);

        $video->update($this->data + ['opened'=>true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos',$this->data + ['opened'=>true]);
    }

    public function testUpdatedWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data);
        $video->update($this->data + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ]);
        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testHandleRelations()
    {
        $video = factory(Video::class)->create();
        Video::handleRelations($video, []);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        $category = factory(Category::class)->create();
        Video::handleRelations($video,[
            'categories_id' => [$category->id]
        ]);
        $video->refresh();

        $this->assertCount(1, $video->categories);
        $genre = factory(Genre::class)->create();
        Video::handleRelations($video,[
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->genres);
        $video->categories()->delete();
        $video->genres()->delete();

        Video::handleRelations($video,[
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->genres);
        $this->assertCount(1, $video->categories);

    }

    public function assertHasCategory(string $videoId, string $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id'=>$videoId,
            'category_id'=>$categoryId
        ]);
    }

    public function assertHasGenre(string $videoId, string $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id'=>$videoId,
            'genre_id'=>$genreId
        ]);
    }

    /**
     * @return void
     * @deprecated
     * @ignore
     */
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

    public function testRollbackCreate(){
        $hasError = false;
        try {
            Video::create(
                [
                    'title'=>'title',
                    'description'=>'description',
                    'year_launched'=>2010,
                    'rating'=>Video::RATING_LIST[0],
                    'duration'=>90,
                    'categories_id'=>[0,1,2],
                ]
            );
        }catch (QueryException $exception){
            $this->assertCount(0,Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate(){
        $video = factory(Video::class)->create();
        $oldTitle = $video->title;
        $videoUpdateAttributes =  $this->data + ['categories_id'=>[0,1,2]];
        $hasError = false;
        try {
            $video->update($videoUpdateAttributes);
        }catch (QueryException $exception){
            $this->assertDatabaseHas('videos',['title'=>$oldTitle]);
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testSyncCategories()
    {

        $categoriesId = factory(Category::class,3)->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, ['categories_id'=>$categoriesId[0]]);

        $this->assertDatabaseHas('category_video',
            [
                'category_id'=>$categoriesId[0],
                'video_id'=>$video->id
            ]);

        Video::handleRelations($video, ['categories_id'=>[$categoriesId[1],$categoriesId[2]]]);

        $this->assertDatabaseMissing('category_video',[
            'video_id'=>$video->id,
            'category_id'=>$categoriesId[0],
        ]);

        $this->assertDatabaseHas('category_video',[
            'video_id'=>$video->id,
            'category_id'=>$categoriesId[1]
        ]);

        $this->assertDatabaseHas('category_video',[
            'video_id'=>$video->id,
            'category_id'=>$categoriesId[2]
        ]);
    }

    public function testSyncGenres()
    {

        $genresId = factory(Genre::class,3)->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, ['genres_id'=>$genresId[0]]);

        $this->assertDatabaseHas('genre_video',
            [
                'genre_id'=>$genresId[0],
                'video_id'=>$video->id
            ]);

        Video::handleRelations($video, ['genres_id'=>[$genresId[1],$genresId[2]]]);

        $this->assertDatabaseMissing('genre_video',[
            'video_id'=>$video->id,
            'genre_id'=>$genresId[0],
        ]);

        $this->assertDatabaseHas('genre_video',[
            'video_id'=>$video->id,
            'genre_id'=>$genresId[1]
        ]);

        $this->assertDatabaseHas('genre_video',[
            'video_id'=>$video->id,
            'genre_id'=>$genresId[2]
        ]);
    }

}
