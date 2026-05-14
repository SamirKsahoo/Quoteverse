<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/surveys/geojson
    // Returns ALL surveys as a GeoJSON FeatureCollection
    // ─────────────────────────────────────────────────────────────
    // public function geojson(): JsonResponse
    // {
    //     $surveys  = Survey::all();
    //     $features = $surveys->map(fn($s) => $s->toGeoJsonFeature())->values()->all();

    //     return response()->json([
    //         'type'     => 'FeatureCollection',
    //         'features' => $features,
    //     ]);
    // }

    // // ─────────────────────────────────────────────────────────────
    // // GET /api/surveys/stream
    // // Server-Sent Events — pushes GeoJSON updates to the browser
    // // ─────────────────────────────────────────────────────────────
    // public function stream(): StreamedResponse
    // {
    //     return response()->stream(function () {
    //         // Send current data immediately on connect
    //         $this->sendSseEvent($this->buildGeoJson());

    //         // Poll the database every 3 seconds and push if changed
    //         $lastHash = '';
    //         $maxIterations = 200; // ~10 minutes max, prevents zombie connections
    //         $i = 0;

    //         while ($i < $maxIterations) {
    //             if (connection_aborted()) {
    //                 break;
    //             }

    //             $geojson  = $this->buildGeoJson();
    //             $hash     = md5(json_encode($geojson));

    //             if ($hash !== $lastHash) {
    //                 $this->sendSseEvent($geojson);
    //                 $lastHash = $hash;
    //             }

    //             ob_flush();
    //             flush();
    //             sleep(3);
    //             $i++;
    //         }
    //     }, 200, [
    //         'Content-Type'                => 'text/event-stream',
    //         'Cache-Control'               => 'no-cache',
    //         'X-Accel-Buffering'           => 'no',
    //         'Access-Control-Allow-Origin' => '*',
    //     ]);
    // }

    // // ─────────────────────────────────────────────────────────────
    // // PATCH /api/surveys/{id}/status
    // // Update plot status → map auto-updates via SSE
    // // Body: { "status": "acquired" }
    // // ─────────────────────────────────────────────────────────────
    // public function updateStatus(Request $request, int $id): JsonResponse
    // {
    //     $request->validate([
    //         'status' => 'required|in:available,need_to_acquire,acquired,reserved,sold',
    //     ]);

    //     $survey = Survey::findOrFail($id);
    //     $survey->update(['status' => $request->status]);

    //     return response()->json([
    //         'success'       => true,
    //         'message'       => 'Status updated successfully',
    //         'survey_number' => $survey->survey_number,
    //         'new_status'    => $survey->status,
    //     ]);
    //     // SSE stream will detect the DB change on its next poll and push to browser
    // }

    // // ─────────────────────────────────────────────────────────────
    // // GET /api/surveys
    // // List all surveys (raw, for admin table)
    // // ─────────────────────────────────────────────────────────────
    // public function index(): JsonResponse
    // {
    //     return response()->json(Survey::all());
    // }

    // // ─────────────────────────────────────────────────────────────
    // // Private helpers
    // // ─────────────────────────────────────────────────────────────
    // private function buildGeoJson(): array
    // {
    //     $surveys  = Survey::all();
    //     $features = $surveys->map(fn($s) => $s->toGeoJsonFeature())->values()->all();

    //     return [
    //         'type'     => 'FeatureCollection',
    //         'features' => $features,
    //     ];
    // }

    // private function sendSseEvent(array $data): void
    // {
    //     echo "data: " . json_encode($data) . "\n\n";
    //     ob_flush();
    //     flush();
    // }


    // new code.........................
//     <?php

// namespace App\Http\Controllers;

// use App\Models\Survey;
// use App\Models\Project;
// use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;
// use Symfony\Component\HttpFoundation\StreamedResponse;

// class SurveyController extends Controller
// {
    // ─────────────────────────────────────────────────────────────
    // GET /api/projects
    // Returns all projects with their metadata (name, center, zoom)
    // Used by the frontend to build the project switcher & fit map bounds
    // ─────────────────────────────────────────────────────────────
    public function projects(): JsonResponse
    {
        // If you have a `projects` table, use Project::all().
        // As a fallback we compute project meta from the surveys table itself
        // so this works even without a separate projects table.
        $rows = Survey::selectRaw('
                project_id,
                COUNT(*) as survey_count,
                AVG(JSON_UNQUOTE(JSON_EXTRACT(centroid, "$.lat"))) as center_lat,
                AVG(JSON_UNQUOTE(JSON_EXTRACT(centroid, "$.lng"))) as center_lng
            ')
            ->whereNotNull('project_id')
            ->groupBy('project_id')
            ->get();

        // Try to enrich with Project model names if the table exists
        $projectNames = [];
        try {
            Project::all()->each(fn($p) => $projectNames[$p->id] = $p->name);
        } catch (\Exception $e) {
            // projects table may not exist yet — ignore
        }

        $projects = $rows->map(fn($r) => [
            'id'           => $r->project_id,
            'name'         => $projectNames[$r->project_id] ?? "Project {$r->project_id}",
            'survey_count' => $r->survey_count,
            'center_lat'   => round((float) $r->center_lat, 6),
            'center_lng'   => round((float) $r->center_lng, 6),
        ])->values();

        return response()->json($projects);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/surveys/geojson?project_id=1
    // Returns surveys as GeoJSON — optionally filtered by project
    // ─────────────────────────────────────────────────────────────
    public function geojson(Request $request): JsonResponse
    {
        $features = $this->buildGeoJson($request->query('project_id'))['features'];

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/surveys/stream?project_id=1
    // Server-Sent Events — pushes GeoJSON updates to the browser
    // ─────────────────────────────────────────────────────────────
    public function stream(Request $request): StreamedResponse
    {
        $projectId = $request->query('project_id'); // null = all projects

        return response()->stream(function () use ($projectId) {
            $this->sendSseEvent($this->buildGeoJson($projectId));

            $lastHash      = '';
            $maxIterations = 200;
            $i             = 0;

            while ($i < $maxIterations) {
                if (connection_aborted()) break;

                $geojson = $this->buildGeoJson($projectId);
                $hash    = md5(json_encode($geojson));

                if ($hash !== $lastHash) {
                    $this->sendSseEvent($geojson);
                    $lastHash = $hash;
                }

                ob_flush();
                flush();
                sleep(3);
                $i++;
            }
        }, 200, [
            'Content-Type'                => 'text/event-stream',
            'Cache-Control'               => 'no-cache',
            'X-Accel-Buffering'           => 'no',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // PATCH /api/surveys/{id}/status
    // Update plot status → map auto-updates via SSE
    // Body: { "status": "acquired" }
    // ─────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:available,need_to_acquire,acquired,reserved,sold',
        ]);

        $survey = Survey::findOrFail($id);
        $survey->update(['status' => $request->status]);

        return response()->json([
            'success'       => true,
            'message'       => 'Status updated successfully',
            'survey_number' => $survey->survey_number,
            'new_status'    => $survey->status,
            'project_id'    => $survey->project_id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/surveys
    // List all surveys (raw, for admin table)
    // ─────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        return response()->json(Survey::all());
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────
    private function buildGeoJson(?string $projectId = null): array
    {
        $query = Survey::query();

        if ($projectId !== null) {
            $query->where('project_id', $projectId);
        }

        $features = $query->get()
            ->map(fn($s) => $s->toGeoJsonFeature())
            ->values()
            ->all();

        return [
            'type'     => 'FeatureCollection',
            'features' => $features,
        ];
    }

    private function sendSseEvent(array $data): void
    {
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }
}
    //end...........................
// }