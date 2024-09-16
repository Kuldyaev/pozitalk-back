<?php

namespace Tests\Feature\Academy\Course;

use App\Models\AcademyCourseItem;
use Vi\Models\Academy\AcademyCourseItemMoment;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAcademyCourseItemMomentTest extends TestCase
{
    use WithFaker;

    public function createMomentForCourseItem(AcademyCourseItem $item): AcademyCourseItemMoment
    {
        return $item->moments()->create([
            'title' => $this->faker->title(),
            'caption' => $this->faker->paragraph(1),
            'link' => $this->faker->url(),
        ]);
    }

    public function testAdminCreateMomentForCourseItem(): void
    {
        $this->setUpFaker();

        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();

        $info = [
            'title' => $this->faker->title(),
            'caption' => $this->faker->paragraph(1),
            'link' => $this->faker->url(),
        ];

        $response = $this->postJson(
            route('academy.course.item.moment.store', ['item' => $item->id]),
            $info,
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        $response->assertStatus(201);
        AcademyCourseItemMoment::where('id', $response->json('data.id'))->delete();
    }

    public function testAdminIndexMomentsForCourseItem(): void
    {
        $this->setUpFaker();
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();

        $createdMoments = collect();

        for ($i = 0; $i < 5; $i++) {
            $createdMoments->push($this->createMomentForCourseItem($item));
        }

        $response = $this->getJson(
            route('academy.course.item.moment.index', ['item' => $item->id]),
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        if ($response->isServerError()) {
            dump($response->getContent());
        }
        $response->assertStatus(200);

        $response->assertJsonCount($item->moments->count(), 'data');

        AcademyCourseItemMoment::whereIn('id', $createdMoments->pluck('id'))->delete();
    }

    public function testAdminShowMomentForCourseItem(): void
    {
        $this->setUpFaker();
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();
        $moment = $this->createMomentForCourseItem($item);

        $response = $this->getJson(
            route(
                'academy.course.item.moment.show',
                ['item' => $item->id, 'moment' => $moment->id]
            ),
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        if ($response->isServerError()) {
            dump($response->getContent());
        }
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'title', 'caption', 'link']]);

        AcademyCourseItemMoment::where('id', $moment->id)->delete();
    }

    public function testAdminUpdateMomentForCourseItem(): void
    {
        $this->setUpFaker();
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();
        $moment = $this->createMomentForCourseItem($item);

        $newData = [
            'title' => $this->faker->title(),
            'caption' => $this->faker->word(),
            'link' => $this->faker->url(),
        ];

        $response = $this->patchJson(
            route(
                'academy.course.item.moment.update',
                ['item' => $item->id, 'moment' => $moment->id]
            ),
            $newData,
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        if ($response->isServerError()) {
            dump($response->getContent());
        }

        $response->assertStatus(200);

        $response->assertJson(['data' => $newData]);

        AcademyCourseItemMoment::where('id', $moment->id)->delete();
    }

    public function testAdminDeleteMomentForCourseItem(): void
    {
        $this->setUpFaker();
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();
        $moment = $this->createMomentForCourseItem($item);

        $response = $this->deleteJson(
            route(
                'academy.course.item.moment.destroy',
                ['item' => $item->id, 'moment' => $moment->id]
            ),
            [],
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        if ($response->isServerError()) {
            dump($response->getContent());
        }
        $response->assertStatus(200);
    }
}