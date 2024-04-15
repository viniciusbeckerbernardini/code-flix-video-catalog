<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request)
    {
        $validatedData = $this->validate($request,$this->rulesStore());
        $self = $this;
        $obj = DB::transaction(function () use ($request, $validatedData, $self){
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj,$request);
            return $obj;
        });
        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request,$this->rulesStore());
        $self = $this;
        $obj = DB::transaction(function () use ($request, $validatedData, $self, $obj){
            $obj->update($validatedData);
            $self->handleRelations($obj,$request);
            return $obj;
        });
        $obj->refresh();
        return $obj;
    }

    protected function handleRelations($category, Request $request)
    {
        // Attach to relate models many to many
        //$obj->categories()->attatch();
        // Dettach to remove related models many to many
        //$obj->categories()->dettach();
        $category->genres()->sync($request->get('genres_id'));
    }

    public function show(string $id)
    {
        return $this->model()::where('id',$id)->with('genres')->firstOrFail();
    }
}
