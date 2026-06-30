<?php
// $listings_json, $projects_json, $total controller'dan gelir
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Portföy Haritası</span>
    <h1>İlanlar & Projeler Haritada</h1>
    <p><?= (int)$total ?> konum listeleniyor</p>
  </div>
</section>

<!-- FİLTRE BAR -->
<div style="background:#fff;border-bottom:1px solid var(--line);padding:14px 0;position:sticky;top:0;z-index:10;">
  <div class="container" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <button class="map-filter-btn active" data-filter="all">Tümü</button>
    <button class="map-filter-btn" data-filter="satilik">Satılık</button>
    <button class="map-filter-btn" data-filter="kiralik">Kiralık</button>
    <button class="map-filter-btn" data-filter="project">Projeler</button>
    <span style="margin-left:auto;font-size:13px;color:var(--muted);font-weight:600;" id="mapCount"><?= (int)$total ?> sonuç</span>
  </div>
</div>

<!-- MAP + LIST LAYOUT -->
<div class="map-page-layout">

  <!-- LEAFLET HARİTA -->
  <div id="leafletMap" style="height:100%;min-height:600px;"></div>

  <!-- POPUP LİSTE PANELİ -->
  <aside class="map-list-panel" id="mapListPanel">
    <div id="mapListItems"></div>
  </aside>

</div>

<style>
.map-page-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  height: calc(100vh - 220px);
  min-height: 560px;
}
.map-list-panel {
  overflow-y: auto;
  border-left: 1px solid var(--line);
  background: var(--soft);
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.map-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--radius);
  overflow: hidden;
  cursor: pointer;
  transition: box-shadow .2s, transform .15s;
  text-decoration: none;
  color: inherit;
  display: block;
}
.map-card:hover { box-shadow: 0 8px 24px rgba(10,31,68,.1); transform: translateY(-2px); }
.map-card.highlighted { border-color: var(--turquoise); box-shadow: 0 0 0 2px rgba(24,198,195,.3); }
.map-card img { width: 100%; height: 110px; object-fit: cover; display: block; }
.map-card-body { padding: 12px 14px; }
.map-card-body h4 { font-family: Syne, sans-serif; font-size: 14px; color: var(--navy); margin: 0 0 4px; line-height: 1.3; }
.map-card-body .map-price { font-weight: 700; color: var(--navy); font-size: 15px; margin: 4px 0 0; }
.map-card-body .map-meta { font-size: 12px; color: var(--muted); }
.map-badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; margin-bottom: 4px; }
.map-badge.satilik { background: #EAF3FF; color: #18589C; }
.map-badge.kiralik { background: #E8F9F2; color: #138C65; }
.map-badge.project { background: rgba(24,198,195,.12); color: var(--turquoise); }
.map-filter-btn {
  padding: 7px 16px; border-radius: 999px; border: 1.5px solid var(--line);
  font-size: 13px; font-weight: 600; color: var(--muted); background: #fff;
  cursor: pointer; transition: all .18s;
}
.map-filter-btn:hover { border-color: var(--navy); color: var(--navy); }
.map-filter-btn.active { background: var(--navy); color: #fff; border-color: var(--navy); }
/* Leaflet popup */
.leaflet-popup-content-wrapper { border-radius: 10px !important; box-shadow: 0 8px 30px rgba(10,31,68,.15) !important; }
.leaflet-popup-content { margin: 0 !important; width: 220px !important; }
.lp-img { width: 100%; height: 110px; object-fit: cover; border-radius: 10px 10px 0 0; display: block; }
.lp-body { padding: 12px 14px 10px; }
.lp-body h4 { font-size: 13px; font-weight: 700; color: #0A1F44; margin: 0 0 4px; }
.lp-body .lp-price { font-weight: 700; color: #0A1F44; font-size: 14px; }
.lp-body a.lp-btn { display: inline-block; margin-top: 8px; background: #0A1F44; color: #fff; border-radius: 999px; padding: 6px 14px; font-size: 12px; font-weight: 700; text-decoration: none; }
@media (max-width: 768px) {
  .map-page-layout { grid-template-columns: 1fr; height: auto; }
  .map-list-panel { border-left: none; border-top: 1px solid var(--line); max-height: 400px; }
  #leafletMap { min-height: 400px; }
}
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
  const LISTINGS = <?= $listings_json ?>;
  const PROJECTS = <?= $projects_json ?>;
  const ALL = [...LISTINGS, ...PROJECTS];

  // Icons
  function makeIcon(color) {
    return L.divIcon({
      className: '',
      html: `<div style="width:28px;height:28px;background:${color};border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.25);"></div>`,
      iconSize: [28, 28],
      iconAnchor: [14, 14],
      popupAnchor: [0, -16],
    });
  }
  const icons = {
    satilik : makeIcon('#174A83'),
    kiralik : makeIcon('#138C65'),
    project : makeIcon('#18C6C3'),
  };

  // Map init — Bolu center
  const map = L.map('leafletMap').setView([40.7370, 31.6070], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
    maxZoom: 18,
  }).addTo(map);

  // Markers
  let markers = [];
  let activeFilter = 'all';

  function popupHtml(item) {
    const img = item.img ? `<img class="lp-img" src="${item.img}" loading="lazy">` : '';
    const badge = item.kind === 'project'
      ? `<span class="map-badge project">Proje</span>`
      : `<span class="map-badge ${item.type}">${item.type === 'satilik' ? 'Satılık' : 'Kiralık'}</span>`;
    const price = item.price || '';
    const meta = [item.room, item.m2 ? item.m2 + ' m²' : ''].filter(Boolean).join(' · ');
    return `<div>${img}<div class="lp-body">${badge}<h4>${item.title}</h4>${price ? `<div class="lp-price">${price}</div>` : ''}${meta ? `<div style="font-size:12px;color:#65758A;">${meta}</div>` : ''}<a class="lp-btn" href="${item.url}" target="_blank">İncele →</a></div></div>`;
  }

  function cardHtml(item, idx) {
    const img = item.img ? `<img loading="lazy" src="${item.img}" alt="${item.title}">` : `<div style="height:110px;background:linear-gradient(135deg,#0A1F44,#18C6C3);"></div>`;
    const badge = item.kind === 'project'
      ? `<span class="map-badge project">Proje</span>`
      : `<span class="map-badge ${item.type}">${item.type === 'satilik' ? 'Satılık' : 'Kiralık'}</span>`;
    const meta = [item.location, item.room, item.m2 ? item.m2 + ' m²' : ''].filter(Boolean).join(' · ');
    return `<a class="map-card" href="${item.url}" target="_blank" data-idx="${idx}">${img}<div class="map-card-body">${badge}<h4>${item.title}</h4>${item.price ? `<div class="map-price">${item.price}</div>` : ''}<div class="map-meta">${meta}</div></div></a>`;
  }

  function buildMarkers(filter) {
    markers.forEach(m => map.removeLayer(m.marker));
    markers = [];
    document.getElementById('mapListItems').innerHTML = '';

    const filtered = ALL.filter(item => {
      if (filter === 'all') return true;
      if (filter === 'project') return item.kind === 'project';
      return item.type === filter;
    });

    document.getElementById('mapCount').textContent = filtered.length + ' sonuç';

    filtered.forEach((item, idx) => {
      const icon = icons[item.kind === 'project' ? 'project' : item.type] || icons.satilik;
      const m = L.marker([item.lat, item.lng], { icon }).addTo(map);
      m.bindPopup(popupHtml(item), { maxWidth: 240 });
      m.on('click', function() {
        // Highlight card
        document.querySelectorAll('.map-card').forEach(c => c.classList.remove('highlighted'));
        const card = document.querySelector(`.map-card[data-idx="${idx}"]`);
        if (card) { card.classList.add('highlighted'); card.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
      });
      markers.push({ marker: m, item, idx });

      // List card
      const div = document.createElement('div');
      div.innerHTML = cardHtml(item, idx);
      const card = div.firstChild;
      card.addEventListener('click', function(e) {
        e.preventDefault();
        map.setView([item.lat, item.lng], 15);
        m.openPopup();
        document.querySelectorAll('.map-card').forEach(c => c.classList.remove('highlighted'));
        card.classList.add('highlighted');
      });
      document.getElementById('mapListItems').appendChild(card);
    });

    // Fit bounds if any markers
    if (markers.length) {
      const group = L.featureGroup(markers.map(m => m.marker));
      map.fitBounds(group.getBounds().pad(0.15));
    }
  }

  buildMarkers('all');

  // Filter buttons
  document.querySelectorAll('.map-filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.map-filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      buildMarkers(this.dataset.filter);
    });
  });
})();
</script>
