<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ServiceRequest;
use App\Models\Branch;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService
    ) {
        $this->authorizeResource(Service::class);
    }

    public function index(): Response
    {
        $services = $this->serviceService->list(Auth::id());

        return Inertia::render('Vendor/Services/Index', [
            'services' => $services,
        ]);
    }

    public function create(): Response
    {
        $branches = Branch::where('vendor_id', Auth::id())->get();

        return Inertia::render('Vendor/Services/Create', [
            'branches' => $branches,
            'durations' => collect(Service::DURATIONS)->mapWithKeys(function ($duration) {
                return [$duration => $duration . ' ' . trans_choice('common.minutes', $duration)];
            })->all(),
            'types' => [
                Service::TYPE_CUPPING => __('services.types.cupping'),
                Service::TYPE_MASSAGE => __('services.types.massage'),
            ],
            'locationTypes' => [
                Service::LOCATION_HOME => __('services.location_types.home'),
                Service::LOCATION_CENTER => __('services.location_types.center'),
                Service::LOCATION_BOTH => __('services.location_types.both'),
            ],
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['vendor_id'] = Auth::id();

        $this->serviceService->create($data, $request->file('images', []));

        return redirect()
            ->route('vendor.services.index')
            ->with('success', __('services.messages.created'));
    }

    public function edit(Service $service): Response
    {
        $branches = Branch::where('vendor_id', Auth::id())->get();

        return Inertia::render('Vendor/Services/Edit', [
            'service' => $service->load(['images', 'branches']),
            'branches' => $branches,
            'durations' => Service::DURATIONS,
            'types' => [
                Service::TYPE_CUPPING => __('Cupping'),
                Service::TYPE_MASSAGE => __('Massage'),
            ],
            'locationTypes' => [
                Service::LOCATION_HOME => __('Home'),
                Service::LOCATION_CENTER => __('Center'),
                Service::LOCATION_BOTH => __('Both'),
            ],
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->validated();
        
        $this->serviceService->update($service, $data, $request->file('images', []));

        return redirect()
            ->route('vendor.services.index')
            ->with('success', __('services.messages.updated'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        try {
            $this->serviceService->delete($service);
            $message = ['success' => __('services.messages.deleted')];
        } catch (\Exception $e) {
            $message = ['error' => $e->getMessage()];
        }

        return redirect()
            ->route('vendor.services.index')
            ->with($message);
    }

    public function toggleStatus(Service $service): RedirectResponse
    {
        $this->authorize('update', $service);
        
        $this->serviceService->toggleStatus($service);

        return redirect()
            ->route('vendor.services.index')
            ->with('success', __('services.messages.status_updated'));
    }
} 