<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Rules\RelationBetweenVideoAndGenreRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
 * Auto commit - Padrão dos bd relacionais
 * Modo transação
 *  - begin transaction
 *  - transactions
 *  - commit - persists the data
 *  - rollback - cancel the transactions
 *  - savepoint - save a specific state to retry if you want (most used in dbms)
 */


class VideoController extends BasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:'.implode(',',Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => ['required','array','exists:categories,id', new RelationBetweenVideoAndGenreRule],
            'genres_id' => ['required','array','exists:genres,id', new RelationBetweenVideoAndGenreRule],
        ];
    }

    protected function model()
    {
        return Video::class;
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
        //Eager loading
        /*
        $video = new Video();
        return $video->with('categories','genres')->get();
        */
        return parent::index();
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

    public function update(Request $request, string $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request,$this->rulesUpdate());
        $self = $this;
        $obj = DB::transaction(function () use ($request, $validatedData, $self, $obj){
            $obj->update($validatedData);
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
        $video->genres()->sync($request->get('genres_id'));
    }


}
