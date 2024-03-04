<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'description'=> 'nullable',
            'is_active' => 'boolean'
        ];
    }

    protected function model()
    {
        return Category::class;
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
