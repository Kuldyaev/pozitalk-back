<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="UserStatus",
 *     description="Статус пользователя",
 *     @OA\Xml(
 *         name="UserStatus"
 *     )
 * )
 */
class UserStatus extends Model
{
    use HasFactory;

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $id;

    /**
     * @OA\Property(
     *     title="title",
     *     description="title",
     *     format="string",
     *     example="test"
     * )
     */
    private $title;

    public $timestamps = false;
}
