<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Получить список задач",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Task")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        return TaskResource::collection($query->get());
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent();
    }

    public function priority()
    {
        $now = \Carbon\Carbon::now();

        $collection = Task::all()->map(function (Task $task) use ($now) {
            // сколько дней до дедлайна (отрицательное, если просрочено)
            $daysRemaining = $now->diffInDays($task->deadline, false);

            $isOverdue = $daysRemaining < 0;
            // если просрочено — score = 0, иначе importance / daysUntil
            $score = $isOverdue
                ? 0
                : $task->importance * (1 / max($daysRemaining, 1));

            return [
                'id'             => $task->id,
                'title'          => $task->title,
                'description'    => $task->description,
                'status'         => $task->status,
                'importance'     => $task->importance,
                'deadline'       => $task->deadline->format('Y-m-d H:i:s'),
                'is_overdue'     => $isOverdue,
                'priority_score' => round($score, 2),
                'created_at'     => $task->created_at->format('Y-m-d H:i:s'),
                'updated_at'     => $task->updated_at->format('Y-m-d H:i:s'),
            ];
        })
            ->sortByDesc('priority_score')
            ->values();

        return response()->json(['data' => $collection], 200);
    }
}
/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     @OA\Property(property="id",           type="integer"),
 *     @OA\Property(property="title",        type="string"),
 *     @OA\Property(property="description",  type="string"),
 *     @OA\Property(property="status",       type="string"),
 *     @OA\Property(property="importance",   type="integer"),
 *     @OA\Property(property="deadline",     type="string", format="date-time"),
 *     @OA\Property(property="is_overdue",    type="boolean"),
 *     @OA\Property(property="priority_score", type="number", format="float"),
 *     @OA\Property(property="created_at",   type="string", format="date-time"),
 *     @OA\Property(property="updated_at",   type="string", format="date-time")
 * )
 */
