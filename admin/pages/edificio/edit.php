<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    header("Location: ?pntl=edificio/view");
    exit;
}

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

    // Handling Foto
    $foto = $_POST['old_foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = time() . '_' . uniqid() . '.' . $ext;
        $target_dir = "uploads/edifisio/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto_name)) {
            $foto = $foto_name;
            if (!empty($_POST['old_foto']) && file_exists($target_dir . $_POST['old_foto'])) {
                unlink($target_dir . $_POST['old_foto']);
            }
        }
    }

    if (!empty($naran_edifisio) && $id_suco > 0 && $id_tipo > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE tb_edifisio_pntl SET id_suco=?, id_tipo=?, naran_edifisio=?, naran_edifisio_pt=?, naran_edifisio_en=?, diskrisaun=?, diskrisaun_pt=?, diskrisaun_en=?, enderesu=?, latitude=?, longtitude=?, tinan_konstrusaun=?, status=?, foto=? WHERE id_edifisio=?");
        mysqli_stmt_bind_param($stmt, "iisssssssddissi", $id_suco, $id_tipo, $naran_edifisio, $naran_edifisio_pt, $naran_edifisio_en, $diskrisaun, $diskrisaun_pt, $diskrisaun_en, $enderesu, $latitude, $longtitude, $tinan_konstrusaun, $status, $foto, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Altera_Edifisio');
            header("Location: ?pntl=edificio/view");
            exit();
        } else {
            $error_msg = mysqli_stmt_error($stmt);
            $_SESSION['error'] = __('Faila_Altera') . " Erru: " . $error_msg;
            header("Location: ?pntl=edificio/edit&id=".$id);
            exit();
        }
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Importante');
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_edifisio_pntl WHERE id_edifisio = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    header("Location: ?pntl=edificio/view");
    exit;
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-11 mx-auto">
            <div class="widget-card">
                <div class="d-flex align-items-center mb-4 gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:46px;height:46px;background:linear-gradient(135deg,#FFCC00,#ff9800);">
                        <i class="fa fa-edit" style="color:#002366;font-size:1.1rem;"></i>
                    </div>
                    <h4 class="mb-0 text-white fw-bold"><?= __('Form_Altera_Edifisio') ?></h4>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="old_foto" value="<?php echo htmlspecialchars($data['foto'] ?? ''); ?>">
                    
                    <div class="row g-4">
                        <!-- Left Side: Basic Info -->
                        <div class="col-lg-7">
                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary mb-4">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Informasaun_Jerál') ?></h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-white-50 small"><?= __('Naran_Edifisio') ?> (TET)</label>
                                        <input type="text" class="form-control premium-input" name="naran_edifisio" value="<?php echo htmlspecialchars($data['naran_edifisio']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Naran_Edifisio') ?> (PT)</label>
                                        <input type="text" class="form-control premium-input" name="naran_edifisio_pt" value="<?php echo htmlspecialchars($data['naran_edifisio_pt']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Naran_Edifisio') ?> (EN)</label>
                                        <input type="text" class="form-control premium-input" name="naran_edifisio_en" value="<?php echo htmlspecialchars($data['naran_edifisio_en']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white-50 small"><?= __('Tipu_Edifisio') ?></label>
                                        <select class="form-select premium-input" name="id_tipo" required>
                                            <option value=""><?= __('Hili_Tipu') ?></option>
                                            <?php
                                            $q_tipo = mysqli_query($conn, "SELECT * FROM tb_tipo_edifisio ORDER BY naran_tipu ASC");
                                            while ($row = mysqli_fetch_array($q_tipo)) {
                                                $selected = ($row['id_tipo'] == $data['id_tipo']) ? "selected" : "";
                                                echo "<option value='".$row['id_tipo']."' ".$selected.">".htmlspecialchars($row['naran_tipu'])."</option>";
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
                                                $selected = ($row['id_suco'] == $data['id_suco']) ? "selected" : "";
                                                echo "<option value='".$row['id_suco']."' ".$selected.">".htmlspecialchars($row['naran_suco'])." (".htmlspecialchars($row['naran_posto']).")</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-white-50 small"><?= __('Enderesu_Kompletu') ?></label>
                                        <input type="text" class="form-control premium-input" name="enderesu" value="<?php echo htmlspecialchars($data['enderesu']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Diskrisaun') ?></h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <textarea class="form-control premium-input mb-2" name="diskrisaun" rows="2" placeholder="Tetun"><?php echo htmlspecialchars($data['diskrisaun']); ?></textarea>
                                        <textarea class="form-control premium-input mb-2" name="diskrisaun_pt" rows="2" placeholder="Português"><?php echo htmlspecialchars($data['diskrisaun_pt']); ?></textarea>
                                        <textarea class="form-control premium-input" name="diskrisaun_en" rows="2" placeholder="English"><?php echo htmlspecialchars($data['diskrisaun_en']); ?></textarea>
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
                                        <input type="text" class="form-control premium-input text-center" id="latitude" name="latitude" value="<?php echo htmlspecialchars($data['latitude']); ?>" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1">Lng</label>
                                        <input type="text" class="form-control premium-input text-center" id="longtitude" name="longtitude" value="<?php echo htmlspecialchars($data['longtitude']); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary mb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1"><?= __('Tinan_Konstrusaun') ?></label>
                                        <input type="number" class="form-control premium-input" name="tinan_konstrusaun" value="<?php echo htmlspecialchars($data['tinan_konstrusaun']); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-white-50 small mb-1"><?= __('Status') ?></label>
                                        <select class="form-select premium-input" name="status" required>
                                            <option value="Uza hela" <?php if($data['status'] == 'Uza hela') echo 'selected'; ?>><?= __('Uza_Hela') ?></option>
                                            <option value="Labele uza" <?php if($data['status'] == 'Labele uza') echo 'selected'; ?>><?= __('Labele_Uza') ?></option>
                                            <option value="Konstrusaun hela" <?php if($data['status'] == 'Konstrusaun hela') echo 'selected'; ?>><?= __('Konstrusaun_Hela') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-dark-50 border border-secondary">
                                <h6 class="text-white-50 mb-3 small fw-bold text-uppercase"><?= __('Foto_Edifisio') ?></h6>
                                <input type="file" class="form-control premium-input" name="foto" accept="image/*">
                                <?php if (!empty($data['foto'])): ?>
                                    <div class="mt-3 text-center">
                                        <img src="uploads/edifisio/<?php echo htmlspecialchars($data['foto']); ?>" 
                                             class="rounded-3 shadow-sm border border-secondary" style="max-height: 120px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top border-secondary d-flex gap-3">
                        <button type="submit" class="btn fw-bold px-5" 
                                style="background:linear-gradient(135deg,#FFCC00,#ff9800);color:#002366;border:none;border-radius:12px;box-shadow:0 4px 15px rgba(255,204,0,0.3);">
                            <i class="fa fa-save me-2"></i> <?= __('Altera_Dadus') ?>
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
    var defaultLat = parseFloat(latInput.value) || -8.5569;
    var defaultLng = parseFloat(lngInput.value) || 125.5603;

    var map = L.map('mapPicker').setView([defaultLat, defaultLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
    });

    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        latInput.value = pos.lat.toFixed(6);
        lngInput.value = pos.lng.toFixed(6);
    });
});
</script>
