<?php
ob_start();
require_once 'auth.php';
checkLogin();

// session_start();
require_once 'koneksaun.php';

// Handle language selection
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['tet', 'pt', 'en'])) {
        $_SESSION['lang'] = $lang;
    }
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tet';

$lang_file = "../lang/{$current_lang}.json";
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

$naran_user = isset($_SESSION['naran_konpletu']) ? $_SESSION['naran_konpletu'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest');

// Count Unread Messages
$unread_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_mensajen WHERE status = 'Unread'");
$unread_count = 0;
if ($unread_res) {
    $unread_row = mysqli_fetch_assoc($unread_res);
    $unread_count = $unread_row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Pagina Admin Systema Geografia PNTL</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/pntl.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary: #002366;
            /* Azul PNTL */
            --secondary: #0a1931;
            /* Dark Blue */
            --light: #adb5bd;
            --dark: #050d1a;
            /* Deepest Blue */
            --pntl-red: #CE1126;
            --pntl-gold: #FFCC00;
            --accent: #FFCC00;
        }

        .sidebar .navbar .navbar-brand h3 {
            color: var(--pntl-gold) !important;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .sidebar .navbar .nav-link {
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .sidebar .navbar .nav-link:hover {
            background: rgba(255, 204, 0, 0.1);
            color: var(--pntl-gold) !important;
            padding-left: 28px;
            border-left-color: var(--pntl-gold);
            box-shadow: inset 5px 0 15px rgba(255, 204, 0, 0.05);
        }

        .sidebar .navbar .nav-link i {
            transition: all 0.3s ease;
        }

        .sidebar .navbar .nav-link:hover i {
            transform: rotate(-10deg) scale(1.2);
            color: var(--pntl-gold);
        }

        .sidebar .navbar .nav-link.active {
            color: var(--pntl-gold) !important;
            background: rgba(255, 204, 0, 0.15);
            border-left-color: var(--pntl-gold);
            font-weight: 700;
        }

        .text-primary {
            color: var(--pntl-gold) !important;
        }

        .btn-primary {
            background-color: var(--pntl-gold) !important;
            border-color: var(--pntl-gold) !important;
            color: var(--primary) !important;
            font-weight: 700;
        }

        .btn-primary:hover {
            background-color: #fff !important;
            border-color: #fff !important;
            color: var(--primary) !important;
            transform: translateY(-2px);
        }

        .bg-secondary {
            background-color: var(--secondary) !important;
        }

        .content {
            background-color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .footer-push {
            margin-top: auto;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.open {
                margin-left: 0;
            }

            .content {
                width: 100% !important;
                margin-left: 0 !important;
            }

            .content.open {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }

        /* Sidebar Footer Fixed */
        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>


</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <script>
            // Fallback spinner removal
            setTimeout(function() {
                var spinner = document.getElementById('spinner');
                if (spinner && spinner.classList.contains('show')) {
                    spinner.classList.remove('show');
                }
            }, 2000);
        </script>


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="?pntl=home" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary d-flex align-items-center">
                        <img src="img/pntl.png" alt="Logo" style="height: 45px; margin-right: 10px;">
                        PNTL
                    </h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="?pntl=home" class="nav-item nav-link active"><i class="fa fa-home me-2"></i><?= __('Dashboard') ?></a>
                    <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'administrator'): ?>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users-cog me-2"></i><?= __('Admin_Maneja') ?></a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="?pntl=admin/view" class="dropdown-item"><i class="fa fa-user-shield me-2"></i><?= __('Dadus_Admin') ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'administrator'): ?>
                        <a href="?pntl=settings/smtp" class="nav-item nav-link" style="font-size:0.88rem;">
                            <i class="fa fa-cog me-2"></i>Email SMTP
                        </a>
                    <?php endif; ?>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-layer-group me-2"></i><?= __('Pages') ?></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="?pntl=munisipio/view" class="dropdown-item"><i class="fa fa-map me-2"></i><?= __('Munisipiu') ?></a>
                            <a href="?pntl=postu_administrativu/view" class="dropdown-item"><i class="fa fa-map-marked-alt me-2"></i><?= __('Postu') ?></a>
                            <a href="?pntl=suku/view" class="dropdown-item"><i class="fa fa-map-pin me-2"></i><?= __('Suku') ?></a>
                            <a href="?pntl=tipo_edifisio/view" class="dropdown-item"><i class="fa fa-building me-2"></i><?= __('Tipo_Edifisio') ?></a>
                            <a href="?pntl=edificio/view" class="dropdown-item"><i class="fa fa-hotel me-2"></i><?= __('Edifisio') ?></a>
                            <a href="?pntl=ajenda/view" class="dropdown-item"><i class="fa fa-calendar-check me-2"></i><?= __('Jestaun_Ajenda') ?></a>
                            <a href="?pntl=lingua/view" class="dropdown-item"><i class="fa fa-language me-2"></i><?= __('Jestaun_Lingua') ?></a>
                            <a href="?pntl=mensajen/view" class="dropdown-item"><i class="fa fa-envelope me-2"></i><?= __('Mensajen') ?></a>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['level']) && ($_SESSION['level'] == 'administrator' || $_SESSION['level'] == 'admin')): ?>
                        <a href="?pntl=relatorio/view" class="nav-item nav-link" style="font-size:0.88rem;">
                            <i class="fa fa-chart-bar me-2"></i><?= __('Relatorio') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="home.php" class="navbar-brand d-flex d-lg-none me-4">
                    <img src="img/pntl.png" alt="Logo" style="height: 30px;">
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control bg-dark border-0" type="search" placeholder="<?= __('Search') ?>">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <!-- Message Notifications -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2 position-relative">
                                <?php if ($unread_count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25em 0.5em;">
                                        <?= $unread_count ?>
                                    </span>
                                <?php endif; ?>
                            </i>
                            <span class="d-none d-lg-inline-flex"><?= __('Mensajen') ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0 shadow">
                            <?php
                            $msg_res = mysqli_query($conn, "SELECT * FROM tb_mensajen WHERE status = 'Unread' ORDER BY data_manda DESC LIMIT 5");
                            if (mysqli_num_rows($msg_res) > 0):
                                while ($msg = mysqli_fetch_assoc($msg_res)):
                            ?>
                                    <a href="?pntl=mensajen/view&read=<?= $msg['id_mensajen'] ?>" class="dropdown-item">
                                        <div class="d-flex align-items-center">
                                            <div class="ms-2">
                                                <h6 class="fw-normal mb-0 text-white"><?= htmlspecialchars($msg['naran']) ?> haruka mensajen</h6>
                                                <small class="text-white-50"><?= date('d M, H:i', strtotime($msg['data_manda'])) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                    <hr class="dropdown-divider bg-light">
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="dropdown-item text-center text-white-50 small py-3"><?= __('Laiha_Mensajen_Foun') ?></div>
                            <?php endif; ?>
                            <a href="?pntl=mensajen/view" class="dropdown-item text-center small text-primary"><?= __('Haree_Hotu_Mensajen') ?></a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center me-lg-2" style="width: 35px; height: 35px;">
                                <i class="fa fa-user text-white" style="font-size: 14px;"></i>
                            </div>
                            <span class="d-none d-lg-inline-flex"><?php echo $_SESSION['username']; ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="?pntl=profile/view" class="dropdown-item"><?= __('My_Profile') ?></a>
                            <a href="logout.php" class="dropdown-item"><?= __('Logout') ?></a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->




            <!-- Koneksaun Page -->
            <?php
            if (isset($_GET['pntl'])) {
                $pntl = $_GET['pntl'];

                // Access Control: Only Administrator can access admin/ and settings/ pages
                $current_level = isset($_SESSION['level']) ? $_SESSION['level'] : 'admin';

                if ((strpos($pntl, 'admin/') === 0 || strpos($pntl, 'settings/') === 0) && $current_level != 'administrator') {
                    echo "
                        <div class='container-fluid pt-4 px-4'>
                            <div class='bg-secondary rounded p-4 text-center'>
                                <i class='fas fa-exclamation-triangle text-danger mb-3' style='font-size: 3rem;'></i>
                                <h3 class='text-white'>" . __('Asesu_Limitadu') . "</h3>
                                <p class='text-white-50'>" . __('Ita_La_Iha_Asesu_Ba_Pajina_Ne') . "</p>
                                <a href='?pntl=home' class='btn btn-primary mt-3'><i class='fas fa-home me-2'></i>" . __('Fila_Ba_Dashboard') . "</a>
                            </div>
                        </div>";
                } else {
                    require_once 'pages/' . $pntl . '.php';
                }
            } else {
                require_once 'pages/home.php';
            }
            ?>


            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4 footer-push">
                <div class="bg-secondary rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">PNTL Admin</a>,GIS.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end text-white-50 small">
                            Dezenvolve Husi <b>Abilio</b>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- html2pdf.js for direct PDF download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- SweetAlert Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            <?php if (isset($_SESSION['success'])): ?>
                Swal.fire({
                    title: '<?= __('Susesu') ?>',
                    text: '<?php echo $_SESSION['success']; ?>',
                    icon: 'success',
                    confirmButtonColor: '#002366',
                    timer: 3000,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    title: '<?= __('Erro') ?>',
                    text: '<?php echo $_SESSION['error']; ?>',
                    icon: 'error',
                    confirmButtonColor: '#CE1126',
                    showClass: {
                        popup: 'animate__animated animate__shakeX'
                    }
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['warning'])): ?>
                Swal.fire({
                    title: '<?= __('Atensaun') ?>',
                    text: '<?php echo $_SESSION['warning']; ?>',
                    icon: 'warning',
                    confirmButtonColor: '#FFCC00',
                    confirmButtonTextColor: '#000'
                });
                <?php unset($_SESSION['warning']); ?>
            <?php endif; ?>

            // Sidebar Active State Persistence
            const urlParams = new URLSearchParams(window.location.search);
            const pntl = urlParams.get('pntl');

            if (pntl) {
                // Remove active from home if we are on a subpage
                document.querySelector('.nav-item.nav-link.active')?.classList.remove('active');

                // Find the link that matches the pntl parameter
                const activeLink = document.querySelector(`.dropdown-item[href*="${pntl}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                    const parentDropdown = activeLink.closest('.dropdown');
                    if (parentDropdown) {
                        const toggle = parentDropdown.querySelector('.dropdown-toggle');
                        const menu = parentDropdown.querySelector('.dropdown-menu');
                        toggle.classList.add('active');
                        menu.classList.add('show');
                    }
                } else {
                    // Check top-level links
                    const topLink = document.querySelector(`.nav-link[href*="${pntl}"]`);
                    if (topLink) topLink.classList.add('active');
                }
            }

            // Global Delete Confirmation
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-confirm') || e.target.closest('.delete-confirm')) {
                    e.preventDefault();
                    const target = e.target.classList.contains('delete-confirm') ? e.target : e.target.closest('.delete-confirm');
                    const url = target.getAttribute('href');
                    const message = target.getAttribute('data-message') || "<?= __('Konfirma_Hamos_Baze') ?>";

                    Swal.fire({
                        title: '<?= __('Ita_Siguru') ?>',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#CE1126',
                        cancelButtonColor: '#6c7293',
                        confirmButtonText: 'OK',
                        cancelButtonText: '<?= __('Kansela') ?>'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                }
            });
        });

        // Global PDF Download Function
        function downloadPDF(elementId, filename) {
            const element = document.getElementById(elementId);
            
            // Create a clone to avoid messing up the UI
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     filename + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { 
                    scale: 3, 
                    useCORS: true, 
                    backgroundColor: '#ffffff',
                    scrollY: 0,
                    windowHeight: element.scrollHeight + 500
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
            };

            // Enhanced temporary styling for PDF
            const buttons = element.querySelectorAll('.btn, .breadcrumb, .sidebar-toggler, .fa-arrow-left, .back-to-top');
            const originalColors = [];
            const textElements = element.querySelectorAll('h4, h5, p, span, td, th');

            // Hide UI elements
            buttons.forEach(btn => btn.style.setProperty('display', 'none', 'important'));
            
            // Force text color to black and background to white for the entire element
            const originalBg = element.style.background;
            const originalShadow = element.style.boxShadow;
            element.style.background = '#ffffff';
            element.style.boxShadow = 'none';

            textElements.forEach(el => {
                originalColors.push({el: el, color: el.style.color});
                el.style.setProperty('color', '#000000', 'important');
            });

            // Handle tables specifically to prevent clipping
            const tables = element.querySelectorAll('table');
            tables.forEach(table => {
                table.style.setProperty('width', '100%', 'important');
                table.style.setProperty('background-color', '#ffffff', 'important');
                table.style.setProperty('color', '#000000', 'important');
            });

            // Execute PDF generation
            html2pdf().set(opt).from(element).save().then(() => {
                // Restore original state
                buttons.forEach(btn => btn.style.display = '');
                element.style.background = originalBg;
                element.style.boxShadow = originalShadow;
                originalColors.forEach(item => {
                    item.el.style.color = item.color;
                });
                tables.forEach(table => {
                    table.style.width = '';
                    table.style.backgroundColor = '';
                    table.style.color = '';
                });
            });
        }
    </script>
</body>

</html>