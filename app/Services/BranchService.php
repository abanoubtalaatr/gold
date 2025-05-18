<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchService
{
    public function list(int $vendorId, array $filters = [])
    {
        $query = Branch::query()
            ->where('vendor_id', $vendorId)
            ->with('city')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('city', function ($q) use ($filters) {
                      $q->where('name', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        return $query->paginate(10)->withQueryString();
    }

    public function canDelete(Branch $branch): bool
    {
        // Example: Prevent deletion if branch has active appointments
        return !$branch->appointments()->where('status', 'active')->exists();
    }
}