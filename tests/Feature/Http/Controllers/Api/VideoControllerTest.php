<?php

namespace Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
        $this->sendData = [
            'title'=>'title',
            'description'=>'description',
            'year_launched'=>2010,
            'rating'=>Video::RATING_LIST[0],
            'duration'=>90
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
            'duration' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
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
        $data = [
            [
                'send_data'=>$this->sendData,
                'test_data'=>$this->sendData+ ['opened' => false]
            ],
            [
                'send_data'=>$this->sendData + ['opened' => true],
                'test_data'=>$this->sendData+ ['opened' => true]
            ],
            [
                'send_data'=> $this->sendData + ['rating' => Video::RATING_LIST[1]],
                'test_data'=> $this->sendData + ['rating' => Video::RATING_LIST[1]]
            ],
        ];
        foreach ($data as $k => $v){
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
