<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ImageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->ready()
            ->editColumn('action', fn(Image $image) => '<a href="'.route('admin.images.destroy', $image).'" data-action="delete" class="btn btn-danger">Delete</a>')
            ->make(true);
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function single(Request $request)
    {
        return $this->ready()->make(true);
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function multiple(Request $request)
    {
        return $this->ready()->make(true);
    }

    protected function ready()
    {
        return DataTables::of(request()->has('order') ? Image::query() : Image::latest('id'))
            ->addIndexColumn()
            ->addColumn('preview', fn(Image $image) => '<img class="select-image" src="'.asset($image->path).'" width="100" height="120" data-id="'.$image->id.'" data-src="'.asset($image->path).'" />')
            ->editColumn('filename', fn(Image $image) => '
                    <div>
                        <input type="text" value="'.$image->filename.'" class="mb-1 w-100" data-id="'.$image->id.'" onfocus="this.select()" disabled />
                        <div class="d-flex">
                            <button class="mr-1 input-group-append btn btn-sm btn-primary d-flex align-items-center img-rename" rename="'.$image->id.'"><i class="mr-1 fa fa-pencil-square-o"></i> <span>Rename</span></button>
                            <button class="ml-1 input-group-append btn btn-sm btn-primary d-flex align-items-center" data-clip="'.asset($image->path).'"><i class="mr-1 fa fa-clipboard"></i> <span>Copy Link</span></button>
                        </div>
                    </div>
                ')
            ->addColumn('action', fn(Image $image) => '<button class="p-1 select-image d-flex justify-content-center align-items-center text-dark" data-id="'.$image->id.'" data-src="'.asset($image->path).'">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </button>')
            ->rawColumns(['preview', 'filename', 'action']);
    }
}
