<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Repositories\SettingsRepository;
use App\Http\Requests\UpdateSettingsRequest;

class SettingsController extends Controller
{
    protected $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    public function index()
    {
        $settings = $this->settingsRepository->getAllGroupedByGroup();
        return Inertia::render('Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $this->settingsRepository->updateSettings($request->validated());

        return redirect()->route('settings.index')->with('success', __('messages.data_updated_successfully'));
    }
}