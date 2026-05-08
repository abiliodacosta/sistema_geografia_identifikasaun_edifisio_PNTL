<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_suco = intval($_POST['id_suco']);
    $id_tipo = intval($_POST['id_tipo']);
    $naran_edifisio = htmlspecialchars(trim($_POST['naran_edifisio']));
    $naran_edifisio_pt = htmlspecialchars(trim($_POST['naran_edifisio_pt']));
    $naran_edifisio_en = htmlspecialchars(trim($_POST['naran_edifisio_en']));
    $diskrisaun = htmlspecialchars(trim($_POST['diskrisaun']));
    $diskrisaun_pt = htmlspecialchars(trim($_POST['diskrisaun_pt']));
    $diskrisaun_en = htmlspecialchars(trim($_POST['diskrisaun_en']));
    $enderesu = htmlspecialchars(trim($_POST['enderesu']));
    $latitude = floatval($_POST['latitude']);
    $longtitude = floatval($_POST['longtitude']);
    $tinan_konstrusaun = intval($_POST['tinan_konstrusaun']);
    $status = htmlspecialchars($_POST['status']);

    $foto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = time() . '_' . uniqid() . '.' . $ext;
        $target_dir = "uploads/edifisio/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto_name)) {
            $foto = $foto_name;
        }
    }

    if (!empty($naran_edifisio) && $id_suco > 0 && $id_tipo > 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tb_edifisio_pntl (id_suco, id_tipo, naran_edifisio, naran_edifisio_pt, naran_edifisio_en, diskrisaun, diskrisaun_pt, diskrisaun_en, enderesu, latitude, longtitude, tinan_konstrusaun, status, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisssssssddiss", $id_suco, $id_tipo, $naran_edifisio, $naran_edifisio_pt, $naran_edifisio_en, $diskrisaun, $diskrisaun_pt, $diskrisaun_en, $enderesu, $latitude, $longtitude, $tinan_konstrusaun, $status, $foto);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Hatama_Edifisio');
            header("Location: ?pntl=edificio/view");
            exit();
        } else {
            $error_msg = mysqli_stmt_error($stmt);
            $_SESSION['error'] = __('Faila_Hatama') . " Erru: " . $error_msg;
            header("Location: ?pntl=edificio/add");
            exit();
        }
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Importante');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-11 mx-auto">
            <div class="widget-card">
                <div class="d-flex align-items-center mb-4 gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:46px;height:46px;background:linear-gradient(135deg,#FFCC00,#ff9800);">
                        <i class="fa fa-plus" style="color:#002366;font-size:1.1rem;"></i>
                    </div>
                    <h4 class="mb-0 text-white fw-bold"><?= __('Form_Aumenta_Edifisio') ?></h4>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Left Side: Basic Info -->
                        <div class="col-lg-7">
                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary mb-4">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Informasaun_Jerál') ?></h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-white-50 small"><?= __('Naran_Edifisio') ?> (TET)</label>
                                        <input type="text" class="form-control premium-input" name="naran_edifisio" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Naran_Edifisio') ?> (PT)</label>
                                        <input type="text" class="form-control premium-input" name="naran_edifisio_pt">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Naran_Edifisio') ?> (EN)</label>
                                        <input type="text" class="form-control premium-input" name="naran_edifisio_en">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Tipu_Edifisio') ?></label>
                                        <select class="form-select premium-input" name="id_tipo" required>
                                            <option value=""><?= __('Hili_Tipu') ?></option>
                                            <?php
                                            $q_tipo = mysqli_query($conn, "SELECT * FROM tb_tipo_edifisio ORDER BY naran_tipu ASC");
                                            while ($row = mysqli_fetch_array($q_tipo)) {
                                                echo "<option value='".$row['id_tipo']."'>".htmlspecialchars($row['naran_tipu'])."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Suku_Postu') ?></label>
                                        <select class="form-select premium-input" name="id_suco" required>
                                            <option value=""><?= __('Hili_Suku') ?></option>
                                            <?php
                                            $q_suco = mysqli_query($conn, "SELECT s.*, p.naran_posto FROM tb_suco s JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto ORDER BY p.naran_posto ASC, s.naran_suco ASC");
                                            while ($row = mysqli_fetch_array($q_suco)) {
                                                echo "<option value='".$row['id_suco']."'>".htmlspecialchars($row['naran_suco'])." (".htmlspecialchars($row['naran_posto']).")</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-white-50 small"><?= __('Enderesu_Kompletu') ?></label>
                                        <input type="text" class="form-control premium-input" name="enderesu" required>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Diskrisaun') ?></h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <textarea class="form-control premium-input mb-2" name="diskrisaun" rows="2" placeholder="Tetun"></textarea>
                                        <textarea class="form-control premium-input mb-2" name="diskrisaun_pt" rows="2" placeholder="Português"></textarea>
                                        <textarea class="form-control premium-input" name="diskrisaun_en" rows="2" placeholder="English"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Map & Extra -->
                        <div class="col-lg-5">
                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary mb-4">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Localizasaun_Mapa') ?></h6>
                                <div id="mapPicker" style="height: 250px; border-radius: 12px;" class="mb-3 border border-secondary"></div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1">Lat</label>
                                        <input type="text" class="form-control premium-input text-center" id="latitude" name="latitude" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1">Lng</label>
                                        <input type="text" class="form-control premium-input text-center" id="longtitude" name="longtitude" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary mb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1"><?= __('Tinan_Konstrusaun') ?></label>
                                        <input type="number" class="form-control premium-input" name="tinan_konstrusaun" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1"><?= __('Status') ?></label>
                                        <select class="form-select premium-input" name="status" required>
                                            <option value="Uza hela"><?= __('Uza_Hela') ?></option>
                                            <option value="Labele uza"><?= __('Labele_Uza') ?></option>
                                            <option value="Konstrusaun hela"><?= __('Konstrusaun_Hela') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Foto_Edifisio') ?></h6>
                                <input type="file" class="form-control premium-input" name="foto" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top border-secondary d-flex gap-3">
                        <button type="submit" class="btn fw-bold px-5" 
                                style="background:linear-gradient(135deg,#FFCC00,#ff9800);color:#002366;border:none;border-radius:12px;box-shadow:0 4px 15px rgba(255,204,0,0.3);">
                            <i class="fa fa-save me-2"></i> <?= __('Rai_Dadus') ?>
                        </button>
                        <a href="?pntl=edificio/view" class="btn btn-outline-light rounded-pill px-4">
                            <i class="fa fa-times me-2"></i> <?= __('Kansela') ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-dark-50 { background: rgba(0, 0, 0, 0.2); }
.premium-input, .premium-input:focus {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: #fff !important;
    border-radius: 10px !important;
}
.premium-input:focus { border-color: #FFCC00 !important; box-shadow: 0 0 0 3px rgba(255,204,0,0.1) !important; }
</style>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longtitude');
    var defaultLat = -8.5569;
    var defaultLng = 125.5603;

    var map = L.map('mapPicker').setView([defaultLat, defaultLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker = null;

    map.on('click', function(e) {
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, {draggable: true}).addTo(map);
            marker.on('dragend', function(ev) {
                var pos = ev.target.getLatLng();
                latInput.value = pos.lat.toFixed(6);
                lngInput.value = pos.lng.toFixed(6);
            });
        }
    });
});
</script>