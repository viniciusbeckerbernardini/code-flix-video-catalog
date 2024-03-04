<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'is_active' => 'boolean'
        ];
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }
}
