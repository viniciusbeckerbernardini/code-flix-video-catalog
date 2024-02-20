<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{

    protected abstract function model();

    protected abstract function rulesStore():array;

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $this->validate($request,$this->rulesStore());

        $model = $this->model()::create($request->all());
        $model->refresh();
        return $model;
    }

    public function show(Model $model)
    {
        return $model;
    }

    public function update(Request $request, Model $model)
    {
        $this->validate($request,$this->rules);
        $model->update($request->all());
        return $model;
    }

    public function destroy(Model $model)
    {
        $model->delete();
        return response()->noContent(); // 204 - No content
    }
}
