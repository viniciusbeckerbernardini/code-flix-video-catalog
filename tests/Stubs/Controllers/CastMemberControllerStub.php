<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\CastMemberStub;

class CastMemberControllerStub  extends BasicCrudController
{
    protected function model()
    {
        return CastMemberStub::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => 'int|max:2'
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => 'int|max:2'
        ];
    }
}
