<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlidersRequest;
use App\Http\Requests\Admin\SystemSettingsRequest;
use App\Models\SystemSetting;
use App\Models\HomeSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SystemSettingsController extends Controller
{
    // Show system settings form
    public function index()
    {
        $settings = SystemSetting::firstOrNew();
        $sliders = HomeSlider::orderBy('display_order')->get();

        return Inertia::render('Admin/SystemSettings/Index', [
            'settings' => $settings,
            'sliders' => $sliders,
        ]);
    }

    // Update system settings
    public function updateSettings(SystemSettingsRequest $request)
    {
        $validated = $request->validated();

        SystemSetting::updateOrCreate(['id' => 1], $validated);

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }

    // Store new slider
    public function storeSlider(SlidersRequest $request)
    {
        $validated = $request->validated();

        $path = $request->file('image')->store('sliders', 'public');
        $validated['image_path'] = $path;
        HomeSlider::create($validated);

        return redirect()->back()->with('success', 'Slider added successfully.');
    }

    // Update slider
    public function updateSlider(SlidersRequest $request, HomeSlider $slider)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($slider->image_path);
            $path = $request->file('image')->store('sliders', 'public');
            $validated['image_path'] = $path;
        }

        $slider->update($validated);

        return redirect()->back()->with('success', 'Slider updated successfully.');
    }

    // Delete slider
    public function destroySlider(HomeSlider $slider)
    {
        Storage::disk('public')->delete($slider->image_path);
        $slider->delete();

        return redirect()->back()->with('success', 'Slider deleted successfully.');
    }
}
