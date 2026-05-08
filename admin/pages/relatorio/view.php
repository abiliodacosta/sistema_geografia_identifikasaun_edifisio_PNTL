<?php
require_once 'auth.php';
checkLogin();

// Statistics Queries
$stats = [
    'Munisipiu'     => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_munisipio"))['total'],
    'Postu'         => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_postu_administrativu"))['total'],
    'Suku'          => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_suco"))['total'],
    'Tipo_Edifisio' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_tipo_edifisio"))['total'],
    'Edifisio'      => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_edifisio_pntl"))['total'],
    'Ajenda'        => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_ajenda"))['total'],
    'Mensajen'      => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_mensajen"))['total'],
    'Admin'         => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_admin"))['total'],
];

// Icons mapping
$icons = [
    'Munisipiu'     => 'fa-map',
    'Postu'         => 'fa-map-marked-alt',
    'Suku'          => 'fa-map-pin',
    'Tipo_Edifisio' => 'fa-building',
    'Edifisio'      => 'fa-hotel',
    'Ajenda'        => 'fa-calendar-check',
    'Mensajen'      => 'fa-envelope',
    'Admin'         => 'fa-user-shield',
];
?>

<div class="container-fluid pt-4 px-4" id="report-container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="text-white fw-bold mb-0">
            <i class="fa fa-chart-bar text-primary me-2"></i> <?= __('Relatorio') ?>
        </h4>
        <div class="d-flex gap-2">
            <a href="?pntl=relatorio/print&type=summary" class="btn btn-primary btn-sm rounded-pill px-4">
                <i class="fa fa-print me-2"></i> <?= __('Print') ?>
            </a>
        </div>
    </div>
    
    <!-- Rest of the content -->


    <!-- Summary Cards (Small & Single Line - Clickable) -->
    <div class="row g-2 mb-4 row-cols-2 row-cols-md-4 row-cols-lg-8">
        <?php foreach($stats as $label => $count): 
            // Map label to type parameter for link
            $link_type = strtolower($label);
            if($label == 'Munisipiu') $link_type = 'munisipio';
            if($label == 'Postu') $link_type = 'postu';
            if($label == 'Suku') $link_type = 'suku';
            if($label == 'Edifisio') $link_type = 'edifisio';
        ?>
        <div class="col">
            <a href="?pntl=relatorio/details&type=<?= $link_type ?>" class="text-decoration-none h-100 d-block card-hover">
                <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-2 shadow-sm border-bottom border-white border-2 h-100 stat-card" style="transition: all 0.3s ease;">
                    <i class="fa <?= $icons[$label] ?> fa-2x text-primary"></i>
                    <div class="ms-2 text-end">
                        <p class="mb-0 text-white-50" style="font-size: 0.75rem;"><?= __($label) ?></p>
                        <h5 class="mb-0 text-white fw-bold"><?= $count ?></h5>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <style>
    .card-hover:hover .stat-card {
        background-color: rgba(255, 255, 255, 0.1) !important;
        transform: translateY(-5px);
        border-bottom-color: #FFCC00 !important;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
    }
    .card-hover:hover i {
        color: #FFCC00 !important;
        transform: scale(1.1);
        transition: all 0.3s ease;
    }
    </style>

    <!-- Data Tables Summary -->
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="mb-0 text-white"><?= __('Dadus_Sistema') ?></h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-white">
                                <th scope="col">No</th>
                                <th scope="col"><?= __('Kategoria') ?></th>
                                <th scope="col" class="text-center"><?= __('Totál_Dadus') ?></th>
                                <th scope="col" class="text-center"><?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            foreach($stats as $label => $count): ?>
                            <tr>
                                <td class="text-white-50"><?= $i++ ?></td>
                                <td class="text-white"><?= __($label) ?></td>
                                <td class="text-center text-white fw-bold"><?= $count ?></td>
                                <td class="text-center">
                                    <?php
                                        // Map label to type parameter
                                        $type = strtolower($label);
                                        if($label == 'Munisipiu') $type = 'munisipio';
                                        if($label == 'Postu') $type = 'postu';
                                        if($label == 'Suku') $type = 'suku';
                                        if($label == 'Edifisio') $type = 'edifisio';
                                    ?>
                                    <a class="btn btn-sm btn-outline-primary rounded-pill px-3" href="?pntl=relatorio/details&type=<?= $type ?>">
                                        <i class="fa fa-file-alt me-1"></i> <?= __('Haree_Relatorio') ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.stat-card:hover {
    transform: translateY(-5px) scale(1.02);
    background: rgba(255, 204, 0, 0.05) !important;
    border-color: var(--pntl-gold) !important;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3) !important;
}

.stat-card i {
    transition: all 0.3s ease;
}

.stat-card:hover i {
    transform: scale(1.1);
    color: #fff !important;
}

@media print {
    .sidebar, .navbar, .btn-primary, .footer-push, .back-to-top {
        display: none !important;
    }
    .content {
        margin-left: 0 !important;
        width: 100% !important;
        background-color: #fff !important;
    }
    .bg-secondary {
        background-color: #fff !important;
        color: #000 !important;
        border: 1px solid #ddd !important;
    }
    .text-white, .text-white-50 {
        color: #000 !important;
    }
    .table thead tr th {
        color: #000 !important;
        border-color: #000 !important;
    }
}
</style>
