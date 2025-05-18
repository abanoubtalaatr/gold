<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Resources\FaqResource;
use App\Repositories\FaqRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\AppBaseController;

class FaqController extends AppBaseController
{
    public $repository;

    public function __construct(FaqRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->sendResponse(FaqResource::collection($this->repository->getAllApis()), 'items retrieved successfully');
    }
}
