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

// Function to get translated string
function __($key)
{
    global $lang_data;
    return isset($lang_data[$key]) ? $lang_data[$key] : $key;
}

require_once 'admin/koneksaun.php';
mysqli_set_charset($conn, 'utf8');

// Get Markers ba Mapa
$markers_data = [];
$q_map = mysqli_query($conn, "SELECT e.*, s.naran_suco, p.naran_posto, m.naran_munisipio, t.naran_tipu, t.naran_tipu_pt, t.naran_tipu_en 
                              FROM tb_edifisio_pntl e 
                              LEFT JOIN tb_suco s ON e.id_suco = s.id_suco
                              LEFT JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
                              LEFT JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio
                              LEFT JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo");

if ($q_map) {
    while ($row = mysqli_fetch_assoc($q_map)) {
        // Lokalizasaun dinámiku
        $title = $row['naran_edifisio'];
        $tipo = $row['naran_tipu'];

        if ($current_lang != 'tet') {
            if (!empty($row["naran_edifisio_$current_lang"])) $title = $row["naran_edifisio_$current_lang"];
            if (!empty($row["naran_tipu_$current_lang"])) $tipo = $row["naran_tipu_$current_lang"];
        }

        // Lokaliza Status
        $status_map = [
            'Uza hela' => 'Uza_Hela',
            'Labele uza' => 'Labele_Uza',
            'Konstrusaun hela' => 'Konstrusaun_Hela'
        ];
        $status_key = isset($status_map[$row['status']]) ? $status_map[$row['status']] : str_replace(' ', '_', $row['status']);
        $status_localized = __($status_key);

        $markers_data[] = [
            'id'        => (int)$row['id_edifisio'],
            'lat'       => floatval($row['latitude']),
            'lng'       => floatval($row['longtitude']),
            'title'     => mb_convert_encoding((string)$title,     'UTF-8', 'auto'),
            'tipo'      => mb_convert_encoding((string)$tipo,      'UTF-8', 'auto'),
            'address'   => mb_convert_encoding((string)$row['enderesu'],      'UTF-8', 'auto'),
            'munisipio' => mb_convert_encoding((string)$row['naran_munisipio'], 'UTF-8', 'auto'),
            'postu'     => mb_convert_encoding((string)$row['naran_posto'],    'UTF-8', 'auto'),
            'suco'      => mb_convert_encoding((string)$row['naran_suco'],     'UTF-8', 'auto'),
            'foto'      => (string)$row['foto'],
            'status'    => mb_convert_encoding((string)$status_localized,      'UTF-8', 'auto')
        ];
    }
}
$markers_json = json_encode($markers_data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
if ($markers_json === false) $markers_json = '[]';

// Get Gallery Photos
$gallery_photos = [];
$q_gallery = mysqli_query($conn, "SELECT id_edifisio, naran_edifisio, naran_edifisio_pt, naran_edifisio_en, foto, enderesu FROM tb_edifisio_pntl WHERE foto IS NOT NULL AND foto != '' ORDER BY id_edifisio DESC LIMIT 6");
if ($q_gallery) {
    while ($row = mysqli_fetch_assoc($q_gallery)) {
        if ($current_lang != 'tet' && !empty($row["naran_edifisio_$current_lang"])) {
            $row['naran_edifisio'] = $row["naran_edifisio_$current_lang"];
        }
        $gallery_photos[] = $row;
    }
}

// Get Dynamic Counts for Statistics & Hero
$total_munisipio = mysqli_num_rows(mysqli_query($conn, "SELECT id_munisipio FROM tb_munisipio"));
$total_postu     = mysqli_num_rows(mysqli_query($conn, "SELECT id_posto FROM tb_postu_administrativu"));
$total_suco      = mysqli_num_rows(mysqli_query($conn, "SELECT id_suco FROM tb_suco"));
$total_edifisio  = mysqli_num_rows(mysqli_query($conn, "SELECT id_edifisio FROM tb_edifisio_pntl"));
?>

<!DOCTYPE html>
<html lang="tet">

<head>
    <meta charset="utf-8">
    <title>PNTL - Sistema Informasaun Edifisiu</title>
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
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            padding-top: 76px;
            /* Offset for fixed navbar */
        }

        /* Navbar Style */
        .navbar {
            background-color: rgba(11, 37, 69, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 15px 0;
        }

        .navbar.navbar-scrolled {
            background-color: rgba(11, 37, 69, 0.98);
            padding: 10px 0;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand img {
            transition: all 0.4s ease;
        }

        .navbar.navbar-scrolled .navbar-brand img {
            height: 45px !important;
        }

        .navbar-brand h1 {
            color: #ffffff;
            font-weight: 800;
            font-size: 1.1rem;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .navbar-brand span {
            color: #ffc107;
        }

        .nav-item {
            position: relative;
            margin: 0 5px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 15px !important;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .nav-link:hover {
            color: #ffc107 !important;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: #ffc107 !important;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #ffc107;
            bottom: 5px;
            left: 15px;
            right: 15px;
            transition: width 0.3s;
            border-radius: 2px;
        }

        .nav-link.active::after {
            width: calc(100% - 30px);
        }

        .btn-login {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #0b2545 !important;
            font-weight: 700;
            border-radius: 50px;
            padding: 10px 25px !important;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.5);
            background: linear-gradient(135deg, #ffca2c 0%, #ffa726 100%);
        }

        /* Language Flag Button — styled like other menu items */
        #langDropdown {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
            padding: 8px 15px !important;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        #langDropdown:hover,
        #langDropdown.show {
            background: rgba(255, 255, 255, 0.1) !important;
        }

        #langDropdown::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #ffc107;
            bottom: 5px;
            left: 15px;
            right: 15px;
            transition: width 0.3s;
            border-radius: 2px;
        }

        #langDropdown:hover::after,
        #langDropdown.show::after {
            width: calc(100% - 30px);
        }

        #langDropdown img {
            border-radius: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        #langDropdown:hover img {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        /* Remove white background from flag dropdown items */
        .dropdown-menu[aria-labelledby="langDropdown"] {
            background: rgba(10, 25, 49, 0.85) !important;
            backdrop-filter: blur(20px);
            min-width: 85px !important;
        }

        .dropdown-menu[aria-labelledby="langDropdown"] .dropdown-item {
            background: transparent !important;
            background-color: transparent !important;
        }

        .dropdown-menu[aria-labelledby="langDropdown"] .dropdown-item:hover,
        .dropdown-menu[aria-labelledby="langDropdown"] .dropdown-item:focus {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        .dropdown-menu[aria-labelledby="langDropdown"] .dropdown-item.active {
            background: rgba(255, 193, 7, 0.15) !important;
        }

        .dropdown-menu[aria-labelledby="langDropdown"] .dropdown-item span {
            font-size: 0.65rem;
            color: #fff;
            margin-top: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Sections */
        .section-padding {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            color: #0b2545;
            font-weight: 700;
        }

        .section-title::after {
            content: '';
            width: 60px;
            height: 4px;
            background: #ffc107;
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Hero Section */
        #baranda {
            position: relative;
            min-height: calc(100vh - 76px);
            background: #0b2545;
            color: white;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: #0b2545;
        }

        .hero-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            z-index: 1;
            animation: heroSlideshow 15s linear infinite;
        }

        @keyframes heroSlideshow {
            0% {
                opacity: 0;
                transform: scale(1);
            }

            8% {
                opacity: 0.4;
            }

            33% {
                opacity: 0.4;
            }

            41% {
                opacity: 0;
                transform: scale(1.1);
            }

            100% {
                opacity: 0;
            }
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(11, 37, 69, 0.92) 0%, rgba(11, 37, 69, 0.5) 60%, transparent 100%);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            max-width: 750px;
            padding: 60px 0 60px 5%;
        }

        /* Hero Badge */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 193, 7, 0.15);
            border: 1px solid rgba(255, 193, 7, 0.4);
            color: #ffc107;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
            animation: fadeInDown 0.6s ease;
        }

        /* Hero Title */
        .hero-content h1 {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
            color: #ffffff;
            animation: fadeInLeft 0.7s ease;
        }

        .hero-highlight {
            color: #ffc107;
            position: relative;
        }

        /* Hero Description */
        .hero-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 35px;
            animation: fadeInLeft 0.8s ease;
        }

        /* Hero Buttons */
        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 50px;
            animation: fadeInLeft 0.9s ease;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #0b2545;
            font-weight: 700;
            font-size: 1rem;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.35);
            transition: all 0.3s ease;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 193, 7, 0.5);
            color: #0b2545;
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            padding: 13px 30px;
            border-radius: 50px;
            text-decoration: none;
            border: 2px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            color: white;
            transform: translateY(-3px);
        }

        /* Hero Stats */
        .hero-stats {
            display: flex;
            align-items: center;
            gap: 25px;
            animation: fadeInUp 1s ease;
        }

        .hero-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero-stat strong {
            font-size: 1.8rem;
            font-weight: 800;
            color: #ffc107;
            line-height: 1;
        }

        .hero-stat span {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 3px;
        }

        .hero-stat-divider {
            width: 1px;
            height: 35px;
            background: rgba(255, 255, 255, 0.2);
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            width: 28px;
            height: 44px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 14px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }

        .scroll-dot {
            width: 5px;
            height: 10px;
            background: #ffc107;
            border-radius: 3px;
            animation: scrollBounce 1.8s ease infinite;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scrollBounce {

            0%,
            100% {
                transform: translateY(0);
                opacity: 1;
            }

            50% {
                transform: translateY(8px);
                opacity: 0.4;
            }
        }

        /* Map Section */
        #mapa-section {
            position: relative;
            height: 600px;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }

        .map-header {
            background: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .map-title {
            font-size: 1.2rem;
            color: #0b2545;
            margin: 0;
            font-weight: 600;
        }

        #map-container {
            flex: 1;
            position: relative;
            width: 100%;
        }

        #map {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        /* Leaflet Popup Styling */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-content {
            margin: 15px;
            line-height: 1.5;
        }

        .info-title {
            color: #0b2545;
            font-size: 16px;
            font-weight: 700;
            border-bottom: 2px solid #ffc107;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-category {
            background: #f1f5f9;
            color: #64748b;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .info-detail {
            font-size: 13px;
            color: #475569;
            margin-bottom: 5px;
        }

        .info-detail i {
            color: #3b82f6;
            width: 16px;
            text-align: center;
            margin-right: 5px;
        }

        .btn-direction {
            display: block;
            background: #0b2545;
            color: #fff !important;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 15px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-direction:hover {
            background: #1e3a8a;
        }

        .btn-view-detail {
            display: block;
            background: #ffc107;
            color: #0b2545 !important;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 10px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-view-detail:hover {
            background: #ffca2c;
            transform: translateY(-1px);
        }

        /* Konaba Section */
        #konaba {
            background: #ffffff;
        }

        .about-img {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        .about-text p {
            color: #64748b;
            line-height: 1.8;
            font-size: 1.1rem;
            text-align: justify;
        }

        .about-image-wrapper {
            position: relative;
            padding: 15px;
        }

        .about-image-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: 80px;
            border-top: 5px solid #ffc107;
            border-left: 5px solid #ffc107;
            border-radius: 20px 0 0 0;
        }

        .about-image-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border-bottom: 5px solid #0b2545;
            border-right: 5px solid #0b2545;
            border-radius: 0 0 20px 0;
        }

        .about-logo-badge {
            position: absolute;
            bottom: -30px;
            right: 0px;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            z-index: 2;
            border: 1px solid #eee;
        }

        .about-logo-badge img {
            height: 60px;
        }

        /* Galeria Section */
        #galeria {
            background: #f8f9fa;
        }

        .gallery-item {
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
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

        /* Informasaun Section */
        #informasaun {
            background: #ffffff;
        }

        .info-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .info-icon {
            width: 60px;
            height: 60px;
            background: #e0f2fe;
            color: #0b2545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .info-card p {
            text-align: justify;
        }

        /* Footer */
        .footer {
            background: #0b2545;
            color: #cbd5e1;
            padding: 80px 0 30px;
        }

        .footer-logo {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .footer-logo img {
            height: 50px;
            margin-right: 15px;
        }

        .footer h5 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: #ffc107;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .footer-links a:hover {
            color: #ffc107;
            transform: translateX(5px);
        }

        .footer-contact-item {
            display: flex;
            margin-bottom: 15px;
            gap: 15px;
        }

        .footer-contact-item i {
            color: #ffc107;
            font-size: 1.1rem;
            margin-top: 4px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background: #ffc107;
            color: #0b2545;
            transform: translateY(-3px);
        }

        .footer hr {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 50px 0 30px;
        }

        .copyright {
            font-size: 0.9rem;
            color: #64748b;
        }

        /* ===== RESPONSIVE: TABLET (max 992px) ===== */
        @media (max-width: 992px) {
            .hero-content {
                padding-left: 5%;
                padding-right: 5%;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .about-img {
                height: 320px;
            }

            .section-padding {
                padding: 60px 0;
            }

            #mapa-section {
                height: auto;
                min-height: 500px;
            }

            #map-container {
                height: 450px;
                position: relative;
            }

            #map {
                position: relative;
                height: 450px;
            }
        }

        /* ===== RESPONSIVE: MOBILE (max 768px) ===== */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            /* Navbar mobile */
            .navbar {
                padding: 8px 0;
            }

            .navbar-brand img {
                height: 36px !important;
                margin-right: 8px !important;
            }

            .navbar-brand h1 {
                font-size: 0.9rem !important;
                letter-spacing: 0.5px !important;
            }

            /* Toggler button ki'ik */
            .navbar-toggler {
                padding: 4px 8px !important;
                border: 1px solid rgba(255, 255, 255, 0.3) !important;
                border-radius: 6px !important;
                font-size: 0.8rem !important;
            }

            .navbar-toggler-icon {
                width: 1.2em !important;
                height: 1.2em !important;
            }

            .navbar-collapse {
                background: rgba(11, 37, 69, 0.98);
                padding: 15px;
                border-radius: 0 0 12px 12px;
                margin-top: 8px;
            }

            .nav-link {
                padding: 10px 15px !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            /* Hero Section mobile */
            #baranda {
                align-items: center;
                min-height: auto;
                padding: 60px 0 40px 0;
            }

            .hero-content {
                padding: 20px;
                max-width: 100%;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .hero-badge {
                font-size: 0.72rem;
                padding: 6px 14px;
                width: fit-content;
                margin-bottom: 15px;
            }

            .hero-content h1 {
                font-size: 1.75rem;
                margin-bottom: 15px;
                width: 100%;
            }

            .hero-content p {
                font-size: 0.95rem;
                margin-bottom: 30px;
                width: 100%;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 10px;
                margin-bottom: 35px;
                width: 100%;
            }

            .btn-hero-primary {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 15px 20px;
                font-size: 1rem;
                border-radius: 12px;
            }

            .btn-hero-secondary {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 14px 20px;
                font-size: 1rem;
                border-radius: 12px;
            }

            .hero-stats {
                gap: 15px;
                justify-content: center;
                width: 100%;
            }

            .hero-stat strong {
                font-size: 1.4rem;
            }

            .scroll-indicator {
                display: none;
            }

            /* Section padding mobile */
            .section-padding {
                padding: 50px 0;
            }

            .section-title {
                font-size: 1.5rem;
                margin-bottom: 35px;
            }

            /* Map Section mobile */
            #mapa-section {
                height: auto;
                min-height: 420px;
            }

            #map-container {
                height: 380px;
                position: relative;
            }

            #map {
                position: relative;
                height: 380px;
            }

            .map-header {
                flex-direction: column;
                gap: 10px;
                padding: 12px 15px;
                text-align: center;
            }

            .map-title {
                font-size: 1rem;
            }

            /* About Section mobile */
            .about-text {
                text-align: center;
            }

            .about-text p {
                text-align: center;
            }

            .about-text .d-flex {
                justify-content: center !important;
            }

            .about-img {
                height: 250px;
                margin-bottom: 20px;
            }

            .about-image-wrapper::before,
            .about-image-wrapper::after {
                display: none;
            }

            .about-logo-badge {
                display: none;
            }

            /* Gallery mobile - floating glass card */
            .gallery-overlay {
                bottom: 12px !important;
                left: 12px !important;
                right: 12px !important;
                width: auto !important;
                transform: translateY(101%) !important;
                padding: 18px;
                border-radius: 18px;
            }

            .gallery-item:hover .gallery-overlay,
            .gallery-item.active .gallery-overlay {
                transform: translateY(0) !important;
            }

            .gallery-overlay h5 {
                font-size: 0.9rem;
                margin-bottom: 4px;
            }

            .gallery-item img {
                height: 200px;
            }

            /* Info cards mobile */
            .info-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
                margin-left: auto;
                margin-right: auto;
            }

            .info-card {
                text-align: center;
            }

            .info-card p {
                text-align: center;
            }

            /* Footer mobile */
            .footer {
                padding: 50px 0 25px;
                text-align: center;
            }

            .footer-logo {
                justify-content: center;
            }

            .footer h5::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-links a:hover {
                transform: none;
            }

            .social-icons {
                justify-content: center;
            }

            .footer-contact-item {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .footer-contact-item i {
                margin-top: 0;
            }
        }

        /* ===== RESPONSIVE: SMALL MOBILE (max 576px) ===== */
        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 1.5rem;
            }

            .hero-content p {
                font-size: 0.95rem;
            }

            #mapa-section {
                min-height: 350px;
            }

            #map-container,
            #map {
                height: 320px;
            }

            .gallery-item img {
                height: 180px;
            }

            .leaflet-popup-content-wrapper {
                max-width: 260px;
            }
        }
    </style>
</head>

<body data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="80">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
        <div class="container">
            <a href="#baranda" class="navbar-brand d-flex align-items-center text-decoration-none">
                <img src="vizitor/img/pntlall.png" alt="PNTL Logo" style="height: 55px; margin-right: 12px;">
                <h1 style="color: #ffffff !important; font-weight: 800; font-size: 1.2rem; margin: 0; letter-spacing: 1px; text-transform: uppercase;"><?= __('PNTL_Naran') ?> <span style="color: #ffc107 !important;"></span></h1>
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a href="#baranda" class="nav-link"><?= __('Baranda') ?></a></li>
                    <li class="nav-item"><a href="#mapa-section" class="nav-link"><?= __('Maps') ?></a></li>
                    <li class="nav-item"><a href="#konaba" class="nav-link"><?= __('Kona-ba') ?></a></li>
                    <li class="nav-item"><a href="#galeria" class="nav-link"><?= __('Galeria') ?></a></li>
                    <li class="nav-item"><a href="#informasaun" class="nav-link"><?= __('Informasaun') ?></a></li>
                    <li class="nav-item"><a href="#kontaktu" class="nav-link"><?= __('Contactu') ?></a></li>
                </ul>
                <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-3 mt-3 mt-lg-0">
                    <div class="dropdown">
                        <button class="btn p-0 border-0 d-flex align-items-center justify-content-center" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent !important; box-shadow: none !important; outline: none !important; transition: all 0.4s ease;">
                            <img src="img/flags/<?= $current_lang ?>.png" alt="<?= $current_lang ?>" style="height: 26px; width: 38px; object-fit: cover; border-radius: 5px; opacity: 0.9; transition: all 0.3s ease;">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 animate__animated animate__fadeInUp" aria-labelledby="langDropdown" style="border-radius: 18px; min-width: 85px; background: rgba(10, 25, 49, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12) !important;">
                            <li><a class="dropdown-item d-flex flex-column align-items-center py-2 mb-2 <?= $current_lang == 'tet' ? 'active' : '' ?>" href="?lang=tet" style="border-radius: 12px; transition: 0.3s;" title="Tetun">
                                    <img src="img/flags/tet.png" alt="Tetun" style="height: 24px; width: 36px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                    <span>Tetun</span>
                                </a></li>
                            <li><a class="dropdown-item d-flex flex-column align-items-center py-2 mb-2 <?= $current_lang == 'pt' ? 'active' : '' ?>" href="?lang=pt" style="border-radius: 12px; transition: 0.3s;" title="Português">
                                    <img src="img/flags/pt.png" alt="Português" style="height: 24px; width: 36px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                    <span>Portuges</span>
                                </a></li>
                            <li><a class="dropdown-item d-flex flex-column align-items-center py-2 <?= $current_lang == 'en' ? 'active' : '' ?>" href="?lang=en" style="border-radius: 12px; transition: 0.3s;" title="English">
                                    <img src="img/flags/en.png" alt="English" style="height: 24px; width: 36px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                    <span>Englis</span>
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Baranda / Hero Section -->
    <section id="baranda">
        <div class="hero-bg">
            <?php if (!empty($gallery_photos)):
                $slides = array_slice($gallery_photos, 0, 3);
                foreach ($slides as $index => $photo): ?>
                    <div class="hero-slide" style="background-image: url('admin/uploads/edifisio/<?php echo $photo['foto']; ?>'); animation-delay: <?php echo $index * 5; ?>s;"></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1541888040520-227bb3098f98?q=80&w=1200'); animation-delay: 0s;"></div>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=1200'); animation-delay: 5s;"></div>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1534124412952-274996351b62?q=80&w=1200'); animation-delay: 10s;"></div>
            <?php endif; ?>
        </div>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <!-- Badge -->
                <!-- <div class="hero-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Polísia Nasionál Timor-Leste</span>
                </div> -->
                <!-- Main Title -->
                <p class="hero-subtitle mb-2" style="font-weight: 500; letter-spacing: 2px;">
                    <?= __('Sistema_Title') ?>
                </p>
                <h2 class="hero-title mb-3">
                    <span class="hero-highlight"><?= __('PNTL_Naran') ?></span>
                </h2>
                <!-- Description -->
                <p><?= __('Monitorizasaun_Desc') ?></p>
                <!-- Buttons -->
                <div class="hero-buttons">
                    <a href="#mapa-section" class="btn-hero-primary">
                        <i class="fas fa-map-marked-alt me-2"></i> <?= __('Haree_Mapa') ?>
                    </a>
                    <a href="#konaba" class="btn-hero-secondary">
                        <i class="fas fa-info-circle"></i> <?= __('Kona-ba_Ami') ?>
                    </a>
                </div>
                <!-- Stats mini -->
                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong><?php echo $total_edifisio; ?></strong>
                        <span><?= __('Edifisiu') ?></span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <strong><?php echo $total_munisipio; ?></strong>
                        <span><?= __('Munisipiu') ?></span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <strong><?php echo $total_suco; ?></strong>
                        <span><?= __('Suku') ?></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <div class="scroll-dot"></div>
        </div>
    </section>

    <!-- Mapa Section -->
    <section id="mapa-section" class="section-padding">
        <div class="container-fluid px-4">
            <div class="map-header d-flex flex-wrap gap-3 mb-4 rounded shadow-sm">
                <h2 class="map-title"><i class="fas fa-map-marked-alt text-primary me-2"></i> <?= __('Distribusaun_Title') ?></h2>
                <div class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                    <?= __('Total_Edifisiu') ?>: <span id="total-buildings">0</span>
                </div>
            </div>
            <div id="map-container" style="height: 600px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div id="map" style="height: 100%;"></div>
            </div>
        </div>
    </section>


    <!-- Kona-ba Section -->
    <section id="konaba" class="section-padding">
        <div class="container">
            <h2 class="section-title"><?= __('Kona-ba_Sistema_PNTL') ?></h2>
            <div class="row align-items-center g-5 mt-3">
                <div class="col-lg-6">
                    <div class="about-image-wrapper">
                        <img src="img/pntl_about.png" alt="PNTL Building" class="about-img shadow-lg">

                    </div>
                </div>
                <div class="col-lg-6 about-text">
                    <div class="d-flex align-items-center mb-3">

                        <h3 class="m-0" style="color: #0b2545; font-weight: 700;"><?= __('PNTL_Naran') ?></h3>
                    </div>
                    <p class="lead"><?= __('Konaba_Desc_1') ?></p>
                    <p><?= __('Konaba_Desc_2') ?></p>
                    <div class="d-flex mt-4 gap-4 flex-wrap">
                        <div class="text-center me-3">
                            <h2 style="color: #ffc107; font-weight: 800;"><?php echo $total_munisipio; ?></h2>
                            <span class="text-muted fw-bold"><?= __('Munisipiu') ?></span>
                        </div>
                        <div class="text-center me-3">
                            <h2 style="color: #ffc107; font-weight: 800;"><?php echo $total_postu; ?></h2>
                            <span class="text-muted fw-bold"><?= __('Postu') ?></span>
                        </div>
                        <div class="text-center me-3">
                            <h2 style="color: #ffc107; font-weight: 800;"><?php echo $total_suco; ?></h2>
                            <span class="text-muted fw-bold"><?= __('Suku') ?></span>
                        </div>
                        <div class="text-center">
                            <h2 style="color: #ffc107; font-weight: 800;"><?php echo $total_edifisio; ?></h2>
                            <span class="text-muted fw-bold"><?= __('Edifisiu') ?></span>
                        </div>
                        <div class="text-center">
                            <h2 style="color: #ffc107; font-weight: 800;">24/7</h2>
                            <span class="text-muted fw-bold"><?= __('Seguransa') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeria Section -->
    <section id="galeria" class="section-padding">
        <div class="container">
            <h2 class="section-title"><?= __('Galeria_Title') ?></h2>
            <div class="row mt-5">
                <?php if (empty($gallery_photos)): ?>
                    <div class="col-12 text-center">
                        <p class="text-muted italic"><?= __('Empty_Foto') ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($gallery_photos as $photo): ?>
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <img src="admin/uploads/edifisio/<?php echo $photo['foto']; ?>" alt="<?php echo $photo['naran_edifisio']; ?>">
                                <div class="gallery-overlay">
                                    <h5><?php echo $photo['naran_edifisio']; ?></h5>
                                    <p class="small mb-2"><?php echo $photo['enderesu']; ?></p>
                                    <a href="detail.php?id=<?php echo $photo['id_edifisio']; ?>" class="btn btn-sm btn-warning w-100 fw-bold">
                                        <i class="fas fa-eye me-1"></i> <?= __('Haree_Detalle') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Butaun Haree Dadus Hotu -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="lista_infraestrutura.php" class="btn btn-primary rounded-pill px-5 py-3 shadow-lg fw-bold" style="background: #0b2545; border: none; transition: all 0.3s ease;">
                        <i class="fas fa-list-alt me-2"></i> <?= __('Haree_Hotu') ?>
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- Informasaun Dinamik Section -->
    <section id="informasaun" class="section-padding">
        <div class="container">
            <h2 class="section-title"><?= __('Info_Title') ?></h2>
            <div class="row g-4 mt-3">
                <div class="col-md-4">
                    <div class="card info-card p-4">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <h4 class="mb-3"><?= __('Linha_Emerjensia') ?></h4>
                        <p class="text-muted"><?= __('Emerjensia_Desc') ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card info-card p-4">
                        <div class="info-icon"><i class="fas fa-bullhorn"></i></div>
                        <h4 class="mb-3"><?= __('Avizu_Publiku') ?></h4>
                        <p class="text-muted"><?= __('Avizu_Desc') ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card info-card p-4">
                        <div class="info-icon"><i class="fas fa-user-shield"></i></div>
                        <h4 class="mb-3"><?= __('Servisu_Polisiamentu') ?></h4>
                        <p class="text-muted"><?= __('Servisu_Desc') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contactu Section -->
    <section id="kontaktu" class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title"><?= __('Kontaktu_Ami') ?></h2>
            <!-- Form Mensajen -->
            <div class="row mt-5 justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg border-0" style="border-radius: 30px; overflow: hidden;">
                        <div class="row g-0">
                            <div class="col-md-5 p-5 text-white d-flex flex-column justify-content-center" style="background: linear-gradient(135deg, #0b2545 0%, #1e3a5f 100%);">
                                <h3 class="fw-bold mb-4"><?= __('Haruka_Mensajen') ?></h3>
                                <p class="mb-5 opacity-75"><?= __('Footer_Desc') ?></p>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-white bg-opacity-10 p-3 rounded-circle me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <span class="small"><?= __('Enderesu_Val') ?></span>
                                </div>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-white bg-opacity-10 p-3 rounded-circle me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span class="small">pntl.timorleste@gmail.com</span>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-10 p-3 rounded-circle me-3">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <span class="small">+670 112 / 331 2345</span>
                                </div>
                            </div>
                            <div class="col-md-7 p-5 bg-white">
                                <form id="contactForm" action="processa_mensajen" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold small text-uppercase text-muted"><?= __('Naran_Kompletu') ?></label>
                                            <input type="text" name="naran" class="form-control bg-light border-0 rounded-3 p-3" placeholder="..." required>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold small text-uppercase text-muted">Email (Gmail)</label>
                                            <input type="email" name="email" class="form-control bg-light border-0 rounded-3 p-3" placeholder="ezemplu@gmail.com" required>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small text-uppercase text-muted"><?= __('Asuntu') ?></label>
                                        <input type="text" name="asuntu" class="form-control bg-light border-0 rounded-3 p-3" placeholder="..." required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small text-uppercase text-muted"><?= __('Mensajen') ?></label>
                                        <textarea name="mensajen" class="form-control bg-light border-0 rounded-3 p-3" rows="4" placeholder="..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-3 shadow-sm hover-up">
                                        <i class="fas fa-paper-plane me-2"></i> <?= __('Haruka_Mensajen') ?>
                                    </button>
                                </form>
                                <div id="formStatus" class="mt-4 text-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5 col-md-12">
                    <div class="footer-logo">
                        <img src="vizitor/img/pntlall.png" alt="PNTL Logo">
                        <?= __('PNTL_Naran') ?>
                    </div>
                    <p class="mb-4"><?= __('Footer_Desc') ?></p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5><?= __('Menu_Aksesu') ?></h5>
                    <ul class="footer-links">
                        <li><a href="#baranda"><?= __('Baranda') ?></a></li>
                        <li><a href="#mapa-section"><?= __('Maps') ?></a></li>
                        <li><a href="#konaba"><?= __('Kona-ba') ?></a></li>
                        <li><a href="#galeria"><?= __('Galeria') ?></a></li>
                        <li><a href="#informasaun"><?= __('Informasaun') ?></a></li>
                        <li><a href="#kontaktu"><?= __('Contactu') ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5><?= __('Kontaktu_Ami') ?></h5>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong><?= __('Enderesu') ?>:</strong><br>
                            <?= __('Enderesu_Val') ?>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <strong><?= __('Telefone') ?>:</strong><br>
                            +670 112 <?= __('Emerjensia_Sub') ?><br>
                            +670 331 2345
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong><?= __('Email') ?>:</strong><br>
                            info@pntl.tl<br>
                            pntl.timorleste@gmail.com
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="copyright mb-md-0">&copy; 2026 <strong><?= __('PNTL_Naran') ?></strong>. <?= __('All_Rights_Reserved') ?>.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="copyright mb-0"><?= __('Desenvolve_Husi') ?> <a href="#" class="text-decoration-none text-white">IPDC</a></p>
                </div>
            </div>
        </div>
    </footer>


    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Data husi PHP
        const markersData = <?php echo $markers_json; ?>;

        // Atualiza total edifisiu
        document.getElementById('total-buildings').textContent = markersData.length;

        // Inicializa Mapa
        function initMap() {
            // Base Maps
            var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18,
                minZoom: 6
            });

            var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                maxZoom: 18,
                minZoom: 6
            });

            // Lokalizasaun default: Timor-Leste
            var map = L.map('map', {
                center: [-8.5569, 125.5603],
                zoom: 8,
                layers: [osmLayer]
            });

            // Layer Control hodi troka mapa
            var baseMaps = {
                "<?= __('Mapa_Padraun') ?> (OSM)": osmLayer,
                "<?= __('Mapa_Satelite') ?> (Esri)": satelliteLayer
            };
            L.control.layers(baseMaps).addTo(map);

            // Custom icon baseia ba PNTL
            var pntlIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:#ffffff; width:35px; height:35px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #0b2545; box-shadow: 0 3px 6px rgba(0,0,0,0.3); overflow: hidden;">
                        <img src="vizitor/img/pntlall.png" style="width: 80%; height: 80%; object-fit: contain;">
                       </div>`,
                iconSize: [35, 35],
                iconAnchor: [17, 17],
                popupAnchor: [0, -17]
            });

            var bounds = [];

            markersData.forEach(item => {
                // Ensure we have valid coordinates
                if (!item.lat || !item.lng || isNaN(item.lat) || isNaN(item.lng)) return;

                var fotoHtml = '';
                if (item.foto) {
                    fotoHtml = `<div style="text-align: center; margin-bottom: 10px;">
                                <img src="admin/uploads/edifisio/${item.foto}" alt="Foto" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">
                            </div>`;
                }

                var popupContent = `
                ${fotoHtml}
                <div class="info-title">${item.title}</div>
                <div class="info-category">${item.tipo || '<?= __('Edifisio_PNTL') ?>'}</div>
                
                <div class="info-detail">
                    <i class="fas fa-map-marker-alt"></i> ${item.address || '<?= __('Laiha_Enderesu') ?>'}
                </div>
                <div class="info-detail">
                    <i class="fas fa-city"></i> ${item.munisipio || '-'}
                </div>
                <div class="info-detail">
                    <i class="fas fa-layer-group"></i> ${item.postu || ''} / ${item.suco || ''}
                </div>
                <div class="info-detail">
                    <i class="fas fa-info-circle"></i> <strong>${item.status}</strong>
                </div>
                
                <a href="detail.php?id=${item.id}" class="btn-view-detail">
                    <i class="fas fa-eye"></i> <?= __('Haree_Detalle') ?>
                </a>

                <a href="https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(item.title)}&destination_place_id=&travelmode=driving" target="_blank" class="btn-direction">
                    <i class="fas fa-directions"></i> <?= __('Hetan_Direisaun') ?>
                </a>
            `;

                var marker = L.marker([item.lat, item.lng], {
                        icon: pntlIcon
                    })
                    .addTo(map)
                    .bindPopup(popupContent, {
                        minWidth: 250
                    });

                // Loke popup bainhira mouse hover (la presija klik)
                marker.on('mouseover', function(e) {
                    this.openPopup();
                });

                bounds.push([item.lat, item.lng]);
            });

            // Ajusta mapa atu hatudu marker hotu
            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }
        }

        // Run map setup when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();

            // Navbar Scroll Effect
            const navbar = document.getElementById('mainNavbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });

            // Taka automatikamente menu mobile wainhira klik
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            const menuToggle = document.getElementById('navbarCollapse');
            if (typeof bootstrap !== 'undefined') {
                const bsCollapse = new bootstrap.Collapse(menuToggle, {
                    toggle: false
                });
                navLinks.forEach((link) => {
                    link.addEventListener('click', () => {
                        if (menuToggle.classList.contains('show')) {
                            bsCollapse.hide();
                        }
                    });
                });
            }

            // Gallery Item Toggle ba hotu-hotu (Mobile & Desktop)
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Se klik iha butaun detail, husik nia la'o ba link
                    if (e.target.closest('.btn')) {
                        return; // Permite link ba detail.php
                    }

                    // Se la'ós klik iha butaun, toggle de'it textu
                    e.stopPropagation();
                    const isActive = this.classList.contains('active');

                    // Taka hotu uluk
                    galleryItems.forEach(i => i.classList.remove('active'));

                    // Foin loke ida ne'e se nia seidauk loke
                    if (!isActive) {
                        this.classList.add('active');
                    }
                });
            });

            // Taka gallery overlay se klik iha li'ur
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.gallery-item')) {
                    galleryItems.forEach(item => item.classList.remove('active'));
                }
            });
            // Contact Form AJAX
            const contactForm = document.getElementById('contactForm');
            const formStatus = document.getElementById('formStatus');

            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formStatus.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Haruka hela...';

                    fetch('processa_mensajen', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                formStatus.innerHTML = `<div class="alert alert-success border-0 shadow-sm rounded-3">
                                <i class="fas fa-check-circle me-2"></i> ${data.message}
                            </div>`;
                                contactForm.reset();
                            } else {
                                formStatus.innerHTML = `<div class="alert alert-danger border-0 shadow-sm rounded-3">
                                <i class="fas fa-exclamation-circle me-2"></i> ${data.message}
                            </div>`;
                            }
                        })
                        .catch(error => {
                            formStatus.innerHTML = '<div class="alert alert-danger border-0 shadow-sm rounded-3">Erro iha koneksaun!</div>';
                        });
                });
            }
        });
    </script>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/67075717580" target="_blank" class="whatsapp-float" title="Konta ami liuhusi WhatsApp" id="whatsappBtn">
        <i class="fab fa-whatsapp my-float"></i>
    </a>

    <style>
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            left: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: grab;
            /* Indicate draggable */
            /* Removed transition for transform/position to allow smooth dragging */
            transition: background-color 0.3s ease-in-out;
            user-select: none;
        }

        .whatsapp-float:active {
            cursor: grabbing;
        }

        .whatsapp-float:hover {
            background-color: #128C7E;
            color: #FFF;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        /* Adjust position for smaller screens */
        @media screen and (max-width: 767px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                font-size: 26px;
            }
        }
    </style>

    <script>
        // Make the WhatsApp button draggable
        const waBtn = document.getElementById('whatsappBtn');
        let isDragging = false;
        let wasDragged = false;
        let startX, startY, initialX, initialY;

        function startDrag(e) {
            isDragging = true;
            wasDragged = false;
            // Get touch or mouse coordinates
            startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
            startY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;

            // Get initial position
            const rect = waBtn.getBoundingClientRect();
            initialX = rect.left;
            initialY = rect.top;

            // Prevent default drag behavior
            if (e.type === 'mousedown') {
                e.preventDefault();
            }
        }

        function doDrag(e) {
            if (!isDragging) return;
            wasDragged = true;

            const currentX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
            const currentY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;

            const dx = currentX - startX;
            const dy = currentY - startY;

            let newX = initialX + dx;
            let newY = initialY + dy;

            // Keep within window bounds
            const maxX = window.innerWidth - waBtn.offsetWidth;
            const maxY = window.innerHeight - waBtn.offsetHeight;

            newX = Math.max(0, Math.min(newX, maxX));
            newY = Math.max(0, Math.min(newY, maxY));

            // Override right/bottom CSS with left/top for dragging
            waBtn.style.bottom = 'auto';
            waBtn.style.right = 'auto';
            waBtn.style.left = newX + 'px';
            waBtn.style.top = newY + 'px';
        }

        function stopDrag() {
            isDragging = false;
        }

        // Prevent opening link if dragging occurred
        waBtn.addEventListener('click', function(e) {
            if (wasDragged) {
                e.preventDefault();
            }
        });

        // Mouse events
        waBtn.addEventListener('mousedown', startDrag);
        document.addEventListener('mousemove', doDrag);
        document.addEventListener('mouseup', stopDrag);

        // Touch events
        waBtn.addEventListener('touchstart', startDrag, {
            passive: false
        });
        document.addEventListener('touchmove', doDrag, {
            passive: false
        });
        document.addEventListener('touchend', stopDrag);
    </script>

    <!-- Chatbot Script -->
    <script src="chatbot.js"></script>
</body>

</html>