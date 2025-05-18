<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\BranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function __construct(protected BranchService $branchService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'services', 'working_days']);
        $branches = $this->branchService->list($request->user()->id, $filters);

        return Inertia::render('Vendor/Branches/Index', [
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return Inertia::render('Vendor/Branches/Create');
    }

    public function store(BranchRequest $request)
    {
        $this->branchService->store($request->validated(), $request->user()->id);

        return redirect()->route('vendor.branches.index')
            ->with('success', __('Branch added successfully.'));
    }

    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);

        return Inertia::render('Vendor/Branches/Edit', [
            'branch' => $branch->load('images'),
        ]);
    }

    public function update(BranchRequest $request, Branch $branch)
    {
        $this->authorize('update', $branch);
        
        $this->branchService->update($branch, $request->validated());

        return redirect()->route('vendor.branches.index')
            ->with('success', __('Branch details updated successfully.'));
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete', $branch);

        if (!$this->branchService->canDelete($branch)) {
            return back()->with('error', __('This branch cannot be deleted due to active appointments.'));
        }

        $this->branchService->delete($branch);

        return back()->with('success', __('Branch deleted successfully.'));
    }

    public function toggleStatus(Branch $branch)
    {
        $this->authorize('update', $branch);
        
        $this->branchService->toggleStatus($branch);

        return back()->with('success', __('Branch status updated successfully.'));
    }
} 