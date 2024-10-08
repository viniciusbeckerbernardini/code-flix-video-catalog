<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;

trait UploadFiles
{
    protected abstract function uploadDir();
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }
    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }
}
