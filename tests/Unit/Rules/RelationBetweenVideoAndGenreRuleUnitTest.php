<?php

namespace Tests\Unit\Rules;

use App\Rules\RelationBetweenVideoAndGenreRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ReflectionClass;
use Tests\TestCase;

class RelationBetweenVideoAndGenreRuleUnitTest extends TestCase
{
    private $rule;
    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new RelationBetweenVideoAndGenreRule();
    }
    public function testRuleImplementation(){
        $rfClass = new ReflectionClass($this->rule);
        $interfaceNames = $rfClass->getInterfaceNames();
        $this->assertTrue(in_array('Illuminate\Contracts\Validation\Rule',$interfaceNames));
    }

    public function testEmpty()
    {
        $request = $this->createRequestMock();
        $request->shouldReceive('input')->with(['genres_id','categories_id'])->andReturn([null,null]);
        $empty = $this->rule->passes('','');
        $this->assertFalse($empty);
    }
    public function testGenreIdEmpty()
    {
        $request = $this->createRequestMock();
        $request->shouldReceive('input')->with(['genres_id'])->andReturn([null]);
        $empty = $this->rule->passes('','');
        $this->assertFalse($empty);
    }

    public function testCategoryIdEmpty()
    {
        $request = $this->createRequestMock();
        $request->shouldReceive('input')->with(['categories_id'])->andReturn([null]);
        $empty = $this->rule->passes('','');
        $this->assertFalse($empty);
    }

    public function testValidated()
    {
        $requestMock = $this->createRequestMock();
        $requestMock->shouldReceive('input')->with('genres_id')->andReturn([1]);
        $requestMock->shouldReceive('input')->with('categories_id')->andReturn([1]);
        $this->app->instance('request', $requestMock);

        $rule = $this->createRelationBetweenVideoAndGenreRuleMock();
        $rule->shouldReceive('verifyRelationBetweenVideoAndGenre')->andReturn(true);

        $this->assertTrue($rule->passes('',''));
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
