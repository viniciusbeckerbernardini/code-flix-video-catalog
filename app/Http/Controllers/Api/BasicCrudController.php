<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{

    protected abstract function model();

    protected abstract function rulesStore():array;

    protected abstract function rulesUpdate():array;

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request,$this->rulesStore());

        $model = $this->model()::create($validatedData);
        $model->refresh();
        return $model;
    }

    public function show(Model $model)
    {
        return $this->findOrFail($model->id);
    }

    public function update(Request $request, Model $model)
    {
        $obj = $this->findOrFail($model->id);
        $validatedData = $this->validate($request,$this->rulesUpdate());
        $obj->update($validatedData);
        $obj->refresh();
        return $obj;
    }

    public function destroy(Model $model)
    {
        $obj = $this->findOrFail($model->id);
        $obj->delete();
        return response()->noContent(); // 204 - No content
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName,$id)->firstOrFail();
    }
}
