<?php

namespace Tests\Feature\Academy;

use App\Models\AcademyCourseCategory;
use Tests\TestCase;

class AcademyCategoriesLatestTest extends TestCase
{

    public function testAcademyCategoriesLatest(): void
    {

        $user = $this->createAndLoginUser();
        $categories = AcademyCourseCategory::inRandomOrder()->limit(4)->get();
        $categoriesIds = [];
        foreach ($categories as $category) {
            $catRes = $this->getJson(
                route('api.v2.academy.categories.show', ['category' => $category->id]),
            )
                // ->assertSuccessful();
;
            if ($catRes->isServerError()) {
                dump($catRes->getContent());
            }
            $categoriesIds[] = $category->id;
        }

        $response = $this->getJson(route('api.v2.academy.categories.latest'), );

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');

        $resCategories = $response->json('data');
        $categoriesIds = array_reverse($categoriesIds);

        foreach ($resCategories as $categoryKey => $category) {
            $this->assertEquals($category['id'], $categoriesIds[$categoryKey]);
        }
    }

}
