<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCompetitionBattleRequest;
use App\Http\Requests\Api\V1\StoreCompetitionCategoryRequest;
use App\Http\Requests\Api\V1\UpsertCompetitionBattleScoresRequest;
use App\Http\Resources\Api\V1\CompetitionBattleResource;
use App\Http\Resources\Api\V1\CompetitionCategoryResource;
use App\Models\CompetitionBattle;
use App\Models\CompetitionBattleScore;
use App\Models\CompetitionCategory;
use App\Models\Edition;
use App\Models\Robot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CompetitionManagementController extends Controller
{
    public function index(Edition $edition): JsonResponse
    {
        $categories = CompetitionCategory::query()
            ->where('edition_id', $edition->id)
            ->with(['battles.scores.robot.team'])
            ->orderBy('volgorde')
            ->orderBy('id')
            ->get();

        $robots = Robot::query()
            ->whereHas('team', static function ($query) use ($edition): void {
                $query->where('edition_id', $edition->id);
            })
            ->with('team')
            ->orderBy('naam')
            ->get()
            ->map(static fn (Robot $robot): array => [
                'id' => $robot->id,
                'naam' => $robot->naam,
                'status' => $robot->status->value,
                'is_battle_ready' => $robot->isBattleReady(),
                'team' => $robot->team !== null ? [
                    'id' => $robot->team->id,
                    'naam' => $robot->team->naam,
                ] : null,
            ])
            ->values();

        return response()->json([
            'data' => [
                'edition' => [
                    'id' => $edition->id,
                    'naam' => $edition->naam,
                ],
                'categories' => CompetitionCategoryResource::collection($categories),
                'available_robots' => $robots,
            ],
        ]);
    }

    public function storeCategory(StoreCompetitionCategoryRequest $request, Edition $edition): CompetitionCategoryResource
    {
        /** @var array{naam: string, omschrijving?: string|null, volgorde?: int|null} $validated */
        $validated = $request->validated();

        $category = CompetitionCategory::query()->create([
            'edition_id' => $edition->id,
            'naam' => $validated['naam'],
            'slug' => $this->resolveUniqueSlug($edition->id, $validated['naam']),
            'omschrijving' => $validated['omschrijving'] ?? null,
            'volgorde' => (int) ($validated['volgorde'] ?? 0),
        ]);

        $category->load(['battles.scores.robot.team']);

        return new CompetitionCategoryResource($category);
    }

    public function updateCategory(Request $request, CompetitionCategory $competitionCategory): CompetitionCategoryResource
    {
        /** @var array{naam?: string, omschrijving?: string|null, volgorde?: int|null} $validated */
        $validated = $request->validate([
            'naam' => ['sometimes', 'string', 'max:120'],
            'omschrijving' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'volgorde' => ['sometimes', 'integer', 'min:0', 'max:999'],
        ]);

        if (array_key_exists('naam', $validated) && is_string($validated['naam']) && $validated['naam'] !== $competitionCategory->naam) {
            $competitionCategory->slug = $this->resolveUniqueSlug(
                editionId: $competitionCategory->edition_id,
                name: $validated['naam'],
                ignoreId: $competitionCategory->id,
            );
        }

        $competitionCategory->fill($validated);
        $competitionCategory->save();
        $competitionCategory->load(['battles.scores.robot.team']);

        return new CompetitionCategoryResource($competitionCategory);
    }

    public function storeBattle(StoreCompetitionBattleRequest $request, CompetitionCategory $competitionCategory): CompetitionBattleResource
    {
        /** @var array{naam: string, battle_mode: string, omschrijving?: string|null, volgorde?: int|null} $validated */
        $validated = $request->validated();

        $battle = $competitionCategory->battles()->create([
            'naam' => $validated['naam'],
            'battle_mode' => $validated['battle_mode'],
            'omschrijving' => $validated['omschrijving'] ?? null,
            'volgorde' => (int) ($validated['volgorde'] ?? 0),
        ]);

        $battle->load(['scores.robot.team']);

        return new CompetitionBattleResource($battle);
    }

    public function updateBattle(Request $request, CompetitionBattle $competitionBattle): CompetitionBattleResource
    {
        /** @var array{naam?: string, battle_mode?: string, omschrijving?: string|null, volgorde?: int|null} $validated */
        $validated = $request->validate([
            'naam' => ['sometimes', 'string', 'max:120'],
            'battle_mode' => ['sometimes', 'string', 'in:solo,multi'],
            'omschrijving' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'volgorde' => ['sometimes', 'integer', 'min:0', 'max:999'],
        ]);

        $competitionBattle->fill($validated);
        $competitionBattle->save();
        $competitionBattle->load(['scores.robot.team']);

        return new CompetitionBattleResource($competitionBattle);
    }

    public function upsertScores(
        UpsertCompetitionBattleScoresRequest $request,
        CompetitionBattle $competitionBattle,
    ): CompetitionBattleResource {
        /** @var array{entries: list<array{robot_id: int, punten: int, opmerkingen?: string|null}>} $validated */
        $validated = $request->validated();

        $editionId = $competitionBattle->category()->value('edition_id');

        if (! is_int($editionId)) {
            throw ValidationException::withMessages([
                'entries' => ['Kan editie voor deze battle niet bepalen.'],
            ]);
        }

        DB::transaction(function () use ($validated, $competitionBattle, $editionId): void {
            foreach ($validated['entries'] as $entry) {
                $robot = Robot::query()->with('team')->findOrFail((int) $entry['robot_id']);

                if (! $robot->isBattleReady()) {
                    throw ValidationException::withMessages([
                        'entries' => ["Robot {$robot->naam} is niet battle ready en kan niet meedoen."],
                    ]);
                }

                if ((int) ($robot->team?->edition_id ?? 0) !== $editionId) {
                    throw ValidationException::withMessages([
                        'entries' => ["Robot {$robot->naam} hoort niet bij deze editie."],
                    ]);
                }

                CompetitionBattleScore::query()->updateOrCreate(
                    [
                        'competition_battle_id' => $competitionBattle->id,
                        'robot_id' => $robot->id,
                    ],
                    [
                        'punten' => (int) $entry['punten'],
                        'opmerkingen' => $entry['opmerkingen'] ?? null,
                    ],
                );
            }
        });

        $competitionBattle->load(['scores.robot.team']);

        return new CompetitionBattleResource($competitionBattle);
    }

    private function resolveUniqueSlug(int $editionId, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'categorie';

        $counter = 1;

        while (CompetitionCategory::query()
            ->where('edition_id', $editionId)
            ->where('slug', $slug)
            ->when($ignoreId !== null, static function ($query) use ($ignoreId): void {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists()) {
            $counter++;
            $slug = ($base !== '' ? $base : 'categorie').'-'.$counter;
        }

        return $slug;
    }
}
