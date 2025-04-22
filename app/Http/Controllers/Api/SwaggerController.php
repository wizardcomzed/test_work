<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Tasks API",
 *     version="1.0.0",
 *     description="Документация для API управления задачами"
 *   ),
 *   @OA\Server(url="http://localhost:8000"),
 *   @OA\Components(
 *     @OA\Schema(
 *       schema="Task",
 *       type="object",
 *       @OA\Property(property="id",             type="integer"),
 *       @OA\Property(property="title",          type="string"),
 *       @OA\Property(property="description",    type="string"),
 *       @OA\Property(property="status",         type="string"),
 *       @OA\Property(property="importance",     type="integer"),
 *       @OA\Property(property="deadline",       type="string", format="date-time"),
 *       @OA\Property(property="is_overdue",     type="boolean"),
 *       @OA\Property(property="priority_score", type="number", format="float"),
 *       @OA\Property(property="created_at",     type="string", format="date-time"),
 *       @OA\Property(property="updated_at",     type="string", format="date-time")
 *     )
 *   )
 * )
 */
class SwaggerController
{

}
