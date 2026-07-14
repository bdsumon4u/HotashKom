<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Traits\ImageUploader;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    use ImageUploader;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __invoke(SettingRequest $request, SettingRepository $settingRepo)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        if ($request->isMethod('GET')) {
            return $this->view(Setting::array());
        }

        $data = $request->validated();

        if ($request->get('tab') === 'delivery') {
            $defaultIndex = (int) ($data['default_delivery_area'] ?? 0);
            $deliveryAreas = $data['delivery_areas'] ?? [];
            foreach ($deliveryAreas as $index => &$area) {
                $area['is_default'] = ($index === $defaultIndex);
            }
            unset($data['default_delivery_area']);
            $data['delivery_areas'] = $deliveryAreas;

            // Reconstruct delivery_charge and default_area for backwards compatibility
            $insideArea = collect($deliveryAreas)->first(fn ($a) => Str::contains(Str::lower($a['name'] ?? ''), 'inside') || Str::contains(Str::lower($a['name'] ?? ''), 'ঢাকা শহর') || Str::contains(Str::lower($a['name'] ?? ''), 'ঢাকা সিটি'));
            $insideArea ??= $deliveryAreas[0] ?? null;

            $outsideArea = collect($deliveryAreas)->first(fn ($a) => Str::contains(Str::lower($a['name'] ?? ''), 'outside') || Str::contains(Str::lower($a['name'] ?? ''), 'বাহির'));
            $outsideArea ??= collect($deliveryAreas)->first(fn ($a) => ! $insideArea || ($a['name'] !== ($insideArea['name'] ?? '')));
            $outsideArea ??= $deliveryAreas[1] ?? $deliveryAreas[0] ?? null;

            $data['delivery_charge'] = [
                'inside_dhaka' => (int) ($insideArea['cost'] ?? 60),
                'outside_dhaka' => (int) ($outsideArea['cost'] ?? 120),
            ];

            $data['default_area'] = [
                'inside' => (bool) ($insideArea['is_default'] ?? false),
                'outside' => (bool) ($outsideArea['is_default'] ?? false),
            ];
        }

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
