<?php

namespace Feature\Rules;

use App\Models\Category;
use App\Models\Genre;
use App\Rules\RelationBetweenVideoAndGenreRule;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\TestCase;

class RelationBetweenVideoAndGenreRuleTest extends TestCase
{
    use DatabaseMigrations;
    private $rule;
    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new RelationBetweenVideoAndGenreRule();
    }

    public function testPassesIsNotValid()
    {
        $requestMock = $this->createRequestMock();
        $requestMock->shouldReceive('input')->with('genres_id')->andReturn([1]);
        $requestMock->shouldReceive('input')->with('categories_id')->andReturn([1]);
        $this->app->instance('request', $requestMock);
        $this->assertFalse($this->rule->passes('',''));
    }


    public function testPassesIsValid()
    {
        $categoryId = factory(Category::class)->create()->id;
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoryId);
        $genreId = $genre->id;

        $requestMock = $this->createRequestMock();
        $requestMock->shouldReceive('input')->with('genres_id')->andReturn([$genreId]);
        $requestMock->shouldReceive('input')->with('categories_id')->andReturn([$categoryId]);
        $this->app->instance('request', $requestMock);
        $this->assertTrue($this->rule->passes('',''));
    }

    public function createRequestMock()
    {
        return \Mockery::mock(Request::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function createRelationBetweenVideoAndGenreRuleMock()
    {
        return \Mockery::mock(RelationBetweenVideoAndGenreRule::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}
