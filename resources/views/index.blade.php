<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Survey Land Map</title>
  {{-- MapTiler SDK --}}
  <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.css" rel="stylesheet"/>
  <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.umd.min.js"></script>
  <style>
    /* ═══════════════════════════════════════════════════════════
       RESET & BASE
    ═══════════════════════════════════════════════════════════ */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      height: 100vh; display: flex; flex-direction: column;
      background: #f0f2f5; overflow: hidden;
    }

    /* ═══════════════════════════════════════════════════════════
       NAVBAR
    ═══════════════════════════════════════════════════════════ */
    .navbar {
      height: 56px; background: #1b2a3b; color: #fff;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 16px; flex-shrink: 0; z-index: 100; gap: 10px;
    }
    .brand { font-size: 16px; font-weight: 700; letter-spacing: .3px; white-space: nowrap; flex-shrink: 0; }
    .nav-center { display: flex; gap: 5px; flex: 1; justify-content: center; flex-wrap: wrap; overflow: hidden; }
    .nav-right  { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

    /* Project tabs */
    .proj-tab {
      padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
      border: 2px solid transparent; cursor: pointer; transition: all .2s;
      background: rgba(255,255,255,.1); color: #cdd8e3; white-space: nowrap;
    }
    .proj-tab:hover { background: rgba(255,255,255,.2); color: #fff; }
    .proj-tab.active { background: #fff; color: #1b2a3b; }
    .proj-tab[data-pid="all"] { background: rgba(255,255,255,.15); }
    .proj-tab[data-pid="all"].active { background: #e2e8f0; }

    /* SSE indicator */
    .sse-dot { width: 8px; height: 8px; border-radius: 50%; background: #555; transition: background .4s; }
    .sse-dot.connected { background: #4ade80; box-shadow: 0 0 6px #4ade80; }
    .sse-label { font-size: 11px; color: #aaa; }

    /* ═══════════════════════════════════════════════════════════
       POI FILTER BAR  (below navbar)
    ═══════════════════════════════════════════════════════════ */
    .filter-bar {
      height: 44px; background: #fff; border-bottom: 1px solid #e2e8f0;
      display: flex; align-items: center; padding: 0 14px; gap: 8px;
      flex-shrink: 0; z-index: 99; overflow-x: auto;
    }
    .filter-bar-label {
      font-size: 11px; font-weight: 700; color: #94a3b8;
      text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; flex-shrink: 0;
    }
    .filter-btn {
      display: flex; align-items: center; gap: 5px;
      padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
      border: 1.5px solid #e2e8f0; background: #fff; color: #475569;
      cursor: pointer; transition: all .2s; white-space: nowrap; flex-shrink: 0;
      position: relative;
    }
    .filter-btn:hover { border-color: #94a3b8; color: #1b2a3b; }
    .filter-btn.active {
      color: #fff; border-color: transparent;
    }
    .filter-btn.active.cat-highway  { background: #7c3aed; }
    .filter-btn.active.cat-medical  { background: #dc2626; }
    .filter-btn.active.cat-schools  { background: #0284c7; }
    .filter-btn.active.cat-markets   { background: #d97706; }
    .filter-btn.active.cat-airports  { background: #0e7490; }
    /* Loading spinner on button */
    .filter-btn .spin {
      display: none; width: 12px; height: 12px;
      border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
      border-radius: 50%; animation: spin .6s linear infinite;
    }
    .filter-btn.loading .spin { display: block; }
    .filter-btn.loading .btn-icon { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* POI count badge on button */
    .filter-btn .count-badge {
      display: none; background: rgba(255,255,255,.25);
      font-size: 10px; font-weight: 700; padding: 1px 5px;
      border-radius: 10px; min-width: 18px; text-align: center;
    }
    .filter-btn.active .count-badge { display: inline-block; }

    /* ═══════════════════════════════════════════════════════════
       LAYOUT
    ═══════════════════════════════════════════════════════════ */
    .layout { flex: 1; display: flex; overflow: hidden; position: relative; }
    #map { flex: 1; height: 100%; }

    /* ═══════════════════════════════════════════════════════════
       ZOOM HINT
    ═══════════════════════════════════════════════════════════ */
    .zoom-hint {
      position: absolute; top: 14px; left: 50%; transform: translateX(-50%);
      background: rgba(27,42,59,.9); color: #fff; padding: 7px 16px;
      border-radius: 20px; font-size: 12px; font-weight: 600;
      pointer-events: none; z-index: 20; opacity: 0; transition: opacity .4s;
      white-space: nowrap;
    }
    .zoom-hint.show { opacity: 1; }

    /* ═══════════════════════════════════════════════════════════
       LEGEND
    ═══════════════════════════════════════════════════════════ */
    .legend {
      position: absolute; bottom: 28px; left: 12px; background: #fff;
      border-radius: 10px; padding: 12px 14px; box-shadow: 0 2px 12px rgba(0,0,0,.15);
      z-index: 5; min-width: 175px; max-height: calc(100vh - 160px); overflow-y: auto;
    }
    .legend h4 {
      font-size: 11px; font-weight: 700; color: #1b2a3b; margin-bottom: 8px;
      text-transform: uppercase; letter-spacing: .5px;
    }
    .legend-item { display: flex; align-items: center; gap: 7px; margin-bottom: 5px; font-size: 12px; color: #444; }
    .legend-color { width: 13px; height: 13px; border-radius: 3px; flex-shrink: 0; }
    .legend-divider { border: none; border-top: 1px solid #e2e8f0; margin: 8px 0; }

    /* Project lines */
    .legend-proj { display: flex; align-items: center; gap: 7px; margin-bottom: 5px; font-size: 12px; color: #444; font-weight: 600; }
    .legend-proj-line { width: 20px; height: 3px; border-radius: 2px; flex-shrink: 0; }

    /* POI legend items */
    .legend-poi { display: flex; align-items: center; gap: 7px; margin-bottom: 5px; font-size: 12px; color: #444; }
    .legend-poi-dot { width: 11px; height: 11px; border-radius: 50%; flex-shrink: 0; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,.2); }
    .poi-highway  { background: #7c3aed; }
    .poi-medical  { background: #dc2626; }
    .poi-schools  { background: #0284c7; }
    .poi-markets   { background: #d97706; }
    .poi-airports  { background: #0e7490; }
    .poi-highway-line { background: #7c3aed; height: 3px; width: 20px; border-radius: 2px; flex-shrink: 0; }

    /* ═══════════════════════════════════════════════════════════
       SIDEBAR
    ═══════════════════════════════════════════════════════════ */
    .sidebar {
      width: 320px; background: #fff; display: flex; flex-direction: column;
      box-shadow: -3px 0 16px rgba(0,0,0,.12); z-index: 10; overflow-y: auto;
      transform: translateX(100%); transition: transform .3s ease;
      position: absolute; right: 0; top: 0; bottom: 0;
    }
    .sidebar.open { transform: translateX(0); }
    .sidebar-img-placeholder {
      width: 100%; height: 160px; display: flex; align-items: center;
      justify-content: center; color: #fff; font-size: 36px; flex-shrink: 0;
    }
    .sidebar-img { width: 100%; height: 160px; object-fit: cover; flex-shrink: 0; }
    .sidebar-body { padding: 16px; flex: 1; }

    .survey-badge {
      display: inline-block; color: #fff; font-size: 11px; font-weight: 700;
      padding: 3px 10px; border-radius: 20px; margin-bottom: 8px; letter-spacing: .5px;
    }
    .project-chip {
      display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px;
      border-radius: 12px; margin-bottom: 8px; margin-left: 6px;
      vertical-align: middle; letter-spacing: .3px;
    }
    .sidebar-title { font-size: 18px; font-weight: 700; color: #1b2a3b; margin-bottom: 5px; }
    .sidebar-desc  { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 12px; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px; }
    .info-card { background: #f8fafc; border-radius: 8px; padding: 9px 11px; border: 1px solid #e2e8f0; }
    .info-card .label { font-size: 10px; color: #999; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
    .info-card .value { font-size: 13px; font-weight: 700; color: #1b2a3b; margin-top: 2px; }

    .status-badge {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 11px; font-weight: 700; padding: 4px 11px;
      border-radius: 20px; margin-bottom: 14px; text-transform: uppercase; letter-spacing: .4px;
    }
    .status-badge .dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-available      { background: #dcfce7; color: #166534; }
    .status-available .dot { background: #16a34a; }
    .status-need_to_acquire      { background: #fef9c3; color: #854d0e; }
    .status-need_to_acquire .dot { background: #ca8a04; }
    .status-acquired      { background: #fee2e2; color: #991b1b; }
    .status-acquired .dot { background: #dc2626; }
    .status-reserved      { background: #dbeafe; color: #1e40af; }
    .status-reserved .dot { background: #2563eb; }
    .status-sold      { background: #f1f5f9; color: #475569; }
    .status-sold .dot { background: #94a3b8; }

    .admin-section {
      background: #f8fafc; border: 1px solid #e2e8f0;
      border-radius: 10px; padding: 12px; margin-bottom: 14px;
    }
    .admin-section label { font-size: 11px; font-weight: 700; color: #64748b; display: block; margin-bottom: 7px; }
    .status-select {
      width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1;
      border-radius: 7px; font-size: 13px; background: #fff; color: #1b2a3b;
      cursor: pointer; outline: none;
    }
    .status-select:focus { border-color: #3b82f6; }
    .btn-update {
      width: 100%; margin-top: 8px; padding: 8px; background: #1b2a3b;
      color: #fff; border: none; border-radius: 7px; font-size: 13px;
      font-weight: 700; cursor: pointer; transition: background .2s;
    }
    .btn-update:hover { background: #2d4a6b; }
    .btn-update:disabled { background: #94a3b8; cursor: not-allowed; }
    .update-msg { font-size: 12px; margin-top: 5px; text-align: center; min-height: 16px; }
    .update-msg.success { color: #16a34a; }
    .update-msg.error   { color: #dc2626; }

    .sidebar-actions { display: flex; gap: 8px; }
    .btn-enquiry, .btn-share {
      flex: 1; padding: 10px; border: none; border-radius: 8px;
      font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity .2s;
    }
    .btn-enquiry { background: #e05c2a; color: #fff; }
    .btn-share   { background: #1b7a5c; color: #fff; }
    .btn-enquiry:hover, .btn-share:hover { opacity: .88; }

    .close-btn {
      position: absolute; top: 10px; right: 10px;
      background: rgba(0,0,0,.55); color: #fff; border: none;
      width: 28px; height: 28px; border-radius: 50%; font-size: 13px;
      cursor: pointer; z-index: 5; display: flex; align-items: center; justify-content: center;
    }

    /* ═══════════════════════════════════════════════════════════
       MAPLIBRE POI POPUP
    ═══════════════════════════════════════════════════════════ */
    .maplibregl-popup-content, .mapboxgl-popup-content {
      border-radius: 8px !important;
      padding: 8px 12px !important;
      box-shadow: 0 4px 16px rgba(0,0,0,.18) !important;
      font-family: 'Segoe UI', sans-serif;
      font-size: 13px;
    }
    .poi-popup-title { font-weight: 700; color: #1b2a3b; margin-bottom: 2px; }
    .poi-popup-type  { font-size: 11px; color: #64748b; text-transform: capitalize; }

    /* ═══════════════════════════════════════════════════════════
       TOAST
    ═══════════════════════════════════════════════════════════ */
    .toast {
      position: absolute; bottom: 22px; right: 22px;
      background: #1b2a3b; color: #fff; padding: 9px 16px;
      border-radius: 8px; font-size: 13px; font-weight: 600;
      opacity: 0; transition: opacity .3s; pointer-events: none; z-index: 200;
    }
    .toast.show { opacity: 1; }
  </style>
</head>
<body>

{{-- ═══════════ NAVBAR ═══════════ --}}
<header class="navbar">
  <span class="brand">🌿 Survey Land Map</span>
  <nav class="nav-center" id="projectTabs">
    <button class="proj-tab active" data-pid="all">All Projects</button>
  </nav>
  <div class="nav-right">
    <div class="sse-dot" id="sseDot"></div>
    <span class="sse-label" id="sseLabel">Connecting…</span>
  </div>
</header>

{{-- ═══════════ POI FILTER BAR ═══════════ --}}
<div class="filter-bar">
  <span class="filter-bar-label">Nearby</span>

  <button class="filter-btn cat-highway" id="btn-highway" data-cat="highway">
    <span class="btn-icon">🛣️</span>
    <span class="spin"></span>
    Highways
    <span class="count-badge" id="count-highway">0</span>
  </button>

  <button class="filter-btn cat-medical" id="btn-medical" data-cat="medical">
    <span class="btn-icon">🏥</span>
    <span class="spin"></span>
    Medical
    <span class="count-badge" id="count-medical">0</span>
  </button>

  <button class="filter-btn cat-schools" id="btn-schools" data-cat="schools">
    <span class="btn-icon">🏫</span>
    <span class="spin"></span>
    Schools
    <span class="count-badge" id="count-schools">0</span>
  </button>

  <button class="filter-btn cat-markets" id="btn-markets" data-cat="markets">
    <span class="btn-icon">🛒</span>
    <span class="spin"></span>
    Markets
    <span class="count-badge" id="count-markets">0</span>
  </button>

  <button class="filter-btn cat-airports" id="btn-airports" data-cat="airports">
    <span class="btn-icon">✈️</span>
    <span class="spin"></span>
    Airports
    <span class="count-badge" id="count-airports">0</span>
  </button>
</div>

{{-- ═══════════ MAP LAYOUT ═══════════ --}}
<div class="layout">
  <div id="map"></div>

  <div class="zoom-hint" id="zoomHint">📍 Click a pin to zoom in and see plots</div>

  {{-- ═══════════ LEGEND ═══════════ --}}
  <div class="legend" id="legend">
    <h4>Plot Status</h4>
    <div class="legend-item"><div class="legend-color" style="background:#16a34a"></div> Available</div>
    <div class="legend-item"><div class="legend-color" style="background:#ca8a04"></div> Need to Acquire</div>
    <div class="legend-item"><div class="legend-color" style="background:#dc2626"></div> Acquired</div>
    <div class="legend-item"><div class="legend-color" style="background:#2563eb"></div> Reserved</div>
    <div class="legend-item"><div class="legend-color" style="background:#94a3b8"></div> Sold</div>

    <hr class="legend-divider"/>
    <h4 style="margin-bottom:7px">Projects</h4>
    <div id="legendProjects"></div>

    {{-- POI legend — shown only when a filter is active --}}
    <div id="legendPoi" style="display:none">
      <hr class="legend-divider"/>
      <h4 style="margin-bottom:7px">Nearby Places</h4>
      <div id="legendPoiItems"></div>
    </div>
  </div>

  {{-- ═══════════ SIDEBAR ═══════════ --}}
  <aside class="sidebar" id="sidebar">
    <button class="close-btn" id="closeBtn">✕</button>
    <div id="sidebarImg"></div>
    <div class="sidebar-body">
      <div>
        <span class="survey-badge" id="sBadge">Survey —</span>
        <span class="project-chip" id="sProjChip"></span>
      </div>
      <h2 class="sidebar-title" id="sTitle"></h2>
      <div id="sStatus"></div>
      <p class="sidebar-desc" id="sDesc"></p>
      <div class="info-grid">
        <div class="info-card"><div class="label">Price</div><div class="value" id="sPrice">—</div></div>
        <div class="info-card"><div class="label">Area</div><div class="value" id="sArea">—</div></div>
      </div>
      <div class="admin-section">
        <label>Change Status</label>
        <select class="status-select" id="statusSelect">
          <option value="available">Available</option>
          <option value="need_to_acquire">Need to Acquire</option>
          <option value="acquired">Acquired</option>
          <option value="reserved">Reserved</option>
          <option value="sold">Sold</option>
        </select>
        <button class="btn-update" id="updateBtn">Update Status</button>
        <div class="update-msg" id="updateMsg"></div>
      </div>
      <div class="sidebar-actions">
        <button class="btn-enquiry">Enquiry</button>
        <button class="btn-share" id="shareBtn">Share</button>
      </div>
    </div>
  </aside>

  <div class="toast" id="toast"></div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════
   CONFIG  — edit these values as needed
═══════════════════════════════════════════════════════════════ */
const MAPTILER_KEY   = 'CUcelt5z3c3qdeY5PWsY';
const API_BASE       = '/api';                       {{-- relative — works in Laravel --}}
const OVERPASS_URL   = 'https://overpass-api.de/api/interpreter';
const POI_RADIUS_M   = 5000;                         {{-- 5 km search radius per project --}}

const PIN_ZOOM_MAX   = 15;
const POLY_ZOOM_MIN  = 14;
const PROJECT_COLORS = ['#6366f1','#f59e0b','#10b981','#ef4444','#8b5cf6'];

/* ═══════════════════════════════════════════════════════════════
   POI CATEGORY DEFINITIONS
   Each category has:
     query(lat, lng, radius) → Overpass QL string
     color                   → circle / line colour
     type                    → 'point' | 'highway'
     layerId                 → base id for map layers
═══════════════════════════════════════════════════════════════ */
const POI_CATS = {
  highway: {
    color: '#7c3aed',
    type: 'highway',
    layerId: 'poi-highway',
    label: '🛣️ Highways',
    query: (lat, lng, r) => `
      [out:json][timeout:30];
      (
        way["highway"~"motorway|trunk|primary|secondary"](around:${r},${lat},${lng});
      );
      out geom;
    `,
  },
  medical: {
    color: '#dc2626',
    type: 'point',
    layerId: 'poi-medical',
    label: '🏥 Medical',
    query: (lat, lng, r) => `
      [out:json][timeout:30];
      (
        node["amenity"~"hospital|clinic|pharmacy|doctors"](around:${r},${lat},${lng});
        way["amenity"~"hospital|clinic|pharmacy|doctors"](around:${r},${lat},${lng});
      );
      out center;
    `,
  },
  schools: {
    color: '#0284c7',
    type: 'point',
    layerId: 'poi-schools',
    label: '🏫 Schools',
    query: (lat, lng, r) => `
      [out:json][timeout:30];
      (
        node["amenity"~"school|college|university|kindergarten"](around:${r},${lat},${lng});
        way["amenity"~"school|college|university|kindergarten"](around:${r},${lat},${lng});
      );
      out center;
    `,
  },
  markets: {
    color: '#d97706',
    type: 'point',
    layerId: 'poi-markets',
    label: '🛒 Markets',
    query: (lat, lng, r) => `
      [out:json][timeout:30];
      (
        node["shop"~"supermarket|mall|convenience"](around:${r},${lat},${lng});
        node["amenity"~"marketplace"](around:${r},${lat},${lng});
        way["shop"~"supermarket|mall"](around:${r},${lat},${lng});
      );
      out center;
    `,
  },
  airports: {
    color: '#0e7490',
    type: 'point',
    layerId: 'poi-airports',
    label: '✈️ Airports',
    // Use a wider 50 km radius — airports are sparse and often far from city plots
    query: (lat, lng, _r) => `
      [out:json][timeout:30];
      (
        node["aeroway"="aerodrome"](around:50000,${lat},${lng});
        way["aeroway"="aerodrome"](around:50000,${lat},${lng});
        node["aeroway"="terminal"](around:50000,${lat},${lng});
      );
      out center;
    `,
  },
};

/* ═══════════════════════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════════════════════ */
maptilersdk.config.apiKey = MAPTILER_KEY;

const map = new maptilersdk.Map({
  container: 'map',
  style:     maptilersdk.MapStyle.STREETS,
  center:    [77.51, 13.10],
  zoom:      10,
});

let selectedSurveyId = null;
let activeProjectId  = 'all';
let projectMeta      = [];
let currentGeojson   = { type: 'FeatureCollection', features: [] };
let sseSource        = null;

// POI state
const activeFilters  = new Set();       // which categories are ON
const overpassCache  = new Map();       // key → GeoJSON, avoids re-fetching
const poiPopup       = new maptilersdk.Popup({ closeButton: false, closeOnClick: false, maxWidth: '220px' });

const EMPTY_FC = { type: 'FeatureCollection', features: [] };

/* ═══════════════════════════════════════════════════════════════
   MAP STYLE EXPRESSIONS
═══════════════════════════════════════════════════════════════ */
function statusColorExpr() {
  return ['match', ['get', 'status'],
    'available','#16a34a', 'need_to_acquire','#ca8a04',
    'acquired','#dc2626',  'reserved','#2563eb', 'sold','#94a3b8',
    '#cccccc'];
}

function projectColorExpr() {
  if (!projectMeta.length) return '#aaaaaa';
  const pairs = projectMeta.flatMap(p => [p.id, p.color]);
  return ['match', ['get', 'project_id'], ...pairs, '#aaaaaa'];
}

/* ═══════════════════════════════════════════════════════════════
   GEOJSON HELPERS
═══════════════════════════════════════════════════════════════ */
function buildPinGeoJson(gj) {
  return {
    type: 'FeatureCollection',
    features: gj.features
      .filter(f => f.properties.centroid_lat && f.properties.centroid_lng)
      .map(f => ({
        type: 'Feature',
        geometry: { type: 'Point', coordinates: [+f.properties.centroid_lng, +f.properties.centroid_lat] },
        properties: { ...f.properties },
      })),
  };
}

function filterGeoJson(gj) {
  if (activeProjectId === 'all') return gj;
  return { ...gj, features: gj.features.filter(f => String(f.properties.project_id) === String(activeProjectId)) };
}

function buildProjectBoundaryGeoJson(gj) {
  const byProj = {};
  gj.features.forEach(f => {
    const pid = f.properties.project_id;
    const lat = +f.properties.centroid_lat, lng = +f.properties.centroid_lng;
    if (!pid || !lat || !lng) return;
    if (!byProj[pid]) byProj[pid] = { minLat: lat, maxLat: lat, minLng: lng, maxLng: lng };
    const b = byProj[pid];
    b.minLat = Math.min(b.minLat, lat); b.maxLat = Math.max(b.maxLat, lat);
    b.minLng = Math.min(b.minLng, lng); b.maxLng = Math.max(b.maxLng, lng);
  });
  const PAD = 0.003;
  return {
    type: 'FeatureCollection',
    features: Object.entries(byProj).map(([pid, b]) => ({
      type: 'Feature',
      geometry: { type: 'Polygon', coordinates: [[[b.minLng-PAD,b.minLat-PAD],[b.maxLng+PAD,b.minLat-PAD],[b.maxLng+PAD,b.maxLat+PAD],[b.minLng-PAD,b.maxLat+PAD],[b.minLng-PAD,b.minLat-PAD]]] },
      properties: { project_id: Number(pid) },
    })),
  };
}

/* ═══════════════════════════════════════════════════════════════
   MAP LOAD
═══════════════════════════════════════════════════════════════ */
map.on('load', async () => {

  /* 1. Fetch project meta */
  try {
    const r = await fetch(`${API_BASE}/projects`);
    const d = await r.json();
    projectMeta = d.map((p, i) => ({ ...p, color: PROJECT_COLORS[i % PROJECT_COLORS.length] }));
  } catch { projectMeta = []; }

  /* 2. Fetch initial survey GeoJSON */
  try {
    const r = await fetch(`${API_BASE}/surveys/geojson`);
    currentGeojson = await r.json();
  } catch {
    currentGeojson = EMPTY_FC;
    showToast('Could not load map data');
  }

  /* 3. Render tabs, legend, fit bounds */
  renderProjectTabs();
  renderLegendProjects();
  fitMapToBothProjects();

  /* 4. Survey sources */
  const visible = filterGeoJson(currentGeojson);
  map.addSource('surveys',         { type: 'geojson', data: visible, generateId: true });
  map.addSource('survey-pins',     { type: 'geojson', data: buildPinGeoJson(visible) });
  map.addSource('proj-boundaries', { type: 'geojson', data: buildProjectBoundaryGeoJson(currentGeojson) });

  /* 5. POI sources — one per category, start empty */
  Object.keys(POI_CATS).forEach(cat => {
    const cfg = POI_CATS[cat];
    if (cfg.type === 'highway') {
      map.addSource(`${cfg.layerId}-src`, { type: 'geojson', data: EMPTY_FC });
    } else {
      map.addSource(`${cfg.layerId}-src`,        { type: 'geojson', data: EMPTY_FC });
      map.addSource(`${cfg.layerId}-labels-src`, { type: 'geojson', data: EMPTY_FC });
    }
  });

  /* 6. Project boundary dashes */
  map.addLayer({
    id: 'proj-boundaries-line', type: 'line', source: 'proj-boundaries',
    paint: {
      'line-color': projectColorExpr(), 'line-width': 2.5,
      'line-dasharray': [4,3],
      'line-opacity': ['interpolate',['linear'],['zoom'], 8,1, POLY_ZOOM_MIN,0],
    },
  });

  /* 7. Survey polygon fill */
  map.addLayer({
    id: 'surveys-fill', type: 'fill', source: 'surveys',
    paint: {
      'fill-color': statusColorExpr(),
      'fill-opacity': ['interpolate',['linear'],['zoom'],
        POLY_ZOOM_MIN, 0,
        PIN_ZOOM_MAX, ['case',['boolean',['feature-state','hover'],false],0.85,0.6]],
    },
  });
  map.addLayer({
    id: 'surveys-outline', type: 'line', source: 'surveys',
    paint: {
      'line-color': '#1b2a3b', 'line-width': 1.2,
      'line-opacity': ['interpolate',['linear'],['zoom'], POLY_ZOOM_MIN,0, PIN_ZOOM_MAX,0.8],
    },
  });
  map.addLayer({
    id: 'surveys-labels', type: 'symbol', source: 'surveys',
    layout: {
      'text-field': ['get','survey_number'], 'text-size': 11,
      'text-font': ['Open Sans Bold','Arial Unicode MS Bold'],
      'text-allow-overlap': false,
    },
    paint: {
      'text-color': '#fff', 'text-halo-color': '#1b2a3b', 'text-halo-width': 1.5,
      'text-opacity': ['interpolate',['linear'],['zoom'], POLY_ZOOM_MIN,0, PIN_ZOOM_MAX,1],
    },
  });

  /* 8. Pin layers */
  map.addLayer({
    id: 'pins-shadow', type: 'circle', source: 'survey-pins',
    paint: {
      'circle-radius': 18, 'circle-color': statusColorExpr(), 'circle-blur': 0.6,
      'circle-opacity': ['interpolate',['linear'],['zoom'], 12,0.18, PIN_ZOOM_MAX,0],
    },
  });
  map.addLayer({
    id: 'pins-circle', type: 'circle', source: 'survey-pins',
    paint: {
      'circle-radius': ['interpolate',['linear'],['zoom'], 10,10, 13,14, PIN_ZOOM_MAX,16],
      'circle-color': statusColorExpr(),
      'circle-stroke-width': 2.5, 'circle-stroke-color': '#fff',
      'circle-opacity':        ['interpolate',['linear'],['zoom'], POLY_ZOOM_MIN,1, PIN_ZOOM_MAX,0],
      'circle-stroke-opacity': ['interpolate',['linear'],['zoom'], POLY_ZOOM_MIN,1, PIN_ZOOM_MAX,0],
    },
  });
  map.addLayer({
    id: 'pins-project-ring', type: 'circle', source: 'survey-pins',
    paint: {
      'circle-radius': ['interpolate',['linear'],['zoom'], 10,13, 13,17, PIN_ZOOM_MAX,19],
      'circle-color': 'rgba(0,0,0,0)',
      'circle-stroke-width': 3, 'circle-stroke-color': projectColorExpr(),
      'circle-opacity': 0,
      'circle-stroke-opacity': ['interpolate',['linear'],['zoom'], POLY_ZOOM_MIN,0.85, PIN_ZOOM_MAX,0],
    },
  });
  map.addLayer({
    id: 'pins-label', type: 'symbol', source: 'survey-pins',
    layout: { 'text-field': ['get','survey_number'], 'text-size': 10, 'text-font': ['Open Sans Bold','Arial Unicode MS Bold'], 'text-anchor': 'center' },
    paint: {
      'text-color': '#fff',
      'text-opacity': ['interpolate',['linear'],['zoom'], POLY_ZOOM_MIN,1, PIN_ZOOM_MAX,0],
    },
  });

  /* 9. POI layers — highways = line layers, others = circle + label */
  addPoiLayers();

  /* 10. Interactions */
  map.on('click', 'pins-circle', e => {
    const p = e.features[0].properties;
    map.flyTo({ center: [+p.centroid_lng, +p.centroid_lat], zoom: 17, speed: 1.2, curve: 1.4 });
    openSidebar(p);
  });
  map.on('mouseenter', 'pins-circle', () => map.getCanvas().style.cursor = 'pointer');
  map.on('mouseleave', 'pins-circle', () => map.getCanvas().style.cursor = '');
  map.on('click', 'surveys-fill', e => openSidebar(e.features[0].properties));

  let hoveredId = null;
  map.on('mousemove', 'surveys-fill', e => {
    map.getCanvas().style.cursor = 'pointer';
    if (e.features.length > 0) {
      if (hoveredId !== null) map.setFeatureState({ source:'surveys', id: hoveredId }, { hover: false });
      hoveredId = e.features[0].id;
      map.setFeatureState({ source:'surveys', id: hoveredId }, { hover: true });
    }
  });
  map.on('mouseleave', 'surveys-fill', () => {
    map.getCanvas().style.cursor = '';
    if (hoveredId !== null) map.setFeatureState({ source:'surveys', id: hoveredId }, { hover: false });
    hoveredId = null;
  });

  /* 11. Zoom hint */
  const hint = document.getElementById('zoomHint');
  function updateZoomHint() { hint.classList.toggle('show', map.getZoom() < PIN_ZOOM_MAX); }
  map.on('zoom', updateZoomHint);
  updateZoomHint();

  /* 12. SSE */
  startSSE();
});

/* ═══════════════════════════════════════════════════════════════
   POI LAYER INITIALISATION
═══════════════════════════════════════════════════════════════ */
function addPoiLayers() {
  Object.entries(POI_CATS).forEach(([cat, cfg]) => {
    if (cfg.type === 'highway') {
      /* Road line layer */
      map.addLayer({
        id:     cfg.layerId,
        type:   'line',
        source: `${cfg.layerId}-src`,
        paint: {
          'line-color': cfg.color,
          'line-width': 4,
          'line-opacity': 0.75,
        },
      });
    } else {
      /* Glow */
      map.addLayer({
        id:     `${cfg.layerId}-glow`,
        type:   'circle',
        source: `${cfg.layerId}-src`,
        paint: { 'circle-radius': 18, 'circle-color': cfg.color, 'circle-opacity': 0.15, 'circle-blur': 0.6 },
      });
      /* Main dot */
      map.addLayer({
        id:     cfg.layerId,
        type:   'circle',
        source: `${cfg.layerId}-src`,
        paint: {
          'circle-radius': 9,
          'circle-color': cfg.color,
          'circle-stroke-width': 2,
          'circle-stroke-color': '#fff',
          'circle-opacity': 0.9,
        },
      });
      /* Name label */
      map.addLayer({
        id:     `${cfg.layerId}-labels`,
        type:   'symbol',
        source: `${cfg.layerId}-labels-src`,
        layout: {
          'text-field':  ['get','name'],
          'text-size':   11,
          'text-font':   ['Open Sans Regular','Arial Unicode MS Regular'],
          'text-anchor': 'top',
          'text-offset': [0, 0.9],
          'text-max-width': 10,
        },
        paint: {
          'text-color': cfg.color,
          'text-halo-color': '#fff',
          'text-halo-width': 1.5,
        },
      });

      /* Popup on hover */
      map.on('mouseenter', cfg.layerId, e => {
        map.getCanvas().style.cursor = 'pointer';
        const f    = e.features[0];
        const name = f.properties.name || 'Unnamed';
        const type = f.properties.amenity || f.properties.shop || cat;
        poiPopup
          .setLngLat(f.geometry.coordinates)
          .setHTML(`<div class="poi-popup-title">${name}</div><div class="poi-popup-type">${type}</div>`)
          .addTo(map);
      });
      map.on('mouseleave', cfg.layerId, () => {
        map.getCanvas().style.cursor = '';
        poiPopup.remove();
      });
    }
  });
}

/* ═══════════════════════════════════════════════════════════════
   OVERPASS FETCHING
═══════════════════════════════════════════════════════════════ */
async function fetchOverpass(queryStr) {
  const res  = await fetch(OVERPASS_URL, {
    method: 'POST',
    body:   'data=' + encodeURIComponent(queryStr),
  });
  const data = await res.json();
  return data.elements || [];
}

/* Build point GeoJSON from Overpass elements (nodes + ways with center) */
function elementsToPointGeoJson(elements) {
  return {
    type: 'FeatureCollection',
    features: elements
      .map(e => {
        const lat = e.lat ?? e.center?.lat;
        const lon = e.lon ?? e.center?.lon;
        if (!lat || !lon) return null;
        return {
          type: 'Feature',
          geometry: { type: 'Point', coordinates: [lon, lat] },
          properties: {
            name:    e.tags?.name ?? '',
            amenity: e.tags?.amenity ?? '',
            shop:    e.tags?.shop ?? '',
          },
        };
      })
      .filter(Boolean),
  };
}

/* Build line GeoJSON from Overpass highway ways (each way has .geometry array) */
function elementsToLineGeoJson(elements) {
  return {
    type: 'FeatureCollection',
    features: elements
      .filter(e => e.type === 'way' && e.geometry?.length > 1)
      .map(e => ({
        type: 'Feature',
        geometry: {
          type: 'LineString',
          coordinates: e.geometry.map(pt => [pt.lon, pt.lat]),
        },
        properties: {
          name: e.tags?.name ?? e.tags?.ref ?? '',
          highway: e.tags?.highway ?? '',
        },
      })),
  };
}

/* Get the lat/lng centres to search around, based on active project */
function getSearchCentres() {
  if (activeProjectId === 'all') {
    return projectMeta.map(p => ({ lat: p.center_lat, lng: p.center_lng }));
  }
  const p = projectMeta.find(m => String(m.id) === String(activeProjectId));
  return p ? [{ lat: p.center_lat, lng: p.center_lng }] : [];
}

/* Cache key for current state */
function cacheKey(cat) {
  return `${cat}::${activeProjectId}`;
}

/* Fetch POI data for a category, with caching */
async function fetchPoi(cat) {
  const key = cacheKey(cat);
  if (overpassCache.has(key)) return overpassCache.get(key);

  const cfg     = POI_CATS[cat];
  const centres = getSearchCentres();
  if (!centres.length) return { points: EMPTY_FC, lines: EMPTY_FC };

  // Fetch for each project centre in parallel and merge
  const results = await Promise.all(
    centres.map(c => fetchOverpass(cfg.query(c.lat, c.lng, POI_RADIUS_M)))
  );
  const elements = results.flat();

  const result = cfg.type === 'highway'
    ? { lines:  elementsToLineGeoJson(elements),  points: EMPTY_FC }
    : { points: elementsToPointGeoJson(elements), lines:  EMPTY_FC };

  overpassCache.set(key, result);
  return result;
}

/* ═══════════════════════════════════════════════════════════════
   FILTER TOGGLE
═══════════════════════════════════════════════════════════════ */
async function toggleFilter(cat) {
  const btn = document.getElementById(`btn-${cat}`);
  const cfg = POI_CATS[cat];

  if (activeFilters.has(cat)) {
    /* ── Turn OFF ── */
    activeFilters.delete(cat);
    btn.classList.remove('active');

    if (cfg.type === 'highway') {
      map.getSource(`${cfg.layerId}-src`).setData(EMPTY_FC);
    } else {
      map.getSource(`${cfg.layerId}-src`).setData(EMPTY_FC);
      map.getSource(`${cfg.layerId}-labels-src`).setData(EMPTY_FC);
    }
    updatePoiLegend();
    return;
  }

  /* ── Turn ON ── */
  activeFilters.add(cat);
  btn.classList.add('active', 'loading');

  try {
    const data = await fetchPoi(cat);

    if (cfg.type === 'highway') {
      map.getSource(`${cfg.layerId}-src`).setData(data.lines);
      const count = data.lines.features.length;
      document.getElementById(`count-${cat}`).textContent = count;
    } else {
      map.getSource(`${cfg.layerId}-src`).setData(data.points);
      map.getSource(`${cfg.layerId}-labels-src`).setData(data.points);
      const count = data.points.features.length;
      document.getElementById(`count-${cat}`).textContent = count;
    }

    updatePoiLegend();
  } catch (err) {
    activeFilters.delete(cat);
    btn.classList.remove('active');
    showToast('Could not load nearby places. Try again.');
    console.error('Overpass error:', err);
  } finally {
    btn.classList.remove('loading');
  }
}

/* Refresh all active filters (called when project switches) */
async function refreshActiveFilters() {
  for (const cat of activeFilters) {
    const cfg = POI_CATS[cat];
    try {
      const data = await fetchPoi(cat);
      if (cfg.type === 'highway') {
        map.getSource(`${cfg.layerId}-src`).setData(data.lines);
        document.getElementById(`count-${cat}`).textContent = data.lines.features.length;
      } else {
        map.getSource(`${cfg.layerId}-src`).setData(data.points);
        map.getSource(`${cfg.layerId}-labels-src`).setData(data.points);
        document.getElementById(`count-${cat}`).textContent = data.points.features.length;
      }
    } catch { /* silent */ }
  }
}

/* Update the POI section inside the legend */
function updatePoiLegend() {
  const section = document.getElementById('legendPoi');
  const items   = document.getElementById('legendPoiItems');

  if (!activeFilters.size) {
    section.style.display = 'none';
    return;
  }
  section.style.display = 'block';
  items.innerHTML = [...activeFilters].map(cat => {
    const cfg = POI_CATS[cat];
    if (cfg.type === 'highway') {
      return `<div class="legend-poi"><div class="poi-highway-line" style="background:${cfg.color}"></div>${cfg.label}</div>`;
    }
    return `<div class="legend-poi"><div class="legend-poi-dot" style="background:${cfg.color}"></div>${cfg.label}</div>`;
  }).join('');
}

/* Filter button click handlers */
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    if (btn.classList.contains('loading')) return;
    toggleFilter(btn.dataset.cat);
  });
});

/* ═══════════════════════════════════════════════════════════════
   FIT MAP
═══════════════════════════════════════════════════════════════ */
function fitMapToBothProjects() {
  if (!currentGeojson.features.length) return;
  let minLat = Infinity, maxLat = -Infinity, minLng = Infinity, maxLng = -Infinity;
  currentGeojson.features.forEach(f => {
    const lat = +f.properties.centroid_lat, lng = +f.properties.centroid_lng;
    if (!lat || !lng) return;
    minLat = Math.min(minLat, lat); maxLat = Math.max(maxLat, lat);
    minLng = Math.min(minLng, lng); maxLng = Math.max(maxLng, lng);
  });
  if (minLat === Infinity) return;
  map.fitBounds([[minLng-.01, minLat-.01],[maxLng+.01, maxLat+.01]], { padding: 60, maxZoom: 14, duration: 800 });
}

/* ═══════════════════════════════════════════════════════════════
   PROJECT TABS
═══════════════════════════════════════════════════════════════ */
function renderProjectTabs() {
  const nav = document.getElementById('projectTabs');
  nav.innerHTML = '<button class="proj-tab active" data-pid="all">All Projects</button>';
  projectMeta.forEach(p => {
    const btn = document.createElement('button');
    btn.className = 'proj-tab';
    btn.dataset.pid = p.id;
    btn.textContent = p.name;
    btn.style.borderColor = p.color;
    nav.appendChild(btn);
  });
  nav.addEventListener('click', e => {
    const btn = e.target.closest('.proj-tab');
    if (!btn) return;
    nav.querySelectorAll('.proj-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    switchProject(btn.dataset.pid);
  });
}

function renderLegendProjects() {
  document.getElementById('legendProjects').innerHTML = projectMeta.map(p => `
    <div class="legend-proj">
      <div class="legend-proj-line" style="background:${p.color}"></div>
      ${p.name} <span style="color:#999;font-weight:400;font-size:11px">(${p.survey_count})</span>
    </div>
  `).join('');
}

async function switchProject(pid) {
  activeProjectId = pid;
  const visible   = filterGeoJson(currentGeojson);
  map.getSource('surveys')?.setData(visible);
  map.getSource('survey-pins')?.setData(buildPinGeoJson(visible));

  if (pid === 'all') {
    fitMapToBothProjects();
  } else {
    const meta = projectMeta.find(p => String(p.id) === String(pid));
    if (meta) map.flyTo({ center: [meta.center_lng, meta.center_lat], zoom: 15, speed: 1.3 });
  }

  /* Refresh any active POI filters for the new area (clear cache for this project) */
  activeFilters.forEach(cat => overpassCache.delete(cacheKey(cat)));
  restartSSE();
  await refreshActiveFilters();
}

/* ═══════════════════════════════════════════════════════════════
   SSE
═══════════════════════════════════════════════════════════════ */
function startSSE() {
  const dot = document.getElementById('sseDot'), label = document.getElementById('sseLabel');
  sseSource = new EventSource(`${API_BASE}/surveys/stream`);
  sseSource.onopen = () => { dot.classList.add('connected'); label.textContent = 'Live'; };
  sseSource.onmessage = e => {
    currentGeojson = JSON.parse(e.data);
    const visible  = filterGeoJson(currentGeojson);
    map.getSource('surveys')?.setData(visible);
    map.getSource('survey-pins')?.setData(buildPinGeoJson(visible));
    map.getSource('proj-boundaries')?.setData(buildProjectBoundaryGeoJson(currentGeojson));
    if (selectedSurveyId !== null) {
      const upd = currentGeojson.features.find(f => f.properties.id === selectedSurveyId);
      if (upd) refreshSidebarStatus(upd.properties.status);
    }
  };
  sseSource.onerror = () => { dot.classList.remove('connected'); label.textContent = 'Reconnecting…'; };
}

function restartSSE() {
  sseSource?.close();
  sseSource = null;
  startSSE();
}

/* ═══════════════════════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════════════════════ */
function openSidebar(props) {
  selectedSurveyId = props.id;
  const projMeta   = projectMeta.find(p => String(p.id) === String(props.project_id));
  const projName   = projMeta?.name ?? `Project ${props.project_id}`;
  const projClr    = projMeta?.color ?? '#6366f1';

  const badge = document.getElementById('sBadge');
  badge.textContent = 'Survey ' + props.survey_number;
  badge.style.background = '#1b2a3b';

  const chip = document.getElementById('sProjChip');
  chip.textContent      = projName;
  chip.style.background = projClr + '22';
  chip.style.color      = projClr;
  chip.style.border     = `1px solid ${projClr}55`;

  document.getElementById('sTitle').textContent = props.title || 'Survey ' + props.survey_number;
  document.getElementById('sDesc').textContent  = props.description || 'No description available.';
  document.getElementById('sPrice').textContent = props.price ? '₹' + Number(props.price).toLocaleString('en-IN') : '—';
  document.getElementById('sArea').textContent  = props.area_sqft ? Number(props.area_sqft).toLocaleString() + ' sq.ft' : '—';

  const imgEl = document.getElementById('sidebarImg');
  imgEl.innerHTML = props.image_url
    ? `<img class="sidebar-img" src="${props.image_url}" alt="${props.title}"/>`
    : `<div class="sidebar-img-placeholder" style="background:linear-gradient(135deg,${projClr},${projClr}99)">🌿</div>`;

  refreshSidebarStatus(props.status);
  document.getElementById('statusSelect').value    = props.status;
  document.getElementById('updateMsg').textContent = '';
  document.getElementById('sidebar').classList.add('open');
}

function refreshSidebarStatus(status) {
  const labels = { available:'Available', need_to_acquire:'Need to Acquire', acquired:'Acquired', reserved:'Reserved', sold:'Sold' };
  document.getElementById('sStatus').innerHTML = `
    <div class="status-badge status-${status}">
      <span class="dot"></span>${labels[status] || status}
    </div>`;
}

document.getElementById('closeBtn').addEventListener('click', () => {
  document.getElementById('sidebar').classList.remove('open');
  selectedSurveyId = null;
});

/* ═══════════════════════════════════════════════════════════════
   STATUS UPDATE
═══════════════════════════════════════════════════════════════ */
document.getElementById('updateBtn').addEventListener('click', async () => {
  if (!selectedSurveyId) return;
  const btn = document.getElementById('updateBtn'), msg = document.getElementById('updateMsg');
  const status = document.getElementById('statusSelect').value;
  btn.disabled = true; btn.textContent = 'Updating…'; msg.textContent = '';
  try {
    const res  = await fetch(`${API_BASE}/surveys/${selectedSurveyId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:   JSON.stringify({ status }),
    });
    const data = await res.json();
    if (res.ok) {
      msg.textContent = '✓ Status updated!'; msg.className = 'update-msg success';
      refreshSidebarStatus(status);
      showToast(`Survey ${data.survey_number} → ${status.replace(/_/g,' ')}`);
    } else {
      msg.textContent = data.message || 'Update failed'; msg.className = 'update-msg error';
    }
  } catch {
    msg.textContent = 'Network error. Try again.'; msg.className = 'update-msg error';
  } finally {
    btn.disabled = false; btn.textContent = 'Update Status';
  }
});

document.getElementById('shareBtn').addEventListener('click', () => {
  navigator.clipboard.writeText(window.location.href);
  showToast('Link copied to clipboard!');
});

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}
</script>
</body>
</html>