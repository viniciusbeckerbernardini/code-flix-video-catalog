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
            'is_active' => 'boolean',
            'genres_id' => 'required|array|exists:genres,id'
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

    public function index()
    {
        $category = new Category();
        return $category->with('genres')->get();
    }
}
