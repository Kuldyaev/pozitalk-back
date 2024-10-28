<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Application;

final class ApplicationBuilder
{
    private Builder $model;

    public function __construct()
    {
        $this->model = Application::query();
    }

    // public function getListAllergens()
    // {
    //     return $this->model
    //         ->get(['id', 'name']);
    // }

    // public function getListAllergensWithPagination(): LengthAwarePaginator
    // {
    //     $allergens = $this->model
    //         ->paginate(config('pagination.admin.allergens'));

    //     return $allergens;
    // }

    // public function getOneAllergenAdmin(Allergen $allergen)
    // {
    //     return $this->model->find($allergen->id);
    // }

    public function create(array $data): Application|bool
    {
        return Application::create($data);
    }

    // public function update(Allergen $allergen, array $data): bool
    // {
    //     return $allergen->fill($data)->save();
    // }

    public function delete(Application $application)
    {
        return $application->delete();
    }
}