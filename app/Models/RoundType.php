<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="RoundType",
 *     description="Тип раунда",
 *     @OA\Xml(
 *         name="RoundType"
 *     )
 * )
 */
class RoundType extends Model
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
     *     title="price",
     *     description="price",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $price;

    /**
     * @OA\Property(
     *     title="count_rounds",
     *     description="count_rounds",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $count_rounds;

    /**
     * @OA\Property(
     *     title="count_givers",
     *     description="count_givers",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $count_givers;

    public $timestamps = false;
}
