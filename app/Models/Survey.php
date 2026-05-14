<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'project_id',
        'survey_number',
        'centroid',
        'survey_records',
        'status',
        'title',
        'description',
        'price',
        'area_sqft',
        'image_url',
    ];

    protected $casts = [
        'centroid'       => 'array',
        'survey_records' => 'array',
        'price'          => 'float',
        'area_sqft'      => 'float',
    ];

    // Status constants
    const STATUS_AVAILABLE       = 'available';
    const STATUS_NEED_TO_ACQUIRE = 'need_to_acquire';
    const STATUS_ACQUIRED        = 'acquired';
    const STATUS_RESERVED        = 'reserved';
    const STATUS_SOLD            = 'sold';

    public function toGeoJsonFeature(): array
    {
        $records = is_string($this->survey_records)
            ? json_decode($this->survey_records, true)
            : $this->survey_records;

        $centroid = is_string($this->centroid)
            ? json_decode($this->centroid, true)
            : $this->centroid;

        // ✅ CORRECT: Each ring from SQL = one separate Polygon inside MultiPolygon
        // This matches exactly how the working geojson file is structured
        $polygons = array_map(function ($ring) {
            // Convert {lat,lng} → [lng,lat]  (GeoJSON uses lng first)
            $coords = array_map(fn($p) => [$p['lng'], $p['lat']], $ring);

            // Ensure ring is closed (last point = first point)
            if ($coords[0] !== end($coords)) {
                $coords[] = $coords[0];
            }

            return [$coords]; // MultiPolygon format: each polygon = [outerRing]
        }, $records);

        return [
            'type' => 'Feature',
            'id'   => $this->id,
            'geometry' => [
                'type'        => 'MultiPolygon',
                'coordinates' => $polygons,
            ],
            // 'properties' => [
            //     'id'            => $this->id,
            //     'survey_number' => $this->survey_number,
            //     'title'         => $this->title ?? 'Survey ' . $this->survey_number,
            //     'status'        => $this->status,
            //     'price'         => $this->price,
            //     'area_sqft'     => $this->area_sqft,
            //     'description'   => $this->description,
            //     'image_url'     => $this->image_url,
            //     'centroid_lat'  => $centroid['lat'] ?? null,
            //     'centroid_lng'  => $centroid['lng'] ?? null,
            // ],
            'properties' => [
                'id'            => $this->id,
                'project_id'    => $this->project_id,   // ← ADD THIS
                'feature_type'  => 'survey',            // ← ADD THIS (helps frontend differentiate)
                'survey_number' => $this->survey_number,
                'title'         => $this->title ?? 'Survey ' . $this->survey_number,
                'status'        => $this->status,
                'price'         => $this->price,
                'area_sqft'     => $this->area_sqft,
                'description'   => $this->description,
                'image_url'     => $this->image_url,
                'centroid_lat'  => $centroid['lat'] ?? null,
                'centroid_lng'  => $centroid['lng'] ?? null,
            ],
        ];
    }
}