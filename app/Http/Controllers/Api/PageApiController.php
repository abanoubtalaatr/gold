<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PageRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\PageResource;

class PageApiController extends Controller
{
    protected $repository;

    public function __construct(PageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }



    public function show($slug)
    {
        $locale = substr(request()->header('Accept-Language', 'en'), 0, 2);
        $page = $this->repository->getPageWithSections($slug, $locale);

        return new PageResource($page);
    }
}
