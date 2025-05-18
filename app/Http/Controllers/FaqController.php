<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Inertia\Inertia;
use App\Http\Requests\FaqRequest;
use App\Repositories\FaqRepository;

class FaqController extends Controller
{
    public function __construct(protected FaqRepository $repository)
    {
    }

    public function index()
    {
        $faqs = $this->repository->getAll();
        return Inertia::render('Faqs/Index', compact('faqs'));
    }

    public function create()
    {
        $max_order = Faq::max('sort_order') + 1;

        return Inertia::render('Faqs/Create', compact('max_order'));
    }

    public function store(FaqRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('faqs.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit($id)
    {
        $faq = $this->repository->find($id);
        return Inertia::render('Faqs/Edit', compact('faq'));
    }

    public function update(FaqRequest $request, $id)
    {
        $faq = $this->repository->find($id);
        $this->repository->update($faq, $request->validated());
        return redirect()->route('faqs.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('faqs.index')
            ->with('success', __('messages.deleted_successfully'));
    }

    public function activate($id)
    {
        $faq = $this->repository->find($id);
        $this->repository->toggleStatus($faq);
        return back()->with('success', __('messages.updated_successfully'));
    }
}