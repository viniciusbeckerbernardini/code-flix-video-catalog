<?php

namespace Tests\Unit\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use App\Models\Video;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Tests\Traits\TestDates;

class VideoUnitTest extends TestCase
{
    use TestDates;

    private $video;
    private const TRAITS_METHODS = [
        'boot',
        'forceDelete',
        'performDeleteOnModel',
        'bootSoftDeletes',
        'initializeSoftDeletes',
        'runSoftDelete',
        'restore',
        'trashed',
        'restoring',
        'restored',
        'isForceDeleting',
        'getDeletedAtColumn',
        'getQualifiedDeletedAtColumn',
        'create',
        'update',
        'handleRelations',
        'uploadFiles',
        'uploadFile',
        'deleteFiles',
        'deleteFile',
        'extractFiles',
        'uploadDir'
    ];
    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    public function getModel()
    {
        return $this->video;
    }

    public function testFillable()
    {
        $fillable =  [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration'
        ];

        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testRatingList()
    {
        $ratingList = [
            'L',
            '10',
            '12',
            '14',
            '16',
            '18'
        ];

        $this->assertEquals($ratingList, Video::RATING_LIST);
    }

    public function testUseRequiredTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class,
            UploadFiles::class

        ];

        $classUsedClasses = array_keys(class_uses(Video::class));

        $this->assertEquals($traits,$classUsedClasses);
    }

    public function testRelationships()
    {
        $relationshipsBelongsToMany = [
            'categories',
            'genres'
        ];

        $videoReflection = new \ReflectionClass(Video::class);
        $methods = array();
        foreach ($videoReflection->getMethods() as $method) {
            if ($method->class == 'App\Models\Video') {
                $methods[] = $method->name;
            }
        }
        $methods = array_diff($methods,self::TRAITS_METHODS);
        foreach ($methods as $method){
            $methodReturn = $videoReflection->getMethod($method)->getReturnType()->getName();
            if($methodReturn === 'Illuminate\Database\Eloquent\Relations\BelongsToMany'){
                $this->assertContains($method,$relationshipsBelongsToMany);
            }
        }
    }


    public function testKeyType()
    {
        $keyType = 'string';
        $this->assertEquals($keyType, $this->video->getKeyType());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->video->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at','updated_at','deleted_at'];
        foreach ($dates as $date){
            $this->assertContains($date, $this->video->getDates());
        }
        $this->assertCount(count($dates),$this->video->getDates());
    }
}
