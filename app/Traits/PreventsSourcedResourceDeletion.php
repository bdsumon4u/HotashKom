<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait PreventsSourcedResourceDeletion
{
    /**
     * Prevent deletion of sourced resources
     *
     * @return \Illuminate\Http\RedirectResponse|true
     */
    protected function preventSourcedResourceDeletion(Model $model)
    {
        if ($model->source_id !== null) {
            $resourceName = class_basename($model);

            return back()->with('danger', "This {$resourceName} cannot be deleted because it is sourced from Oninda.");
        }

        return true;
    }
}
