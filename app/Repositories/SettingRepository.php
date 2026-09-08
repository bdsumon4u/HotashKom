<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    public function set($name, $value)
    {
        return Setting::updateOrCreate(compact('name'), compact('value'));
    }

    public function setMany($data): void
    {
        isset($data['logo'])
        && $data = $this->mergeLogo($data);
        foreach ($data as $name => $value) {
            $this->set($name, $value);
            \cacheMemo()->forget(tenantCachePrefix().'settings:'.$name);
        }
        \cacheMemo()->forget(tenantCachePrefix().'settings');
    }

    public function get($name)
    {
        return Setting::where('name', $name)->get() ?? collect([]);
    }

    public function first($name)
    {
        return Setting::where('name', $name)->first() ?? new Setting;
    }

    public function mergeLogo($data)
    {
        $logo = (array) $this->first('logo')->value ?? [];
        foreach ($data['logo'] as $name => $value) {
            if (! empty($value)) {
                $logo[$name] = $value;
            }
        }
        $data['logo'] = $logo;

        return $data;
    }
}
