<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuItemSortController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function __invoke(Request $request, Menu $menu)
    {
        foreach ($request->positions as $position) {
            $menu->menuItems()->find($position[0])->update(['order' => $position[1]]);
        }

        return response()->json(['success', 'SUCCESS']);
    }
}
