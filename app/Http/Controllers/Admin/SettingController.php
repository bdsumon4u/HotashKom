<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Traits\ImageUploader;

class SettingController extends Controller
{
    use ImageUploader;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SettingRequest $request, SettingRepository $settingRepo)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        if ($request->isMethod('GET')) {
            return $this->view(Setting::array());
        }

        $data = $request->validated();

        if (isset($data['logo'])) {
            foreach ($data['logo'] as $type => $file) {
                $data['logo'][$type] = $this->upload($file, $type);
            }
        }

        $settingRepo->setMany($data);

        return back()->withSuccess('Settings Has Been Updated.');
    }

    protected function upload($file, $type)
    {
        if ($type == 'desktop') {
            return $this->uploadImage($file, [
                'dir' => 'logo',
                'resize' => false,
                // 'width' => config('services.logo.desktop.width', 260),
                // 'height' => config('services.logo.desktop.height', 54),
            ]);
        }

        if ($type == 'mobile') {
            return $this->uploadImage($file, [
                'dir' => 'logo',
                'resize' => false,
                // 'width' => config('services.logo.mobile.width', 192),
                // 'height' => config('services.logo.mobile.height', 40),
            ]);
        }

        if ($type == 'login') {
            return $this->uploadImage($file, [
                'dir' => 'logo',
                'resize' => false,
                // 'width' => config('services.logo.desktop.width', 260),
                // 'height' => config('services.logo.desktop.height', 54),
            ]);
        }

        if ($type == 'favicon') {
            return $this->uploadImage($file, [
                'dir' => 'logo',
                'resize' => false,
                'width' => config('services.logo.favicon.width', 56),
                'height' => config('services.logo.favicon.height', 56),
            ]);
        }
    }
}
