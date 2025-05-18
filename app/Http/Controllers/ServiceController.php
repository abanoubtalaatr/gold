namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
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
        $this->authorizeResource(Service::class, 'service');
    }

    public function index(): Response
    {
        $services = Service::query()
            ->with(['images', 'branches'])
            ->byVendor(Auth::id())
            ->latest()
            ->get()
            ->transform(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'type' => $service->type,
                'type_text' => $service->type_text,
                'price' => $service->formatted_price,
                'duration' => $service->formatted_duration,
                'location_type' => $service->location_type,
                'location_text' => $service->location_text,
                'rating' => $service->rating,
                'rating_count' => $service->rating_count,
                'is_active' => $service->is_active,
                'thumbnail' => $service->images->first()?->url,
                'branches' => $service->branches->map(fn ($branch) => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                ]),
            ]);

        return Inertia::render('Services/Index', [
            'services' => $services,
            'types' => Service::TYPES,
            'locations' => Service::LOCATIONS,
            'durations' => Service::DURATIONS,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Services/Create', [
            'types' => Service::TYPES,
            'locations' => Service::LOCATIONS,
            'durations' => Service::DURATIONS,
            'branches' => Auth::user()->branches->map(fn ($branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
            ]),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $this->serviceService->create(
            $request->validated(),
            $request->file('images', [])
        );

        return redirect()
            ->route('services.index')
            ->with('success', __('services.created_successfully'));
    }

    public function edit(Service $service): Response
    {
        $service->load(['images', 'branches']);

        return Inertia::render('Services/Edit', [
            'service' => [
                'id' => $service->id,
                'type' => $service->type,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'available_sessions_per_day' => $service->available_sessions_per_day,
                'duration' => $service->duration,
                'max_concurrent_requests' => $service->max_concurrent_requests,
                'location_type' => $service->location_type,
                'is_active' => $service->is_active,
                'images' => $service->images->map(fn ($image) => [
                    'id' => $image->id,
                    'url' => $image->url,
                    'order' => $image->order,
                ]),
                'branches' => $service->branches->pluck('id'),
            ],
            'types' => Service::TYPES,
            'locations' => Service::LOCATIONS,
            'durations' => Service::DURATIONS,
            'branches' => Auth::user()->branches->map(fn ($branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
            ]),
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $this->serviceService->update(
            $service,
            $request->validated(),
            $request->file('images', []),
            $request->input('removed_images', [])
        );

        return redirect()
            ->route('services.index')
            ->with('success', __('services.updated_successfully'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        try {
            $this->serviceService->delete($service);

            return redirect()
                ->route('services.index')
                ->with('success', __('services.deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->route('services.index')
                ->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Service $service): RedirectResponse
    {
        $this->authorize('toggleStatus', $service);

        $this->serviceService->toggleStatus($service);

        return redirect()
            ->route('services.index')
            ->with('success', __('services.status_updated_successfully'));
    }
} 