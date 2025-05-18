<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BranchService
{
    public function list(int $vendorId): Collection
    {
        return Branch::where('vendor_id', $vendorId)
            ->with('images')
            ->get();
    }

    public function create(array $data, array $images = []): Branch
    {
        return DB::transaction(function () use ($data, $images) {
            $branch = Branch::create($data);
            
            if (!empty($images)) {
                $this->handleImages($branch, $images);
            }

            return $branch->load('images');
        });
    }

    public function update(Branch $branch, array $data, array $images = []): Branch
    {
        return DB::transaction(function () use ($branch, $data, $images) {
            $branch->update($data);
            
            if (!empty($images)) {
                $this->handleImages($branch, $images);
            }

            return $branch->load('images');
        });
    }

    public function delete(Branch $branch): bool
    {
        return DB::transaction(function () use ($branch) {
            // Delete associated images from storage
            foreach ($branch->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            return $branch->delete();
        });
    }

    public function toggleStatus(Branch $branch): Branch
    {
        $branch->update(['is_active' => !$branch->is_active]);
        return $branch;
    }

    protected function handleImages(Branch $branch, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('branches', 'public');
                $branch->images()->create([
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                ]);
            }
        }
    }

    public function canDelete(Branch $branch): bool
    {
        // Add logic to check if branch has active appointments
        // For now, returning true as appointment functionality is not implemented
        return true;
    }
} 