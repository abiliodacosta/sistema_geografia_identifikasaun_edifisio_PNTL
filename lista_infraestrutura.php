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

// Get All Buildings
$query = "SELECT e.*, s.naran_suco, p.naran_posto, m.naran_munisipio, t.naran_tipu, t.naran_tipu_pt, t.naran_tipu_en 
          FROM tb_edifisio_pntl e 
          JOIN tb_suco s ON e.id_suco = s.id_suco
          JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
          JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio
          JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo
          ORDER BY m.naran_munisipio ASC, p.naran_posto ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">

<head>
    <meta charset="utf-8">
    <title><?= __('Galeria_Title') ?> - PNTL</title>
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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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
            padding-top: 100px;
        }

        .navbar {
            background: var(--pntl-blue) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .status-construction { background: #fef9c3; color: #854d0e; }

        .btn-view {
            background: var(--pntl-blue);
            color: white;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .btn-view:hover {
            background: var(--pntl-gold);
            color: var(--pntl-blue);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--pntl-blue) !important;
            color: white !important;
            border: none;
            border-radius: 6px;
        }

        /* Gallery/Card Styles */
        .gallery-item {
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .gallery-item img {
            width: 100%;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 12px;
            left: 12px;
            right: 12px;
            padding: 18px;
            background: rgba(11, 37, 69, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            color: white;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(101%);
            z-index: 10;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .gallery-item:hover .gallery-overlay,
        .gallery-item.active .gallery-overlay {
            transform: translateY(0);
            background: rgba(11, 37, 69, 0.95);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
        }

        .gallery-overlay h5 {
            font-weight: 700;
            margin-bottom: 8px;
            color: #ffc107;
        }

        .gallery-overlay p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 15px;
        }

        .gallery-overlay .btn {
            background: #ffc107 !important;
            border: none !important;
            color: #0b2545 !important;
            font-weight: 800;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .gallery-overlay .btn:hover {
            background: #ffca2c !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="vizitor/img/pntlall.png" alt="Logo" height="40" class="me-2">
                <h1 class="mb-0" style="color: #ffffff !important; font-weight: 800; font-size: 1.2rem; margin: 0; letter-spacing: 1px; text-transform: uppercase;"><?= __('PNTL_Naran') ?></h1>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 50px; padding: 5px 10px;">
                        <img src="img/flags/<?= $current_lang ?>.png" alt="<?= $current_lang ?>" style="height: 25px; width: 35px; object-fit: cover; border-radius: 4px;">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="langDropdown" style="border-radius: 15px; min-width: 60px; padding: 10px;">
                        <li><a class="dropdown-item d-flex justify-content-center py-2 <?= $current_lang == 'tet' ? 'active' : '' ?>" href="?lang=tet" style="border-radius: 10px;" title="Tetun"><img src="img/flags/tet.png" alt="Tetun" style="height: 25px; width: 35px; object-fit: cover; border-radius: 3px;"></a></li>
                        <li><a class="dropdown-item d-flex justify-content-center py-2 <?= $current_lang == 'pt' ? 'active' : '' ?>" href="?lang=pt" style="border-radius: 10px;" title="Português"><img src="img/flags/pt.png" alt="Português" style="height: 25px; width: 35px; object-fit: cover; border-radius: 3px;"></a></li>
                        <li><a class="dropdown-item d-flex justify-content-center py-2 <?= $current_lang == 'en' ? 'active' : '' ?>" href="?lang=en" style="border-radius: 10px;" title="English"><img src="img/flags/en.png" alt="English" style="height: 25px; width: 35px; object-fit: cover; border-radius: 3px;"></a></li>
                    </ul>
                </div>
                <a href="index.php" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> <?= __('Fila_Baranda') ?>
                </a>
            </div>
        </div>
    </nav>

    <div class="section-header text-center">
        <div class="container">
            <h2 class="fw-bold mb-2" style="color: var(--pntl-blue);"><?= __('Galeria_Title') ?></h2>
            <p class="text-muted"><?= __('Distribusaun_Title') ?></p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text border-0 bg-white ps-4">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="cardSearch" class="form-control border-0 py-3" placeholder="<?= __('Search') ?>">
                </div>
            </div>
        </div>

        <div class="row g-4" id="infraGrid">
            <?php 
            mysqli_data_seek($result, 0); // Reset result pointer
            while($row = mysqli_fetch_assoc($result)): 
                // Localization for dynamic data
                $naran_edifisio = $row['naran_edifisio'];
                $naran_tipu = $row['naran_tipu'];
                if ($current_lang != 'tet') {
                    if (!empty($row["naran_edifisio_$current_lang"])) $naran_edifisio = $row["naran_edifisio_$current_lang"];
                    if (!empty($row["naran_tipu_$current_lang"])) $naran_tipu = $row["naran_tipu_$current_lang"];
                }
            ?>
            <div class="col-lg-4 col-md-6 infra-card-item" 
                 data-title="<?php echo strtolower($naran_edifisio); ?>" 
                 data-municipality="<?php echo strtolower($row['naran_munisipio']); ?>"
                 data-type="<?php echo strtolower($naran_tipu); ?>">
                <div class="gallery-item h-100 mb-0 shadow-sm" style="border-radius: 20px;">
                    <img src="admin/uploads/edifisio/<?php echo $row['foto']; ?>" alt="<?php echo htmlspecialchars($naran_edifisio); ?>" class="w-100" style="height: 220px; object-fit: cover;">
                    <div class="gallery-overlay">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-warning text-dark px-3 rounded-pill"><?php echo htmlspecialchars($naran_tipu); ?></span>
                            <span class="status-badge <?php 
                                if($row['status'] == 'Uza hela') echo 'status-active';
                                elseif($row['status'] == 'Labele uza') echo 'status-inactive';
                                else echo 'status-construction';
                            ?>" style="font-size: 0.65rem; padding: 4px 10px;">
                                <?php 
                                    $status_map = [
                                        'Uza hela' => 'Uza_Hela',
                                        'Labele uza' => 'Labele_Uza',
                                        'Konstrusaun hela' => 'Konstrusaun_Hela'
                                    ];
                                    $status_key = isset($status_map[$row['status']]) ? $status_map[$row['status']] : $row['status'];
                                    echo __($status_key); 
                                ?>
                            </span>
                        </div>
                        <h5 class="mb-1 text-warning"><?php echo htmlspecialchars($naran_edifisio); ?></h5>
                        <p class="small mb-3" style="color: rgba(255,255,255,0.8);">
                            <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($row['naran_munisipio']); ?>, <?php echo htmlspecialchars($row['naran_posto']); ?>
                        </p>
                        <a href="detail.php?id=<?php echo $row['id_edifisio']; ?>" class="btn btn-sm btn-warning w-100 fw-bold py-2 rounded-pill shadow-sm">
                            <i class="fas fa-eye me-1"></i> <?= __('Haree_Detalle') ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div id="noResults" class="text-center py-5 d-none">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted"><?= __('Empty_Foto') ?></h4>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Live Search Logic
            $("#cardSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                var items = $(".infra-card-item");
                var visibleCount = 0;

                items.filter(function() {
                    var match = $(this).data("title").indexOf(value) > -1 || 
                                $(this).data("municipality").indexOf(value) > -1 || 
                                $(this).data("type").indexOf(value) > -1;
                    
                    $(this).toggle(match);
                    if(match) visibleCount++;
                });

                if(visibleCount === 0) {
                    $("#noResults").removeClass("d-none");
                } else {
                    $("#noResults").addClass("d-none");
                }
            });

            // Card Toggle Logic (Click to show)
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.closest('.btn')) return;

                    e.stopPropagation();
                    const isActive = this.classList.contains('active');
                    
                    galleryItems.forEach(i => i.classList.remove('active'));
                    
                    if (!isActive) {
                        this.classList.add('active');
                    }
                });
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.gallery-item')) {
                    galleryItems.forEach(item => item.classList.remove('active'));
                }
            });
        });
    </script>
</body>

</html>
