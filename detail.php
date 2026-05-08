<?php
session_start();

// Handle language selection
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['tet', 'pt', 'en'])) {
        $_SESSION['lang'] = $lang;
    }
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tet';

$lang_file = "lang/{$current_lang}.json";
if (file_exists($lang_file)) {
    $lang_data = json_decode(file_get_contents($lang_file), true);
} else {
    $lang_data = [];
}

function __($key)
{
    global $lang_data;
    return isset($lang_data[$key]) ? $lang_data[$key] : $key;
}

require_once 'admin/koneksaun.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: index.php");
    exit;
}

// Get Building Detail - Fix SQL Injection
$query = "SELECT e.*, s.naran_suco, p.naran_posto, m.naran_munisipio, t.naran_tipu, t.naran_tipu_pt, t.naran_tipu_en 
          FROM tb_edifisio_pntl e 
          JOIN tb_suco s ON e.id_suco = s.id_suco
          JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
          JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio
          JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo
          WHERE e.id_edifisio = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php");
    exit;
}

// Lokalizasaun dinámiku ba dadus database
if ($current_lang != 'tet') {
    if (!empty($data["naran_edifisio_$current_lang"])) $data['naran_edifisio'] = $data["naran_edifisio_$current_lang"];
    if (!empty($data["naran_tipu_$current_lang"])) $data['naran_tipu'] = $data["naran_tipu_$current_lang"];
    if (!empty($data["diskrisaun_$current_lang"])) $data['diskrisaun'] = $data["diskrisaun_$current_lang"];
}
?>

<!DOCTYPE html>
<html lang="tet">

<head>
    <meta charset="utf-8">
    <title><?php echo $data['naran_edifisio']; ?> - <?= __('Detallu') ?> PNTL</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="admin/img/pntl.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        :root {
            --pntl-blue: #0b2545;
            --pntl-gold: #ffc107;
            --pntl-light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--pntl-light);
            color: #334155;
            padding-top: 80px;
        }

        .navbar {
            background-color: var(--pntl-blue);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        }

        .detail-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .detail-header {
            padding: 30px;
            background: linear-gradient(135deg, var(--pntl-blue) 0%, #1e3a8a 100%);
            color: white;
        }

        .detail-header h1 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .category-badge {
            background: rgba(255, 193, 7, 0.2);
            color: var(--pntl-gold);
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid var(--pntl-gold);
        }

        .image-container {
            height: 400px;
            width: 100%;
            overflow: hidden;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .info-item i {
            font-size: 1.5rem;
            color: #3b82f6;
            margin-top: 5px;
        }

        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #1e293b;
        }

        .description-box {
            padding: 0 30px 30px;
        }

        .description-box h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--pntl-blue);
        }

        .description-content {
            line-height: 1.8;
            color: #475569;
        }

        #detail-map {
            height: 400px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-back {
            background: var(--pntl-gold);
            color: var(--pntl-blue);
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: white;
            color: var(--pntl-blue);
            transform: translateX(-5px);
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-construction {
            background: #fef9c3;
            color: #854d0e;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="vizitor/img/pntlall.png" alt="Logo" height="40" class="me-2">
                <h1 class="mb-0" style="color: #ffffff !important; font-weight: 800; font-size: 1.2rem; margin: 0; letter-spacing: 1px; text-transform: uppercase;"><?= __('PNTL_Naran') ?><span style="color: #ffc107 !important;"></span></h1>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 50px; padding: 5px 10px;">
                        <img src="img/flags/<?= $current_lang ?>.png" alt="<?= $current_lang ?>" style="height: 25px; width: 35px; object-fit: cover; border-radius: 4px;">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="langDropdown" style="border-radius: 15px; min-width: 60px; padding: 10px;">
                        <li><a class="dropdown-item d-flex justify-content-center py-2 <?= $current_lang == 'tet' ? 'active' : '' ?>" href="?id=<?= $id ?>&lang=tet" style="border-radius: 10px;"><img src="img/flags/tet.png" alt="Tetun" style="height: 25px; width: 35px; object-fit: cover; border-radius: 3px;"></a></li>
                        <li><a class="dropdown-item d-flex justify-content-center py-2 <?= $current_lang == 'pt' ? 'active' : '' ?>" href="?id=<?= $id ?>&lang=pt" style="border-radius: 10px;"><img src="img/flags/pt.png" alt="Português" style="height: 25px; width: 35px; object-fit: cover; border-radius: 3px;"></a></li>
                        <li><a class="dropdown-item d-flex justify-content-center py-2 <?= $current_lang == 'en' ? 'active' : '' ?>" href="?id=<?= $id ?>&lang=en" style="border-radius: 10px;"><img src="img/flags/en.png" alt="English" style="height: 25px; width: 35px; object-fit: cover; border-radius: 3px;"></a></li>
                    </ul>
                </div>
                <a href="index.php" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> <?= __('Fila_Baranda') ?>
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="detail-card">
                    <div class="image-container">
                        <?php if (!empty($data['foto'])): ?>
                            <img src="admin/uploads/edifisio/<?php echo htmlspecialchars($data['foto']); ?>" alt="Foto">
                        <?php else: ?>
                            <img src="admin/img/no-image.jpg" alt="No Image" style="object-fit: contain; background: #eee; padding: 50px;">
                        <?php endif; ?>
                    </div>
                    <div class="detail-header">
                        <span class="category-badge mb-2 d-inline-block"><?php echo htmlspecialchars($data['naran_tipu']); ?></span>
                        <h1><?php echo htmlspecialchars($data['naran_edifisio']); ?></h1>
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-badge <?php
                                                        if ($data['status'] == 'Uza hela') echo 'status-active';
                                                        elseif ($data['status'] == 'Labele uza') echo 'status-inactive';
                                                        else echo 'status-construction';
                                                        ?>">
                                <i class="fas fa-info-circle me-1"></i> <?php
                                                                        $status_map = [
                                                                            'Uza hela' => 'Uza_Hela',
                                                                            'Labele uza' => 'Labele_Uza',
                                                                            'Konstrusaun hela' => 'Konstrusaun_Hela'
                                                                        ];
                                                                        $status_key = isset($status_map[$data['status']]) ? $status_map[$data['status']] : str_replace(' ', '_', $data['status']);
                                                                        echo __($status_key);
                                                                        ?>
                            </span>
                            <span class="text-white-50 small"><i class="fas fa-calendar-alt me-1"></i> <?= __('Tinan') ?>: <?php echo htmlspecialchars($data['tinan_konstrusaun']); ?></span>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-city"></i>
                            <div>
                                <div class="info-label"><?= __('Munisipiu') ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($data['naran_munisipio']); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-layer-group"></i>
                            <div>
                                <div class="info-label"><?= __('Postu') ?> / <?= __('Suku') ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($data['naran_posto']); ?> / <?php echo htmlspecialchars($data['naran_suco']); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <div class="info-label"><?= __('Enderesu') ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($data['enderesu']); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-compass"></i>
                            <div>
                                <div class="info-label"><?= __('Koordenada') ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($data['latitude']); ?>, <?php echo htmlspecialchars($data['longtitude']); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="description-box">
                        <h3><?= __('Diskrisaun') ?></h3>
                        <div class="description-content">
                            <?php echo !empty($data['diskrisaun']) ? nl2br(htmlspecialchars($data['diskrisaun'])) : '<i>' . __('Laiha_Diskrisaun') . '</i>'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div id="detail-map"></div>

                <div class="detail-card p-4">
                    <h4 class="mb-4" style="color: var(--pntl-blue); font-weight: 700;"><?= __('Asaun') ?></h4>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($data['naran_edifisio']); ?>&destination_place_id=&travelmode=driving" target="_blank" class="btn btn-primary w-100 py-3 mb-3 rounded-3" style="background: var(--pntl-blue); border: none;">
                        <i class="fas fa-directions me-2"></i> <?= __('Hetan_Direisaun') ?>
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100 py-3 rounded-3">
                        <i class="fas fa-print me-2"></i> <?= __('Imprime_Detalle') ?>
                    </button>
                </div>

                <a href="index.php" class="btn-back mt-4 w-100">
                    <i class="fas fa-arrow-left"></i> <?= __('Fila_Baranda') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var lat = <?php echo $data['latitude']; ?>;
            var lng = <?php echo $data['longtitude']; ?>;

            var map = L.map('detail-map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var pntlIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:#ffffff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid #0b2545; box-shadow: 0 3px 6px rgba(0,0,0,0.3); overflow: hidden;">
                        <img src="vizitor/img/pntlall.png" style="width: 80%; height: 80%; object-fit: contain;">
                       </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            L.marker([lat, lng], {
                    icon: pntlIcon
                }).addTo(map)
                .bindPopup("<b><?php echo htmlspecialchars($data['naran_edifisio']); ?></b>")
                .openPopup();
        });
    </script>
</body>

</html>