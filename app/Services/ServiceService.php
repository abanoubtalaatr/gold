<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    public function list(int $vendorId): Collection
    {
        return Service::with(['images', 'branches'])
            ->byVendor($vendorId)
            ->latest()
            ->get();
    }

    public function create(array $data, array $images = []): Service
    {
        return DB::transaction(function () use ($data, $images) {
            // Create service
            $service = Service::create([
                'vendor_id' => auth()->id(),
                'type' => $data['type'],
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'available_sessions_per_day' => $data['available_sessions_per_day'],
                'duration' => $data['duration'],
                'max_concurrent_requests' => $data['max_concurrent_requests'],
                'location_type' => $data['location_type'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Attach branches
            $service->branches()->attach($data['branches']);

            // Handle images
            $this->handleImages($service, $images);

            return $service->load('images', 'branches');
        });
    }

    public function update(Service $service, array $data, array $images = [], array $removedImages = []): Service
    {
        return DB::transaction(function () use ($service, $data, $images, $removedImages) {
            // Update service
            $service->update([
                'type' => $data['type'],
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'available_sessions_per_day' => $data['available_sessions_per_day'],
                'duration' => $data['duration'],
                'max_concurrent_requests' => $data['max_concurrent_requests'],
                'location_type' => $data['location_type'],
                'is_active' => $data['is_active'] ?? $service->is_active,
            ]);

            // Sync branches
            $service->branches()->sync($data['branches']);

            // Remove specified images
            if (!empty($removedImages)) {
                $this->removeImages($service, $removedImages);
            }

            // Add new images
            if (!empty($images)) {
                $this->handleImages($service, $images);
            }

            return $service->load('images', 'branches');
        });
    }

    public function delete(Service $service): bool
    {
        if ($service->hasActiveBookings()) {
            throw new \Exception(__('services.cannot_delete_active_bookings'));
        }

        return DB::transaction(function () use ($service) {
            // Delete all images from storage
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete the service (will cascade delete images and pivot records)
            return $service->delete();
        });
    }

    public function toggleStatus(Service $service): Service
    {
        $service->update(['is_active' => !$service->is_active]);
        return $service;
    }

    protected function handleImages(Service $service, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('services/' . $service->id, 'public');
                
                $service->images()->create([
                    'path' => $path,
                    'order' => $service->images()->count(),
                ]);
            }
        }
    }

    protected function removeImages(Service $service, array $imageIds): void
    {
        $images = $service->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        // Reorder remaining images
        $service->images()
            ->orderBy('order')
            ->get()
            ->each(function ($image, $index) {
                $image->update(['order' => $index]);
            });
    }

    protected function hasActiveBookings(Service $service): bool
    {
        // TODO: Implement booking check logic
        return false;
    }
} 