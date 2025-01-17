<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $viewPath = '';

    protected function view($data = [], $view = '', $mergeData = [])
    {
        if (empty($this->viewPath)) {
            $str = Str::beforeLast(Str::after(static::class, __NAMESPACE__.'\\'), 'Controller');
            $dir = array_map(fn($item) => Str::kebab($item), explode('\\', $str));
            $dir[] = Str::plural(array_pop($dir));
            $this->viewPath = implode('.', $dir);
        }

        empty($view) && (
            $view = debug_backtrace()[1]['function']
        );

        return view("{$this->viewPath}.{$view}", $data, $mergeData);
    }

    protected function delete()
    {
        $route = request()->route();
        $App = App::getNamespace();

        // Model Name & Key Name
        $Model = Str::beforeLast(Str::afterLast(static::class, '\\'), 'Controller');
        $keyName = Str::lower($Model);

        // Route Model
        if (($model = $route->parameter($keyName)) instanceof Model) {
            goto delete;
        }

        // Class & Object
        $class = class_exists($App.'Models\\'.$Model) ? $App.'Models\\'.$Model : $App.$Model;
        $object = new $class;

        // Model Binder
        $ModelBinder = data_get(
            $route->bindingFields(),
            $keyName,
            $object->getRouteKeyName()
        );

        // The Model
        $model = $class::where($ModelBinder, $model)->firstOrFail();

        delete:
        // Deleting The Model
        $model->delete();

        return back()->withSuccess("{$Model} Has Been Deleted.");
    }
}
