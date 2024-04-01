<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenreController extends BasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:categories,id',
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

    public function index()
    {
        $genre = new Genre();
        return $genre->with('categories')->get();
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

    protected function handleRelations($video, Request $request)
    {
        // Attach to relate models many to many
        //$obj->categories()->attatch();
        // Dettach to remove related models many to many
        //$obj->categories()->dettach();
        $video->categories()->sync($request->get('categories_id'));
    }
    public function show(string $id)
    {
        return $this->model()::where('id',$id)->with('categories')->firstOrFail();
    }
}
