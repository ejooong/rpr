<!-- resources/views/gis/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Peta GIS Demplot - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Visualisasi sebaran demplot seluruh Indonesia</p>
    </x-slot>



    <!-- Map Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div id="map" class="w-full h-[900px] rounded-lg border border-gray-300"></div>
            <div id="mapInfo" class="mt-4 text-sm text-gray-600">
                Memuat data demplot...
            </div>
        </div>

    </div>

@push('styles')
<style>
    /* Custom colors matching the Gandiwa theme */
    :root {
        --primary-color: #2A7BE4;
        --success-color: #00B27A;
        --warning-color: #FF9F43;
        --danger-color: #EA5455;
        --info-color: #00CFE8;
        --dark-color: #4B4B4B;
        --light-color: #F8F9FA;
    }

    .bg-primary-600 {
        background-color: var(--primary-color);
    }

    .bg-primary-600:hover {
        background-color: #1c6ed8;
    }

    .peta-control {
        background: rgba(255,255,255,0.98) !important;
        backdrop-filter: blur(10px);
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .leaflet-popup-tip {
        box-shadow: none;
    }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
if (!window._petaDemplotInitialized) {
  window._petaDemplotInitialized = true;

  (function(){
    // --- prevent Leaflet trying to load shadow png from CDN (optional) ---
    try { L.Icon.Default.mergeOptions({ shadowUrl: '' }); } catch(e){}

    // --- Paths (blade route helpers) ---
    const GEOJSON_PROV_PATH = '/geojson/38_provinsi_indonesia.json';
    const GEOJSON_KAB_PATH  = '/geojson/38_provinsi_indonesia_kabupaten.json';
    const API_DEMPLOT = "{{ route('gis.api.demplot') }}";
    const API_COUNTS = "{{ route('gis.api.demplot.counts') }}";

    // cache ttl (ms) -> 24 jam
    const GEO_CACHE_TTL = 24 * 60 * 60 * 1000;

    // global state
    let map = null;
    let demplotData = [];
    let markers = [];
    let geoLayerProv = null;
    let geoLayerKab = null;
    let currentChoroplethProv = null;

    // ----- helpers -----
    function normalizeName(s) {
      if (!s) return '';
      return String(s).toLowerCase().trim()
        .replace(/^kabupaten\s+/i, '')
        .replace(/^kota\s+/i, '')
        .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()"]/g,' ')
        .replace(/\s+/g,' ')
        .trim();
    }

    function detectProperty(keys, props) {
      for (const k of keys) {
        if (props[k] !== undefined && props[k] !== null && String(props[k]).trim() !== '') return k;
      }
      return null;
    }

    async function loadGeoJsonWithCache(path, cacheKey) {
      try {
        const raw = localStorage.getItem(cacheKey);
        if (raw) {
          const parsed = JSON.parse(raw);
          if (Date.now() - parsed._ts < GEO_CACHE_TTL && parsed.data) return parsed.data;
        }
        const res = await fetch(path);
        if (!res.ok) throw new Error('404 ' + path);
        const json = await res.json();
        try { localStorage.setItem(cacheKey, JSON.stringify({ _ts: Date.now(), data: json })); } catch(e){ /* ignore */ }
        return json;
      } catch(err) {
        console.warn('Load geojson error', err);
        throw err;
      }
    }

    // --- UI: control + monitor ---
    function createMapControlUI() {
      const container = document.createElement('div');
      container.className = 'peta-control';
      container.style.position = 'absolute';
      container.style.top = '10px';
      container.style.left = '10px';
      container.style.zIndex = 1000;
      container.style.padding = '12px';
      container.style.borderRadius = '10px';
      container.style.fontSize = '14px';
      container.style.width = '260px';
      container.innerHTML = `
        
        <select id="mapLayerSelect" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;margin-bottom:10px;background:white;">
         
          <option value="provinsi">Provinsi</option>
          <option value="kabupaten">Kabupaten</option>
         
        </select>
        <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px">
          <button id="btnResetMarkers" style="padding:8px 12px;border-radius:6px;border:1px solid #ddd;background:#f8f9fa;cursor:pointer;font-weight:500;color:#4B4B4B">Reset Peta</button>
        </div>
        
      `;
      document.getElementById('map').appendChild(container);

      document.getElementById('mapLayerSelect').addEventListener('change', function(){
        const v = this.value;
        if (v === 'both') { 
            if (geoLayerProv && !map.hasLayer(geoLayerProv)) geoLayerProv.addTo(map); 
            if (geoLayerKab && !map.hasLayer(geoLayerKab)) geoLayerKab.addTo(map); 
        }
        else if (v === 'provinsi') { 
            if (geoLayerProv && !map.hasLayer(geoLayerProv)) geoLayerProv.addTo(map); 
            if (geoLayerKab && map.hasLayer(geoLayerKab)) map.removeLayer(geoLayerKab); 
        }
        else if (v === 'kabupaten') { 
            if (geoLayerKab && !map.hasLayer(geoLayerKab)) geoLayerKab.addTo(map); 
            if (geoLayerProv && map.hasLayer(geoLayerProv)) map.removeLayer(geoLayerProv); 
        }
        else { 
            if (geoLayerProv && map.hasLayer(geoLayerProv)) map.removeLayer(geoLayerProv); 
            if (geoLayerKab && map.hasLayer(geoLayerKab)) map.removeLayer(geoLayerKab); 
        }
      });

      document.getElementById('btnResetMarkers').addEventListener('click', () => {
        restoreDefaultView();
      });
    }

    function createMonitorPanel() {
      let panel = document.getElementById('monitorPanel');
      if (panel) return panel;
      panel = document.createElement('div');
      panel.id = 'monitorPanel';
      panel.style.position = 'absolute';
      panel.style.top = '80px';
      panel.style.right = '10px';
      panel.style.width = '340px';
      panel.style.maxHeight = '75vh';
      panel.style.overflow = 'auto';
      panel.style.zIndex = 1100;
      panel.style.background = '#fff';
      panel.style.borderRadius = '10px';
      panel.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
      panel.style.padding = '16px';
      panel.style.fontSize = '13px';
      panel.style.display = 'none';
      panel.style.border = '1px solid #e5e7eb';
      panel.innerHTML = `<div style="font-weight:700;margin-bottom:12px;color:#2A7BE4;font-size:14px">Monitor: <span id="monitorProvName"></span></div><div id="monitorList"></div>`;
      document.getElementById('map').appendChild(panel);
      return panel;
    }

    function showMonitor(provName, items) {
      const panel = createMonitorPanel();
      document.getElementById('monitorProvName').innerText = provName;
      const list = document.getElementById('monitorList');
      list.innerHTML = '';
      
      const headerRow = document.createElement('div');
      headerRow.style.display = 'flex';
      headerRow.style.justifyContent = 'space-between';
      headerRow.style.alignItems = 'center';
      headerRow.style.padding = '12px 8px';
      headerRow.style.borderBottom = '2px solid #e5e7eb';
      headerRow.style.marginBottom = '12px';
      headerRow.style.background = '#f8f9fa';
      headerRow.style.borderRadius = '6px';
      
      const totalDemplot = items.reduce((sum, item) => sum + item.count, 0);
      
      headerRow.innerHTML = `
        <div>
          <div style="font-weight:600;font-size:11px;color:#6b7280;text-transform:uppercase">TOTAL DEMPLOT</div>
          <div style="font-weight:800;font-size:20px;color:#2A7BE4">${totalDemplot}</div>
        </div>
        <div style="display:flex;gap:8px">
          <button class="btn-show-all-province" style="padding:8px 12px;border-radius:6px;border:none;background:#2A7BE4;color:white;cursor:pointer;font-size:12px;font-weight:600">
            Tampilkan Semua
          </button>
          <button class="btn-close-monitor" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;background:#f3f4f6;color:#6b7280;cursor:pointer;font-size:12px;font-weight:600">×</button>
        </div>
      `;
      
      list.appendChild(headerRow);

      items.sort((a,b)=>b.count - a.count);
      items.forEach(it=>{
        const row = document.createElement('div');
        row.style.display = 'flex';
        row.style.justifyContent = 'space-between';
        row.style.alignItems = 'center';
        row.style.padding = '10px 8px';
        row.style.borderBottom = '1px solid #f0f0f0';
        row.style.transition = 'background-color 0.2s';

        row.onmouseenter = () => row.style.background = '#f8f9fa';
        row.onmouseleave = () => row.style.background = 'transparent';

        const left = document.createElement('div');
        left.innerHTML = `<div style="font-weight:600;color:#374151">${it.name}</div><div style="font-size:11px;color:#6b7280">${it.kab_code ? ('Kode: '+it.kab_code) : ''}</div>`;
        const right = document.createElement('div');
        right.style.display = 'flex';
        right.style.gap = '8px';
        right.style.alignItems = 'center';
        right.innerHTML = `<div style="background:${getCountColor(it.count)};padding:6px 10px;border-radius:6px;color:#fff;font-weight:700;min-width:40px;text-align:center">${it.count}</div><button class="btn-show-markers" data-kab="${it.name}" style="padding:6px 10px;border-radius:6px;border:1px solid #2A7BE4;background:transparent;color:#2A7BE4;cursor:pointer;font-size:11px">Tampilkan</button>`;
        row.appendChild(left);
        row.appendChild(right);
        list.appendChild(row);
      });

      list.querySelector('.btn-show-all-province').addEventListener('click', () => {
        showMarkersForProvince(provName);
      });

      list.querySelector('.btn-close-monitor').addEventListener('click', () => {
        hideMonitor();
      });

      list.querySelectorAll('.btn-show-markers').forEach(btn=>{
        btn.addEventListener('click', ev=>{
          const kabName = btn.getAttribute('data-kab');
          showMarkersForKabupaten(kabName);
        });
      });

      panel.style.display = 'block';
    }

    function hideMonitor() {
      const panel = document.getElementById('monitorPanel');
      if (panel) panel.style.display = 'none';
    }

    function getCountColor(count) {
      if (!count || count === 0) return '#9ca3af';
      if (count <= 3) return '#60a5fa';
      if (count <= 10) return '#3b82f6';
      if (count <= 20) return '#2563eb';
      return '#1d4ed8';
    }

    function updateLegend() {
      const el = document.getElementById('petaLegend');
      if (!el) return;
      el.innerHTML = `
        <div style="font-weight:600;margin-bottom:8px;color:#374151">Legenda (Jumlah Demplot)</div>
        <div style="display:flex;flex-direction:column;gap:4px">
          <div style="display:flex;align-items:center"><span style="display:inline-block;width:14px;height:14px;background:#9ca3af;margin-right:8px;border-radius:2px"></span> 0</div>
          <div style="display:flex;align-items:center"><span style="display:inline-block;width:14px;height:14px;background:#60a5fa;margin-right:8px;border-radius:2px"></span> 1 - 3</div>
          <div style="display:flex;align-items:center"><span style="display:inline-block;width:14px;height:14px;background:#3b82f6;margin-right:8px;border-radius:2px"></span> 4 - 10</div>
          <div style="display:flex;align-items:center"><span style="display:inline-block;width:14px;height:14px;background:#2563eb;margin-right:8px;border-radius:2px"></span> 11 - 20</div>
          <div style="display:flex;align-items:center"><span style="display:inline-block;width:14px;height:14px;background:#1d4ed8;margin-right:8px;border-radius:2px"></span> > 20</div>
        </div>
      `;
    }

    // --- markers functions ---
    function clearMarkers() {
      markers.forEach(m => map.removeLayer(m));
      markers = [];
    }

function createMarker(d) {
  if (!d.latitude || !d.longitude) return null;
  try {
    // Custom icon dengan logo NasDem
    const customIcon = L.divIcon({
      className: 'custom-nasdem-marker',
      html: `
        <div style="
          background: linear-gradient(135deg, #1E3A8A 0%, #0A8BCC 50%, #0F3376 100%);
          width: 32px;
          height: 32px;
          border-radius: 50%;
          border: 3px solid white;
          box-shadow: 0 4px 12px rgba(0,0,0,0.3);
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: bold;
          font-size: 14px;
          position: relative;
          overflow: hidden;
        ">
          <div style="
            background: url('{{ asset('images/nasdem.png') }}') no-repeat center center;
            background-size: 18px 18px;
            width: 100%;
            height: 100%;
          "></div>
        </div>
      `,
      iconSize: [32, 32],
      iconAnchor: [16, 16],
      popupAnchor: [0, -16]
    });
    
    const m = L.marker([Number(d.latitude), Number(d.longitude)], { icon: customIcon });
    
    // Enhanced popup content
    const popupContent = `
      <div style="min-width: 250px; font-family: system-ui, sans-serif;">
        <div style="background: linear-gradient(135deg, #1E3A8A, #0A8BCC); padding: 12px; border-radius: 8px 8px 0 0; color: white;">
          <div style="font-weight: bold; font-size: 16px;">📍 Demplot RPR NasDem</div>
        </div>
        <div style="padding: 12px;">
          <div style="margin-bottom: 8px;">
            <strong>Lokasi:</strong><br>
            ${d.kabupaten ? d.kabupaten + ', ' : ''}${d.provinsi || 'Lokasi tidak diketahui'}
          </div>
          ${d.nama_demplot ? `<div style="margin-bottom: 8px;"><strong>Nama:</strong> ${d.nama_demplot}</div>` : ''}
          ${d.komoditas ? `<div style="margin-bottom: 8px;"><strong>Komoditas:</strong> ${d.komoditas}</div>` : ''}
          ${d.luas_lahan ? `<div style="margin-bottom: 8px;"><strong>Luas Lahan:</strong> ${d.luas_lahan} Ha</div>` : ''}
          ${d.status ? `<div style="margin-bottom: 8px;"><strong>Status:</strong> ${d.status}</div>` : ''}
          <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
            <strong>RPR NasDem</strong> - Rumah Pangan Rakyat
          </div>
        </div>
      </div>
    `;
    
    m.bindPopup(popupContent);
    return m;
  } catch(e){ 
    console.error('Error creating marker:', e);
    return null; 
  }
}

    function showMarkersForKabupaten(kabName) {
      clearMarkers();
      const nk = normalizeName(kabName);
      
      let filtered = demplotData;
      let provName = '';
      
      if (currentChoroplethProv) {
        const provProps = currentChoroplethProv.properties || {};
        provName = (provProps.WADMPR || provProps.PROVINSI || provProps.NAME || provProps.NAMA || '').toString();
        const provNorm = normalizeName(provName);
        
        filtered = demplotData.filter(d => 
          normalizeName(d.kabupaten) === nk && 
          normalizeName(d.provinsi) === provNorm
        );
      } else {
        filtered = demplotData.filter(d => normalizeName(d.kabupaten) === nk);
      }
      
      filtered.forEach(d => {
        const m = createMarker(d);
        if (m) { m.addTo(map); markers.push(m); }
      });
      
      if (markers.length) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds(), { padding: [40,40] });
        
        Swal.fire({
          icon: 'success',
          title: `Ditemukan ${markers.length} demplot`,
          text: `Di ${kabName}${provName ? `, ${provName}` : ''}`,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          background: '#f0f9ff',
          iconColor: '#2A7BE4'
        });
        
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Tidak ada demplot',
          html: `
            <div class="text-center">
              <div class="text-6xl mb-4">📭</div>
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak ada demplot di ${kabName}</h3>
              <p class="text-gray-600 text-sm">
                ${provName ? `Pada provinsi <strong>${provName}</strong>, ` : ''}
                tidak ditemukan data demplot di kabupaten <strong>${kabName}</strong>.
              </p>
            </div>
          `,
          showConfirmButton: true,
          confirmButtonText: 'Mengerti',
          confirmButtonColor: '#2A7BE4',
          background: '#fff',
          customClass: {
            popup: 'rounded-2xl shadow-xl',
            title: 'text-gray-800 font-semibold'
          }
        });
      }
    }

    function restoreDefaultView() {
      clearMarkers();
      restoreKabupatenStyle();
      hideMonitor();
      updateMapInfo(demplotData.length);
    }

    function updateMapInfo(total) {
      document.getElementById('mapInfo').innerText = `Menampilkan ${total} demplot`;
    }

    // --- choropleth: server counts (preferred) with client fallback ---
    async function styleKabupatenChoroplethForProvince(provFeature) {
      if (!geoLayerKab) return;
      const props = provFeature.properties || {};
      const provName = (props.WADMPR || props.PROVINSI || props.NAME || props.NAMA || '').toString();
      const provCode = props.KDPROV || props.KODE_PROV || props.PROV_CODE || null;

      Swal.fire({
        title: 'Memuat data...',
        text: `Sedang mengambil data untuk ${provName}`,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        const url = new URL(API_COUNTS, window.location.origin);
        url.searchParams.append('provinsi_name', provName);
        const res = await fetch(url.toString());
        if (!res.ok) throw new Error('Server ' + res.status);
        const json = await res.json();
        if (!json.success || !Array.isArray(json.data)) throw new Error('Invalid response');

        const counts = {};
        json.data.forEach(r => {
          const raw = r.kabupaten || r.nama || r.name || '';
          counts[normalizeName(raw)] = { name: raw, count: Number(r.total || 0), kab_code: r.kabupaten_kode || null };
        });

        geoLayerKab.eachLayer(layer => {
          const p = layer.feature?.properties || {};
          
          const kabProvCode = p.KDPROV || p.KODE_PROV || p.PROV_CODE || null;
          const kabProvName = p.WADMPR || p.PROVINSI || p.NAME || p.NAMA || '';
          
          if (provCode && kabProvCode !== provCode) {
            layer.setStyle({ fillColor: '#f8f9fa', fillOpacity: 0.1, color: '#e5e7eb', weight: 0.5 });
            layer.bindTooltip('', { sticky: true });
            return;
          }
          
          if (!provCode && normalizeName(kabProvName) !== normalizeName(provName)) {
            layer.setStyle({ fillColor: '#f8f9fa', fillOpacity: 0.1, color: '#e5e7eb', weight: 0.5 });
            layer.bindTooltip('', { sticky: true });
            return;
          }

          const nameKey = detectProperty(['WADMKK','WADMKC','WADMKD','NAMOBJ','KABUPATEN','NAMA','NAME'], p);
          const rawName = nameKey ? p[nameKey] : (p.KABUPATEN || p.NAMA || p.NAME || '');
          const nk = normalizeName(rawName);
          const entry = counts[nk] || { name: rawName, count: 0, kab_code: null };
          
          const color = getCountColor(entry.count);
          layer.setStyle({ 
            fillColor: color, 
            fillOpacity: 0.7, 
            color: '#374151', 
            weight: 1.5,
            opacity: 0.8
          });
          layer.bindTooltip(`<div style="font-weight:600">${rawName}</div><div>${entry.count} demplot</div>`, { 
            sticky: true,
            className: 'custom-tooltip'
          });
        });

        const items = Object.values(counts).map(v => ({ name: v.name, count: v.count, kab_code: v.kab_code }));
        items.sort((a,b) => b.count - a.count);
        
        Swal.close();
        showMarkersForProvince(provName, provCode);
        showMonitor(provName || 'Provinsi', items);
        currentChoroplethProv = provFeature;
        updateLegend();
        
      } catch (err) {
        console.warn('Counts API failed, using client fallback', err);
        Swal.close();
        
        const provNorm = normalizeName(provName);
        const mapCounts = {};
        
        demplotData.forEach(d => {
          if (normalizeName(d.provinsi) !== provNorm) return;
          const nk = normalizeName(d.kabupaten || '');
          if (!mapCounts[nk]) mapCounts[nk] = { name: d.kabupaten || 'Unknown', count: 0, kab_code: d.kabupaten_kode || null };
          mapCounts[nk].count++;
        });

        geoLayerKab.eachLayer(layer => {
          const p = layer.feature?.properties || {};
          
          const kabProvCode = p.KDPROV || p.KODE_PROV || p.PROV_CODE || null;
          const kabProvName = p.WADMPR || p.PROVINSI || p.NAME || p.NAMA || '';
          
          if (provCode && kabProvCode !== provCode) {
            layer.setStyle({ fillColor: '#f8f9fa', fillOpacity: 0.1, color: '#e5e7eb', weight: 0.5 });
            layer.bindTooltip('', { sticky: true });
            return;
          }
          
          if (!provCode && normalizeName(kabProvName) !== normalizeName(provName)) {
            layer.setStyle({ fillColor: '#f8f9fa', fillOpacity: 0.1, color: '#e5e7eb', weight: 0.5 });
            layer.bindTooltip('', { sticky: true });
            return;
          }

          const nameKey = detectProperty(['WADMKK','WADMKC','WADMKD','NAMOBJ','KABUPATEN','NAMA','NAME'], p);
          const rawName = nameKey ? p[nameKey] : (p.KABUPATEN||props.NAME||props.NAMA||'');
          const nk = normalizeName(rawName);
          
          if (!mapCounts[nk]) mapCounts[nk] = { name: rawName, count: 0, kab_code: null };
          
          const color = getCountColor(mapCounts[nk].count);
          layer.setStyle({ 
            fillColor: color, 
            fillOpacity: 0.7, 
            color: '#374151', 
            weight: 1.5,
            opacity: 0.8
          });
          layer.bindTooltip(`<div style="font-weight:600">${rawName}</div><div>${mapCounts[nk].count} demplot</div>`, { 
            sticky: true,
            className: 'custom-tooltip'
          });
        });

        const items = Object.values(mapCounts).map(v => ({ name: v.name, count: v.count, kab_code: v.kab_code }));
        items.sort((a,b) => b.count - a.count);
        
        showMarkersForProvince(provName, provCode);
        showMonitor(provName || 'Provinsi', items);
        updateLegend();
      }
    }

function showMarkersForProvince(provName, provCode = null) {
  clearMarkers();
  
  let filtered = demplotData;
  
  if (provCode) {
    filtered = demplotData.filter(d => {
      const dProvCode = d.provinsi_kode || '';
      return String(dProvCode) === String(provCode);
    });
  } else {
    const provNorm = normalizeName(provName);
    filtered = demplotData.filter(d => normalizeName(d.provinsi) === provNorm);
  }
  
  filtered.forEach(d => {
    const m = createMarker(d);
    if (m) { 
      m.addTo(map); 
      markers.push(m); 
    }
  });
  
  if (markers.length > 0) {
    const group = L.featureGroup(markers);
    
    // PERBAIKAN: Tambahkan padding lebih besar dan batasi zoom minimum
    const bounds = group.getBounds();
    const boundsSize = bounds.getNorthEast().distanceTo(bounds.getSouthWest());
    
    // Jika bounds terlalu kecil (kurang dari 50km), gunakan zoom yang lebih reasonable
    if (boundsSize < 50000) { // 50km dalam meter
      // Cari center point dan set zoom yang sesuai
      const center = bounds.getCenter();
      map.setView(center, 9, { // Zoom level 9 biasanya cukup untuk melihat kabupaten
        animate: true,
        duration: 1
      });
    } else {
      map.fitBounds(bounds, { 
        padding: [60, 60], // PERBAIKAN: Tambahkan padding lebih besar
        maxZoom: 12 // PERBAIKAN: Batasi zoom maksimum
      });
    }
    
    Swal.fire({
      icon: 'success',
      title: `Menampilkan ${markers.length} demplot`,
      text: `Di provinsi ${provName}`,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: '#f0f9ff',
      iconColor: '#2A7BE4'
    });
        
        updateMapInfo(`${markers.length} demplot di ${provName}`);
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Tidak ada demplot',
          html: `
            <div class="text-center">
              <div class="text-6xl mb-4">🏢</div>
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak ada demplot di ${provName}</h3>
              <p class="text-gray-600 text-sm">
                Tidak ditemukan data demplot di provinsi <strong>${provName}</strong>.
              </p>
            </div>
          `,
          showConfirmButton: true,
          confirmButtonText: 'Mengerti',
          confirmButtonColor: '#2A7BE4',
          background: '#fff',
          customClass: {
            popup: 'rounded-2xl shadow-xl',
            title: 'text-gray-800 font-semibold'
          }
        });
        
        updateMapInfo(`Tidak ada demplot di ${provName}`);
      }
    }

    function restoreKabupatenStyle() {
      if (!geoLayerKab) return;
      geoLayerKab.eachLayer(layer => {
        layer.setStyle({ 
          fillColor: '#f0f4ff', 
          fillOpacity: 0.4, 
          color: '#94a3b8', 
          weight: 1,
          opacity: 0.6
        });
        
        const p = layer.feature?.properties || {};
        const nameKey = detectProperty(['WADMKK','WADMKC','WADMKD','NAMOBJ','KABUPATEN','NAMA','NAME'], p);
        const rawName = nameKey ? p[nameKey] : (p.KABUPATEN || p.NAMA || p.NAME || '');
        layer.bindTooltip(rawName || '', { 
          sticky: true,
          className: 'custom-tooltip'
        });
      });
      hideMonitor();
      currentChoroplethProv = null;
    }

    // --- load geo layers ---
    async function initGeoLayers() {
      try {
        const provJson = await loadGeoJsonWithCache(GEOJSON_PROV_PATH, 'geo.prov');
        geoLayerProv = L.geoJSON(provJson, {
          style: { 
            color: '#64748b', 
            weight: 2, 
            fillColor: '#f1f5f9', 
            fillOpacity: 0.3,
            opacity: 0.7
          },
          onEachFeature: (feature, layer)=>{
            const p = feature.properties || {};
            const provName = p.WADMPR || p.PROVINSI || p.NAME || p.NAMA || 'Provinsi';
            layer.bindTooltip(`<div style="font-weight:600">${String(provName)}</div>`, { 
              sticky: true,
              className: 'custom-tooltip'
            });
            layer.on('click', () => {
              map.fitBounds(layer.getBounds(), { padding: [20,20] });
              styleKabupatenChoroplethForProvince(feature);
            });
            layer.on('mouseover', () => layer.setStyle({ weight: 3, color: '#2A7BE4', fillOpacity: 0.4 }));
            layer.on('mouseout',  () => layer.setStyle({ weight: 2, color: '#64748b', fillOpacity: 0.3 }));
          }
        }).addTo(map);

        const kabJson = await loadGeoJsonWithCache(GEOJSON_KAB_PATH, 'geo.kab');
        geoLayerKab = L.geoJSON(kabJson, {
          style: { 
            color: '#94a3b8', 
            weight: 1, 
            fillColor: '#f0f4ff', 
            fillOpacity: 0.4,
            opacity: 0.6
          },
          onEachFeature: (feature, layer)=>{
            const p = feature.properties || {};
            const name = p.WADMKK || p.WADMKC || p.WADMKD || p.NAMOBJ || p.KABUPATEN || p.NAME || p.NAMA || '';
            layer.bindTooltip(String(name), { 
              sticky: true,
              className: 'custom-tooltip'
            });
            layer.on('mouseover', () => layer.setStyle({ weight: 2, color: '#2A7BE4', fillOpacity: 0.5 }));
            layer.on('mouseout',  () => layer.setStyle({ weight: 1, color: '#94a3b8', fillOpacity: 0.4 }));
            layer.on('click', () => {
              const props = layer.feature?.properties || {};
              const nameKey = detectProperty(['WADMKK','WADMKC','WADMKD','NAMOBJ','KABUPATEN','NAMA','NAME'], props);
              const rawName = nameKey ? props[nameKey] : (props.KABUPATEN||props.NAME||props.NAMA||'');
              showMarkersForKabupaten(rawName);
            });
          }
        }).addTo(map);

        // Layer control dengan style yang lebih baik
        const overlays = {};
        if (geoLayerProv) overlays['Provinsi'] = geoLayerProv;
        if (geoLayerKab) overlays['Kabupaten'] = geoLayerKab;
        
        if (Object.keys(overlays).length) {
          L.control.layers(null, overlays, { 
            collapsed: false, 
            position: 'topright',
            className: 'custom-layer-control'
          }).addTo(map);
        }

      } catch(err){
        console.error('Error loading geo layers', err);
        document.getElementById('mapInfo').innerHTML = '<div class="text-red-600">Gagal memuat layer peta (cek console)</div>';
      }
    }

    // --- load demplot data ---
    async function loadDemplotData(filters = {}) {
      try {
        const url = new URL(API_DEMPLOT, window.location.origin);
        Object.keys(filters).forEach(k => { if (filters[k]) url.searchParams.append(k, filters[k]); });
        const res = await fetch(url.toString());
        if (!res.ok) throw new Error('Server demplot ' + res.status);
        const json = await res.json();
        const arr = Array.isArray(json) ? json : (json.data || json);
        demplotData = arr.map(d => ({
          ...d,
          latitude: Number(d.latitude),
          longitude: Number(d.longitude),
          provinsi: d.provinsi ?? '',
          kabupaten: d.kabupaten ?? '',
          provinsi_kode: d.provinsi_kode ?? null,
          kabupaten_kode: d.kabupaten_kode ?? null,
          sektor_id: d.sektor_id ?? null
        }));

        restoreDefaultView();
        updateMapInfo(demplotData.length);
      } catch(err) {
        console.error('Failed fetch demplot', err);
        document.getElementById('mapInfo').innerHTML = '<div class="text-red-600">Error memuat data demplot</div>';
      }
    }

    // --- init map ---
    function initMap() {
      map = L.map('map').setView([-2.5489, 118.0149], 5);
      
      // Gunakan tile layer yang lebih bersih
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
      }).addTo(map);

      createMapControlUI();
      initGeoLayers();
      loadDemplotData();
      updateLegend();
    }

    document.addEventListener('DOMContentLoaded', initMap);
  })();
}
</script>

<style>
.custom-tooltip {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    font-weight: 500;
}

.custom-layer-control {
    background: rgba(255,255,255,0.95) !important;
    backdrop-filter: blur(10px);
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
}

.custom-layer-control .leaflet-control-layers-toggle {
    background-color: #2A7BE4 !important;
}

.leaflet-control-layers-expanded {
    background: rgba(255,255,255,0.98) !important;
    backdrop-filter: blur(10px);
}
</style>

@endpush

</x-app-layout>