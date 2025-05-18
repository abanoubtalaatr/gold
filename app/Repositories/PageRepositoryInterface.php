<?php

namespace App\Repositories;

interface PageRepositoryInterface
{
    public function getAll();
    public function getBySlug(string $slug, string $locale);
    public function updatePage(array $data, $pageId);
}
