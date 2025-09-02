<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Tests\Traits\TestDates;

class GenreUnitTest extends TestCase
{
    use TestDates;
    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function getModel()
    {
        return $this->genre;
    }

    public function testFillable()
    {
        $fillable =  [
            'name',
            'is_active'
        ];

        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    public function testUseRequiredTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];

        $classUsedClasses = array_keys(class_uses(Genre::class));

        $this->assertEquals($traits,$classUsedClasses);
    }

    public function testKeyType()
    {
        $keyType = 'string';
        $this->assertEquals($keyType, $this->genre->getKeyType());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->genre->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at','updated_at','deleted_at'];
        foreach ($dates as $date){
            $this->assertContains($date, $this->genre->getDates());
        }
        $this->assertCount(count($dates),$this->genre->getDates());
    }
}
