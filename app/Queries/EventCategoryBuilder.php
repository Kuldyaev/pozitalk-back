<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\EventCategory;

final class EventCategoryBuilder
{
    private Builder $model;

    public function __construct()
    {
        $this->model = EventCategory::query();
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

    public function create(array $data): EventCategory|bool
    {
        return EventCategory::create($data);
    }

    // public function update(Allergen $allergen, array $data): bool
    // {
    //     return $allergen->fill($data)->save();
    // }

    // public function delete(Allergen $allergen)
    // {
    //     return $allergen->delete();
    // }
}