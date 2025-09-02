<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;

class GenreControllerStub extends BasicCrudController
{
    protected function model()
    {
        return GenreControllerStub::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255'
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255'
        ];
    }
}
