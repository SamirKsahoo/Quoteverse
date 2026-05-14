<?php

use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;




// ── Projects ─────────────────────────────────────────────────────
// GET /api/projects  →  list all projects with center coords & survey count
Route::get('/projects', [SurveyController::class, 'projects']);

// ── Surveys ───────────────────────────────────────────────────────
// GET /api/surveys/geojson              →  all surveys as GeoJSON
// GET /api/surveys/geojson?project_id=1 →  one project's surveys
Route::get('/surveys/geojson', [SurveyController::class, 'geojson']);

// GET /api/surveys/stream               →  SSE (all projects)
// GET /api/surveys/stream?project_id=1  →  SSE (one project)
Route::get('/surveys/stream', [SurveyController::class, 'stream']);

// PATCH /api/surveys/{id}/status  →  update plot status
Route::patch('/surveys/{id}/status', [SurveyController::class, 'updateStatus']);

// GET /api/surveys  →  raw list (admin)
Route::get('/surveys', [SurveyController::class, 'index']);