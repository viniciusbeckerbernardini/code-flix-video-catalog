<?php

namespace Tests\Unit\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Stubs\Models\UploadFilesStub;

class UploadedFilesUnitTest extends TestCase
{
    private $uploadedFile;
    public static $fileFields = ['movie', 'trailer'];


    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadedFile = new UploadFilesStub();
    }

    public function testUploadFile()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->uploadedFile->uploadFile($file);
        Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadFiles()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');
        $this->uploadedFile->uploadFiles([$file,$file2]);
        Storage::assertExists("1/{$file->hashName()}");
        Storage::assertExists("1/{$file2->hashName()}");
    }

    public function testDeleteFile()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->uploadedFile->uploadFile($file);
        $this->uploadedFile->deleteFile($file->hashName());
        Storage::assertMissing("1/{$file->hashName()}");

        $file = UploadedFile::fake()->create('video2.mp4');
        $this->uploadedFile->uploadFile($file);
        $this->uploadedFile->deleteFile($file);
        Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFiles()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');
        $this->uploadedFile->deleteFiles([$file->hashName(),$file2]);
        Storage::assertMissing("1/{$file->hashName()}");
        Storage::assertMissing("1/{$file2->hashName()}");
    }

    public function testExtractFiles()
    {
        $attributes = [];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(0, $attributes);
        $this->assertCount(0, $files);

        $attributes = ['movie' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(1, $attributes);
        $this->assertCount(0, $files);

        $attributes = ['movie' => 'test', 'trailer' => UploadedFile::fake()->create('video.mp4')];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertCount(1, $files);

        $movie = UploadedFile::fake()->create('movie.mp4');
        $trailer = UploadedFile::fake()->create('trailer.mp4');
        $attributes = ['movie' => $movie, 'trailer' => $trailer];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertCount(2, $files);
        $this->assertEquals(['movie' => $movie->hashName(), 'trailer' => $trailer->hashName()], $attributes);
        $this->assertEquals([$movie,$trailer], $files);
    }
}
