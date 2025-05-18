<?php

namespace App\Repositories;

use App\Models\Faq;
use Str;

class FaqRepository
{
    protected $model;

    public function __construct(Faq $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('created_at', 'desc')->paginate();
    }

    public function getAllApis()
    {
        return $this->model->where('is_active', 1)->orderBy('sort_order')->get();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        $faq = new Faq();
        foreach ($data['translations'] as $locale => $translation) {
            $faq->translateOrNew($locale)->question = $translation['question'];
            $faq->translateOrNew($locale)->answer = $translation['answer'];
        }
        $faq->sort_order = $data['sort_order'];
        $faq->save();
        return $faq;
    }

    public function update(Faq $faq, array $data)
    {

        foreach ($data['translations'] as $locale => $translation) {
            $faq->translateOrNew($locale)->question = $translation['question'];
            $faq->translateOrNew($locale)->answer = $translation['answer'];
        }

        $faq->sort_order = $data['sort_order'];

        $faq->save();

        return $faq;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function toggleStatus($faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);
        return $faq;
    }


    public function find($id)
    {
        return $this->model->with('translations')->findOrFail($id);
    }
}
