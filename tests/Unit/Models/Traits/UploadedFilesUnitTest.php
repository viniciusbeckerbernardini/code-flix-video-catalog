<?php

namespace Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\Traits\TestDates;

class UploadedFilesUnitTest extends TestCase
{
    use TestDates;
    private $uploadedFile;

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
}
