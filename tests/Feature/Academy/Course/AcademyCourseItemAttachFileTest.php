<?php

namespace Tests\Feature\Academy\Course;

use Vi\Models\Academy\AcademyCourseItemFile;
use App\Models\AcademyCourseItem;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AcademyCourseItemAttachFileTest extends TestCase
{
    use WithFaker;

    private function createFileForItem(AcademyCourseItem $item, User $user): array
    {
        $file = UploadedFile::fake()->create('doc.pdf', 1324);
        $info = [
            'name' => $this->faker->name(),
            'type' => $this->faker->word()
        ];

        $response = $this->postJson(route('academy.course.item.file.store', ['item' => $item->id]), [
            'file' => $file,
        ] + $info, [
            'Authorization' => 'Bearer ' . $user->adminToken,
        ]);

        return [$response, $info, $file];
    }

    public function testAdminUploadFileForAcademyCourseItem(): void
    {
        $this->setUpFaker();

        $user = $this->createAdminUser();

        $item = AcademyCourseItem::inRandomOrder()->first();
        [$response, $info, $file] = $this->createFileForItem($item, $user);

        if ($response->isServerError()) {
            dump($response->getContent());
        }

        $response->assertStatus(201);

        AcademyCourseItemFile::where('id', $response->json('data.id'))->delete();
    }

    public function testListFilesForAcademyCourseItem(): void
    {
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();
        $startCount = $item->files()->count();
        $testIds = [];

        for ($i = 4; $i > 0; $i--) {
            [$response] = $this->createFileForItem($item, $user);
            $testIds[] = $response->json('data.id');
        }

        $response = $this->getJson(route('academy.course.item.file.index', ['item' => $item->id]), [
            'Authorization' => 'Bearer ' . $user->adminToken,
        ]);

        $response->assertStatus(200);

        $response->assertJsonCount($startCount + 4, 'data');

        foreach ($testIds as $fileId) {
            AcademyCourseItemFile::where('id', $fileId)->delete();
        }
    }

    public function testUpdateFilesForCourseItem(): void
    {
        $this->setUpFaker();
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();
        [$responseFile] = $this->createFileForItem($item, $user);
        $itemFile = $responseFile->json('data');

        $updateInfo = [
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
        ];

        $response = $this->patchJson(
            route('academy.course.item.file.update', ['item' => $item->id, 'file' => $itemFile['id']]),
            $updateInfo,
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('academy_course_item_files', [
            'id' => $itemFile['id']
        ] + $updateInfo);
    }

    public function testDestroyFilesForCourseItem(): void
    {
        $this->setUpFaker();
        $user = $this->createAdminUser();
        $item = AcademyCourseItem::inRandomOrder()->first();
        [$responseFile] = $this->createFileForItem($item, $user);
        $itemFile = $responseFile->json('data');

        $response = $this->deleteJson(
            route('academy.course.item.file.destroy', ['item' => $item->id, 'file' => $itemFile['id']]),
            [],
            ['Authorization' => 'Bearer ' . $user->adminToken]
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('academy_course_item_files', ['id' => $itemFile['id']]);
    }
}
