<?php

namespace Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Exceptions\TestException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();
        factory(Category::class)->create();
        factory(Genre::class)->create();
        $this->sendData = [
            'title'=>'title',
            'description'=>'description',
            'year_launched'=>2010,
            'rating'=>Video::RATING_LIST[0],
            'duration'=>90,
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('api.videos.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('api.videos.show', ['video'=>$this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());

    }

    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationCategoriesField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a',256)
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string',['max'=>255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string',['max'=>255]);
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's'
        ];
        $this->assertInvalidationInStoreAction($data,'integer');
        $this->assertInvalidationInUpdateAction($data,'integer');
    }

    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'year_launched' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpenedField()
    {
        $data = [
            'opened' => 's'
        ];

        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating'=>0
        ];

        $this->assertInvalidationInStoreAction($data,'in');
        $this->assertInvalidationInUpdateAction($data,'in');
    }
    public function testSave()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $category->genres()->attach($genre->id);

        $data = [
            [
                'send_data'=>$this->sendData + ['categories_id'=>[$category->id],'genres_id'=>[$genre->id]],
                'test_data'=>$this->sendData
            ],
            [
                'send_data'=>$this->sendData + ['categories_id'=>[$category->id],'genres_id'=>[$genre->id],'opened' => true],
                'test_data'=>$this->sendData
            ],
            [
                'send_data'=> $this->sendData + ['categories_id'=>[$category->id],'genres_id'=>[$genre->id],'rating' => Video::RATING_LIST[1]],
                'test_data'=> $this->sendData + ['rating' => Video::RATING_LIST[1]]
            ],
        ];
        foreach ($data as $v){
            $response = $this->assertStore(
                $v['send_data'],
                $v['test_data'] + ['deleted_at'=>null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);

            $response = $this->assertUpdate(
                $v['send_data'],
                $v['test_data'] + ['deleted_at'=>null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);
        }

    }

    public function testRollbackStore(){
        $controller = \Mockery::mock(VideoController::class)
            ->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $request = \Mockery::mock(Request::class);

        $controller->shouldReceive('handleRelations')
            ->once()
            // Verify why didint get TestException
            ->andThrow(new \Exception());

        try {
            $controller->store($request);
        }catch (\Exception $exception){
            $this->assertCount(1, Video::all());
        }
    }

    public function testDelete()
    {
        $response = $this->json('DELETE',route('api.videos.destroy',['video'=>$this->video->id]));
        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertEmpty(Video::find($this->video)->toArray());
        $this->assertNotNull(Video::withoutTrashed()->find($this->video));
    }

    public function routeStore()
    {
        return route('api.videos.store');
    }

    public function routeUpdate()
    {
        return route('api.videos.update',['video'=>$this->video->id]);
    }

    public function model()
    {
        return Video::class;
    }
}
