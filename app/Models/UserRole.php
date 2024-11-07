<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="UserRole",
 *     description="Роль пользователя",
 *     @OA\Xml(
 *         name="UserRole"
 *     )
 * )
 */
class UserRole extends Model
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
     *     title="role",
     *     description="role",
     *     format="string",
     *     example="admin"
     * )
     */
    private $role;

    public $timestamps = false;

     public function users()
    {
        return $this->hasMany(User::class, 'usersrole_id');
    }
}
