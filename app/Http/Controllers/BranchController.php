<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService
    ) {
        $this->authorizeResource(Branch::class);
    }

    public function index(): Response
    {
        $branches = $this->branchService->list(auth()->id());

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Branches/Create');
    }

    public function store(BranchRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['vendor_id'] = auth()->id();

        $this->branchService->create($data, $request->file('images', []));

        return redirect()
            ->route('branches.index')
            ->with('success', __('branches.messages.created'));
    }

    public function edit(Branch $branch): Response
    {
        return Inertia::render('Branches/Edit', [
            'branch' => $branch->load('images'),
        ]);
    }

    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->branchService->update($branch, $request->validated(), $request->file('images', []));

        return redirect()
            ->route('branches.index')
            ->with('success', __('branches.messages.updated'));
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->branchService->delete($branch);

        return redirect()
            ->route('branches.index')
            ->with('success', __('branches.messages.deleted'));
    }

    public function toggleStatus(Branch $branch): RedirectResponse
    {
        $this->authorize('update', $branch);
        
        $this->branchService->toggleStatus($branch);

        return back()->with('success', __('branches.messages.status_updated'));
    }
} 