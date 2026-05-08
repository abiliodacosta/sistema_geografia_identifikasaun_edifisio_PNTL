<?php

// Get Edifisio
$get1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_edifisio_pntl");
$row1 = mysqli_fetch_assoc($get1);
$count1 = $row1['total'];

// Get Munisipio
$get2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_munisipio");
$row2 = mysqli_fetch_assoc($get2);
$count2 = $row2['total'];

// Postu Administrativu
$get3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_postu_administrativu");
$row3 = mysqli_fetch_assoc($get3);
$count3 = $row3['total'];

// Suku
$get4 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_suco");
$row4 = mysqli_fetch_assoc($get4);
$count4 = $row4['total'];

// Tipu edifisio
$get5 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_tipo_edifisio");
$row5 = mysqli_fetch_assoc($get5);
$count5 = $row5['total'];

// Get Markers ba Mapa
$markers_data = [];
$q_map = mysqli_query($conn, "SELECT e.*, s.naran_suco, p.naran_posto, m.naran_munisipio, t.naran_tipu 
                              FROM tb_edifisio_pntl e 
                              JOIN tb_suco s ON e.id_suco = s.id_suco
                              JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
                              JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio
                              JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo");
while ($row = mysqli_fetch_assoc($q_map)) {
    $markers_data[] = [
        'lat' => floatval($row['latitude']),
        'lng' => floatval($row['longtitude']),
        'title' => $row['naran_edifisio'],
        'tipo' => $row['naran_tipu'],
        'address' => $row['enderesu'],
        'munisipio' => $row['naran_munisipio'],
        'postu' => $row['naran_posto'],
        'suco' => $row['naran_suco']
    ];
}
$markers_json = json_encode($markers_data);

?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>


<style>
    /* ===== DASHBOARD COLOR SCHEME (PNTL) ===== */
    :root {
        --pntl-blue: #002366;
        --pntl-red: #CE1126;
        --pntl-gold: #FFCC00;
        --pntl-dark-blue: #051124;
        --card-bg: rgba(13, 33, 55, 0.85);
    }

    /* ===== CARD DASHBOARD STYLES ===== */
    .card-dashboard, .widget-card {
        background: var(--card-bg);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        overflow: hidden;
        position: relative;
    }

    .card-dashboard {
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 1rem;
        cursor: pointer;
        height: 100%;
    }

    .widget-card {
        padding: 1.5rem;
        height: 100%;
    }

    .card-dashboard:hover, .widget-card:hover {
        transform: translateY(-8px);
        background: rgba(13, 33, 55, 1);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        border-color: var(--pntl-gold);
    }

    /* Icon Container */
    .card-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
        transition: all 0.4s ease;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        margin-bottom: 0.5rem;
    }

    .card-dashboard:hover .card-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 15px 25px rgba(0,0,0,0.4);
        animation: pulse-gold 2s infinite;
    }

    @keyframes pulse-gold {
        0% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(255, 204, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0); }
    }

    /* Content */
    .card-content {
        width: 100%;
        color: #fff;
    }

    .card-label {
        font-size: 0.85rem;
        font-weight: 600;
        margin: 0 0 8px 0;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .card-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
        line-height: 1;
        color: #fff;
        display: block;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .card-desc {
        font-size: 0.75rem;
        opacity: 0.6;
        margin-top: 8px;
        font-weight: 400;
        display: block;
    }

    /* Hover Decoration */
    .card-dashboard::after, .widget-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--pntl-gold);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .card-dashboard:hover::after, .widget-card:hover::after {
        transform: scaleX(1);
    }

    /* Hover Arrow */
    .card-hover {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        color: var(--pntl-gold);
        font-size: 1rem;
        transition: all 0.4s ease;
        opacity: 0;
    }

    .card-dashboard:hover .card-hover {
        bottom: 10px;
        opacity: 1;
    }

    /* ===== ICON COLORS ===== */
    .card-edifisio .card-icon { background: linear-gradient(135deg, var(--pntl-blue) 0%, #0044cc 100%); }
    .card-munisipio .card-icon { background: linear-gradient(135deg, var(--pntl-red) 0%, #ff4d4d 100%); }
    .card-postu .card-icon { background: linear-gradient(135deg, var(--pntl-gold) 0%, #ffdb4d 100%); color: #000; }
    .card-suku .card-icon { background: linear-gradient(135deg, #00a859 0%, #00e67a 100%); }
    .card-tipo .card-icon { background: linear-gradient(135deg, #6f42c1 0%, #a06ee1 100%); }

    /* Custom Scrollbar for Calendar */
    #ajenda-calendar .fc-scroller::-webkit-scrollbar {
        width: 8px;
    }
    #ajenda-calendar .fc-scroller::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }
    #ajenda-calendar .fc-scroller::-webkit-scrollbar-thumb {
        background: var(--pntl-gold);
        border-radius: 4px;
    }

    /* ===== CALENDAR TITLE STYLING ===== */
    #ajenda-calendar .fc-toolbar-title {
        font-size: 1.4rem !important;
        font-weight: 800 !important;
        color: #1a2a4a !important;
        letter-spacing: 0.5px;
    }

    #ajenda-calendar .fc-button {
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        padding: 6px 12px !important;
        border-radius: 10px !important;
        background: #1a2a4a !important;
        border-color: #1a2a4a !important;
        color: #fff !important;
        margin: 0 3px !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
    }

    #ajenda-calendar .fc-button:hover {
        background: #002366 !important;
        border-color: #002366 !important;
        transform: translateY(-2px) scale(1.05) !important;
        box-shadow: 0 6px 16px rgba(0, 35, 102, 0.35) !important;
    }

    #ajenda-calendar .fc-button:active {
        transform: translateY(0) scale(0.97) !important;
    }

    #ajenda-calendar .fc-button-active,
    #ajenda-calendar .fc-button-primary:not(:disabled).fc-button-active {
        background: #002366 !important;
        border-color: #FFCC00 !important;
        box-shadow: 0 0 0 2px rgba(255,204,0,0.4), 0 4px 12px rgba(0,35,102,0.3) !important;
        transform: translateY(-1px) !important;
    }

    #ajenda-calendar .fc-toolbar.fc-header-toolbar {
        gap: 8px !important;
        flex-wrap: wrap !important;
    }

    #ajenda-calendar .fc-toolbar-chunk {
        display: flex !important;
        gap: 4px !important;
        align-items: center !important;
    }

    #ajenda-calendar .fc-col-header-cell-cushion,
    #ajenda-calendar .fc-daygrid-day-number {
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        color: #0b2545 !important;
        text-decoration: none !important;
    }

    #ajenda-calendar .fc-day-today {
        background: rgba(0, 35, 102, 0.08) !important;
    }

    #ajenda-calendar .fc-day-today .fc-daygrid-day-number {
        color: #002366 !important;
        font-size: 1rem !important;
    }

    /* Animation Entrance */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card-dashboard-col {
        opacity: 0;
        animation: fadeInUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    }

    .card-dashboard-col:nth-child(1) { animation-delay: 0.1s; }
    .card-dashboard-col:nth-child(2) { animation-delay: 0.2s; }
    .card-dashboard-col:nth-child(3) { animation-delay: 0.3s; }
    .card-dashboard-col:nth-child(4) { animation-delay: 0.4s; }
    .card-dashboard-col:nth-child(5) { animation-delay: 0.5s; }
</style>

<!-- ================== -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 justify-content-center">

        <!-- 🏢 Edifisio -->
        <div class="col card-dashboard-col">
            <div class="card-dashboard card-edifisio" onclick="window.location='?pntl=edificio/view'">
                <div class="card-icon">
                    <i class="fa fa-building fa-2x"></i>
                </div>
                <div class="card-content">
                    <p class="card-label"><?= __('Edifisio') ?></p>
                    <h2 class="card-value counter" data-target="<?= $count1; ?>">0</h2>
                    <small class="card-desc"><?= __('Total_Rejistadu') ?></small>
                </div>
                <div class="card-hover">
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- 🗺️ Munisipio -->
        <div class="col card-dashboard-col">
            <div class="card-dashboard card-munisipio" onclick="window.location='?pntl=munisipio/view'">
                <div class="card-icon">
                    <i class="fa fa-map-marked-alt fa-2x"></i>
                </div>
                <div class="card-content">
                    <p class="card-label"><?= __('Munisipio') ?></p>
                    <h2 class="card-value counter" data-target="<?= $count2; ?>">0</h2>
                    <small class="card-desc"><?= __('Area_Administrativu') ?></small>
                </div>
                <div class="card-hover">
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- 🏛️ Postu Administrativu -->
        <div class="col card-dashboard-col">
            <div class="card-dashboard card-postu" onclick="window.location='?pntl=postu_administrativu/view'">
                <div class="card-icon">
                    <i class="fa fa-landmark fa-2x"></i>
                </div>
                <div class="card-content">
                    <p class="card-label"><?= __('Postu_Admin') ?></p>
                    <h2 class="card-value counter" data-target="<?= $count3; ?>">0</h2>
                    <small class="card-desc"><?= __('Unidade_Lokal') ?></small>
                </div>
                <div class="card-hover">
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- 🏘️ Suku -->
        <div class="col card-dashboard-col">
            <div class="card-dashboard card-suku" onclick="window.location='?pntl=suku/view'">
                <div class="card-icon">
                    <i class="fa fa-map-pin fa-2x"></i>
                </div>
                <div class="card-content">
                    <p class="card-label"><?= __('Suku') ?></p>
                    <h2 class="card-value counter" data-target="<?= $count4; ?>">0</h2>
                    <small class="card-desc"><?= __('Komunidade_Baze') ?></small>
                </div>
                <div class="card-hover">
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- 🏗️ Tipo Edifisio -->
        <div class="col card-dashboard-col">
            <div class="card-dashboard card-tipo" onclick="window.location='?pntl=tipo_edifisio/view'">
                <div class="card-icon">
                    <i class="fa fa-layer-group fa-2x"></i>
                </div>
                <div class="card-content">
                    <p class="card-label"><?= __('Tipu_Edifisio') ?></p>
                    <h2 class="card-value counter" data-target="<?= $count5; ?>">0</h2>
                    <small class="card-desc"><?= __('Kategoria_Diversu') ?></small>
                </div>
                <div class="card-hover">
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- ================= -->

<!-- =============================== -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <!-- Grafiku Barra: Distribuisaun Dadus -->
        <div class="col-sm-12 col-lg-6">
            <div class="widget-card text-center d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0 text-white">📊 <?= __('Estatistika_Administrativa') ?></h6>
                </div>
                <div class="flex-grow-1" style="height: 350px; position: relative; width: 100%;">
                    <canvas id="chart-estatistika"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafiku Doughnut: Proporsaun -->
        <div class="col-sm-12 col-lg-6">
            <div class="widget-card text-center d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0 text-white">🥧 <?= __('Proporsaun_Dadus') ?></h6>
                    <a href="" class="text-primary small"><?= __('Detallu') ?></a>
                </div>
                <div class="flex-grow-1" style="height: 350px; position: relative; width: 100%;">
                    <canvas id="chart-proporsaun"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Dadus husi PHP
    const dadosEstatistika = {
        edifisio: <?php echo $count1; ?>,
        munisipio: <?php echo $count2; ?>,
        postuAdmin: <?php echo $count3; ?>,
        suco: <?php echo $count4; ?>,
        tipoEdifisio: <?php echo $count5; ?>
    };

    // 📊 Chart 1: Barra
    const ctx1 = document.getElementById('chart-estatistika').getContext('2d');
    if (window.chartBarra) window.chartBarra.destroy();
    window.chartBarra = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ["<?= __('Edifisio') ?>", "<?= __('Munisipio') ?>", "<?= __('Postu_Admin') ?>", "<?= __('Suku') ?>", "<?= __('Tipu_Edifisio') ?>"],
            datasets: [{
                label: "<?= __('Total_Rejistadu') ?>",
                data: [
                    dadosEstatistika.edifisio,
                    dadosEstatistika.munisipio,
                    dadosEstatistika.postuAdmin,
                    dadosEstatistika.suco,
                    dadosEstatistika.tipoEdifisio
                ],
                backgroundColor: [
                    '#36B9CC', '#6610f2', '#20c997', '#fd7e14', '#e83e8c'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#fff'
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#fff'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#fff'
                    }
                }
            }
        }
    });

    // 🥧 Chart 2: Doughnut
    const ctx2 = document.getElementById('chart-proporsaun').getContext('2d');
    if (window.chartDoughnut) window.chartDoughnut.destroy();
    window.chartDoughnut = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ["<?= __('Edifisio') ?>", "<?= __('Munisipio') ?>", "<?= __('Suku') ?>"],
            datasets: [{
                data: [
                    dadosEstatistika.edifisio,
                    dadosEstatistika.munisipio,
                    dadosEstatistika.suco
                ],
                backgroundColor: ['#36B9CC', '#6610f2', '#20c997'],
                borderWidth: 2,
                borderColor: '#2b3947'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#fff',
                        padding: 15
                    }
                }
            },
            cutout: '60%'
        }
    });
</script>
<!-- ============================== -->

<!-- ============================== -->


<!-- Leaflet Map Setup -->
<script>
    // Dadus husi PHP
    const markersData = <?php echo $markers_json; ?>;

    let map;

    // Inicializa Mapa
    function initMap() {
        // Lokalizasaun default: Timor-Leste
        // Base Maps
        var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EBP, and the GIS User Community'
        });

        // Lokalizasaun default: Timor-Leste, uza OSM nudar default
        map = L.map('map', {
            center: [-8.5569, 125.5603],
            zoom: 8,
            layers: [osmLayer]
        });

        // Layer Control hodi troka mapa
        var baseMaps = {
            "OpenStreetMap": osmLayer,
            "Satelite (Esri)": satelliteLayer
        };
        L.control.layers(baseMaps).addTo(map);

        // Kria bounds hodi halo zoom auto tuir marker
        var bounds = [];

        markersData.forEach(item => {
            if (!item.lat || !item.lng) return;

            var content = `
            <div style="min-width:200px; font-family:Arial; color:#333;">
                <strong style="font-size:14px;">${item.title}</strong><br>
                <small style="color:#666;">${item.tipo || "<?= __('Edifisio') ?>"}</small><br>
                <hr style="margin:5px 0;">
                <strong>📍 <?= __('Enderesu') ?>:</strong> ${item.address || '-'}<br>
                <strong>🏛️ <?= __('Munisipio') ?>:</strong> ${item.munisipio || '-'}<br>
                <strong>📮 <?= __('Postu_Suku') ?>:</strong> ${item.postu || ''} / ${item.suco || ''}<br>
                <a href="https://www.google.com/maps/dir/?api=1&destination=${item.lat},${item.lng}" target="_blank" style="display:inline-block; margin-top:8px; padding:4px 12px; background:#0d6efd; color:white; text-decoration:none; border-radius:4px;">
                    🧭 <?= __('Direisaun') ?>
                </a>
            </div>
        `;

            var marker = L.marker([item.lat, item.lng]).addTo(map)
                .bindPopup(content);

            bounds.push([item.lat, item.lng]);
        });

        // Se iha dadus, centra mapa ba marker hotu
        if (bounds.length > 0) {
            map.fitBounds(bounds);
        }
    }

    // Inicializa bainhira page load
    document.addEventListener('DOMContentLoaded', initMap);
</script>
<!-- ============================= -->
<!-- FullCalendar Setup -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<!-- Widgets Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-md-6 col-xl-5">
            <div class="widget-card d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0 text-white">📅 <?= __('Kalendariu_Ajenda') ?></h6>
                    <a href="?pntl=ajenda/view" class="text-primary small"><?= __('Haree_Hotu') ?></a>
                </div>
                <div id="ajenda-calendar" style="background: rgba(255,255,255,0.9); color: black; padding: 15px; border-radius: 12px; flex-grow: 1;"></div>
            </div>
        </div>
        <div class="col-sm-12 col-xl-7">
            <div class="widget-card d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="mb-0 text-white"><?= __('Mapa_Hare_Dadus') ?></h4>
                </div>
                <div id="map" class="position-relative rounded w-100 flex-grow-1" style="min-height: 400px; z-index: 1;"></div>
            </div>
        </div>
    </div>
</div>
<!-- Widgets End -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('ajenda-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: 'api_ajenda.php',
            eventColor: '#ffc107',
            eventTextColor: '#0b2545',
            height: 450,
            eventClick: function(info) {
                alert("<?= __('Titulu') ?>: " + info.event.title + "\n<?= __('Oras') ?>: " + info.event.start.toLocaleTimeString() + "\n<?= __('Diskrisaun') ?>: " + (info.event.extendedProps.description || "<?= __('Laiha_Diskrisaun') ?>"));
            }
        });
        calendar.render();
    });

    // 🔢 Counter-Up Animation
    const counters = document.querySelectorAll('.counter');
    const speed = 200;

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(updateCount, 1);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
</script>