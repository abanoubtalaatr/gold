<?php

namespace App\Http\Controllers;
use App\Repositories\PageRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\UpdatePageRequest;
class PageWebController extends Controller
{
   protected $repository;

    public function __construct(PageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function edit($slug)
    {
        $page = $this->repository->getPageWithSections($slug);
        return inertia('Pages/Edit', ['page' => $page]);
    }

    public function update(UpdatePageRequest $request, $id)
    {

        $this->repository->updatePage($request, $id);
        return redirect()->back()->with('success', __('messages.data_updated_successfully'));
    }

}