<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    echo "<script>window.location='?pntl=edificio/view';</script>";
    exit;
}

$query = "SELECT e.*, s.naran_suco, p.naran_posto, m.naran_munisipio, t.naran_tipu 
          FROM tb_edifisio_pntl e 
          JOIN tb_suco s ON e.id_suco = s.id_suco 
          JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
          JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio
          JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo 
          WHERE e.id_edifisio = $id";
$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    echo "<script>window.location='?pntl=edificio/view';</script>";
    exit;
}
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="widget-card">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-hotel text-white fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white"><?= htmlspecialchars($data['naran_edifisio']) ?></h3>
                            <span class="text-white-50"><?= htmlspecialchars($data['naran_tipu']) ?></span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="?pntl=edificio/edit&id=<?= $id ?>" class="btn btn-warning rounded-pill px-4">
                            <i class="fa fa-pen me-2"></i> <?= __('Edit') ?>
                        </a>
                        <a href="?pntl=edificio/view" class="btn btn-outline-light rounded-pill px-4">
                            <i class="fa fa-arrow-left me-2"></i> <?= __('Fila') ?>
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left: Info & Photo -->
                    <div class="col-lg-6">
                        <!-- Main Image -->
                        <div class="mb-4 rounded-4 overflow-hidden shadow-lg border border-secondary">
                            <?php if (!empty($data['foto'])): ?>
                                <img src="uploads/edifisio/<?= htmlspecialchars($data['foto']) ?>" class="w-100" style="max-height: 400px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-dark d-flex flex-column align-items-center justify-content-center py-5">
                                    <i class="fa fa-image fa-4x text-white-50 mb-3"></i>
                                    <span class="text-white-50"><?= __('Laiha_Foto') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Data Grid -->
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-dark-50 border border-secondary h-100">
                                    <small class="text-white-50 d-block mb-1"><?= __('Munisipio') ?></small>
                                    <span class="text-white fw-bold"><?= htmlspecialchars($data['naran_munisipio']) ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-dark-50 border border-secondary h-100">
                                    <small class="text-white-50 d-block mb-1"><?= __('Postu_Admin') ?></small>
                                    <span class="text-white fw-bold"><?= htmlspecialchars($data['naran_posto']) ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-dark-50 border border-secondary h-100">
                                    <small class="text-white-50 d-block mb-1"><?= __('Suku') ?></small>
                                    <span class="text-white fw-bold"><?= htmlspecialchars($data['naran_suco']) ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-dark-50 border border-secondary h-100">
                                    <small class="text-white-50 d-block mb-1"><?= __('Status') ?></small>
                                    <div>
                                        <?php 
                                        if ($data['status'] == 'Uza hela') echo '<span class="badge bg-success">'.__('Uza_Hela').'</span>';
                                        elseif ($data['status'] == 'Labele uza') echo '<span class="badge bg-danger">'.__('Labele_Uza').'</span>';
                                        else echo '<span class="badge bg-warning text-dark">'.__('Konstrusaun_Hela').'</span>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="p-3 rounded-3 bg-dark-50 border border-secondary">
                                    <small class="text-white-50 d-block mb-1"><?= __('Enderesu') ?></small>
                                    <span class="text-white"><?= htmlspecialchars($data['enderesu']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Map & Description -->
                    <div class="col-lg-6">
                        <!-- Map Container -->
                        <div class="mb-4 rounded-4 overflow-hidden border border-secondary shadow-lg" style="height: 300px; z-index: 1;">
                            <div id="detailMap" style="height: 100%; width: 100%;"></div>
                        </div>

                        <!-- Description Tabs -->
                        <div class="bg-dark-50 rounded-4 p-4 border border-secondary">
                            <ul class="nav nav-tabs border-secondary mb-3" id="descTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active text-white" data-bs-toggle="tab" data-bs-target="#tet-desc">TET</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link text-white" data-bs-toggle="tab" data-bs-target="#pt-desc">PT</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link text-white" data-bs-toggle="tab" data-bs-target="#en-desc">EN</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="descContent">
                                <div class="tab-pane fade show active" id="tet-desc">
                                    <p class="text-white opacity-75 mb-0" style="white-space: pre-wrap;"><?= !empty($data['diskrisaun']) ? htmlspecialchars($data['diskrisaun']) : '<i>Laiha diskrisaun.</i>' ?></p>
                                </div>
                                <div class="tab-pane fade" id="pt-desc">
                                    <p class="text-white opacity-75 mb-0" style="white-space: pre-wrap;"><?= !empty($data['diskrisaun_pt']) ? htmlspecialchars($data['diskrisaun_pt']) : '<i>Sem descrição.</i>' ?></p>
                                </div>
                                <div class="tab-pane fade" id="en-desc">
                                    <p class="text-white opacity-75 mb-0" style="white-space: pre-wrap;"><?= !empty($data['diskrisaun_en']) ? htmlspecialchars($data['diskrisaun_en']) : '<i>No description available.</i>' ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Construction Year -->
                        <div class="mt-4 d-flex align-items-center gap-2">
                            <i class="fa fa-calendar-alt text-primary"></i>
                            <span class="text-white-50 small"><?= __('Tinan_Konstrusaun') ?>:</span>
                            <span class="text-white fw-bold"><?= $data['tinan_konstrusaun'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-dark-50 { background: rgba(0, 0, 0, 0.2); }
.nav-tabs .nav-link { border: none !important; opacity: 0.5; }
.nav-tabs .nav-link.active { background: rgba(255, 204, 0, 0.1) !important; color: #FFCC00 !important; opacity: 1; border-bottom: 2px solid #FFCC00 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = <?= $data['latitude'] ?>;
    var lng = <?= $data['longtitude'] ?>;
    var name = "<?= addslashes($data['naran_edifisio']) ?>";

    var map = L.map('detailMap').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Satelite toggle if needed
    var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Esri'
    });
    
    L.control.layers({ "Mapa": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png') }, { "Satelite": satellite }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(`<b>${name}</b>`)
        .openPopup();
});
</script>
