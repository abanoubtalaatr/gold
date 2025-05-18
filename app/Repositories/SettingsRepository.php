<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;

class SettingsRepository extends BaseRepository
{

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Setting::class;
    }

    public function getAllGroupedByGroup()
    {
        $settings = Setting::get();
        return $settings->groupBy('group');
    }

    public function getByGroup($group)
    {
        return Setting::where('group', $group)->get();
    }

    public function getAllSettings()
    {
        return $this->model->getFromCache();
    }

    public function getByKey(string $key)
    {
        return $this->model->getFromCache([$key]);
    }

    public function updateSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                if ($setting->type === 'file' && request()->hasFile($key)) {
                    $file = request()->file($key);
                    $path = $file->store('settings', 'public');

                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    $value = $path;
                }
                $setting->update(['value' => $value]);
            }
        }
    }
}