<?php
date_default_timezone_set('Asia/Dili');
require_once 'auth.php';
checkLogin();

// Get logged-in user's full name and level for signature
$user_id = $_SESSION['id'];
$user_res = mysqli_query($conn, "SELECT naran_konpletu, level FROM tb_admin WHERE id = '$user_id'");
$user_row = mysqli_fetch_assoc($user_res);
$naran_konpletu = $user_row ? $user_row['naran_konpletu'] : '_________________________';
$user_level = $user_row ? ucfirst($user_row['level']) : 'Staff';

$type = isset($_GET['type']) ? $_GET['type'] : 'munisipio';
$title = "Relatòriu " . ucfirst($type);
$data = [];
$columns = [];

// Reuse the same logic as details.php
switch ($type) {
    case 'munisipio':
        $title = __('Munisipiu');
        $columns = ['No', __('Naran_Munisipiu')];
        $res = mysqli_query($conn, "SELECT * FROM tb_munisipio");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id_munisipio'], $row['naran_munisipio']];
        }
        break;
    case 'postu':
        $title = __('Postu');
        $columns = ['No', __('Postu'), __('Munisipiu')];
        $res = mysqli_query($conn, "SELECT p.*, m.naran_munisipio FROM tb_postu_administrativu p JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id_posto'], $row['naran_posto'], $row['naran_munisipio']];
        }
        break;
    case 'suku':
        $title = __('Suku');
        $columns = ['No', __('Suku'), __('Postu')];
        $res = mysqli_query($conn, "SELECT s.*, p.naran_posto FROM tb_suco s JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id_suco'], $row['naran_suco'], $row['naran_posto']];
        }
        break;
    case 'edifisio':
        $title = __('Edifisio');
        $columns = ['No', __('Foto'), __('Edifisio'), __('Tipo_Edifisio'), __('Diskrisaun'), __('Munisipiu'), __('Enderesu'), 'Status'];
        $res = mysqli_query($conn, "SELECT e.*, t.naran_tipu as tipo_edifisio, m.naran_munisipio FROM tb_edifisio_pntl e 
                  LEFT JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo 
                  LEFT JOIN tb_suco s ON e.id_suco = s.id_suco
                  LEFT JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
                  LEFT JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio");
        while ($row = mysqli_fetch_assoc($res)) {
            $foto = !empty($row['foto']) ? '<img src="uploads/edifisio/'.$row['foto'].'" style="width:40px; height:40px; object-fit:cover; border: 1px solid #000;" class="rounded">' : '-';
            $data[] = [
                $row['id_edifisio'],
                $foto,
                $row['naran_edifisio'],
                $row['tipo_edifisio'],
                $row['diskrisaun'],
                $row['naran_munisipio'],
                $row['enderesu'],
                $row['status']
            ];
        }
        break;
    case 'tipo_edifisio':
        $title = __('Tipo_Edifisio');
        $columns = ['No', __('Tipo_Edifisio'), __('Diskrisaun')];
        $res = mysqli_query($conn, "SELECT * FROM tb_tipo_edifisio");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id_tipo'], $row['naran_tipu'], $row['diskrisaun']];
        }
        break;
    case 'ajenda':
        $title = __('Jestaun_Ajenda');
        $columns = ['No', __('Titulu'), __('Data'), __('Oras'), __('Diskrisaun')];
        $res = mysqli_query($conn, "SELECT * FROM tb_ajenda ORDER BY data_ajenda DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id_ajenda'], $row['titulu'], $row['data_ajenda'], $row['ora_ajenda'], $row['diskrisaun']];
        }
        break;
    case 'mensajen':
        $title = __('Mensajen');
        $columns = ['No', __('Naran'), __('Email'), __('Asuntu'), __('Data')];
        $res = mysqli_query($conn, "SELECT * FROM tb_mensajen ORDER BY data_manda DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id_mensajen'], $row['naran'], $row['email'], $row['asuntu'], $row['data_manda']];
        }
        break;
    case 'admin':
        $title = "Admin Users";
        $columns = ['No', 'Naran Kompletu', 'Username', 'Level'];
        $res = mysqli_query($conn, "SELECT * FROM tb_admin");
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = [$row['id'], $row['naran_konpletu'], $row['username'], $row['level']];
        }
        break;
    case 'summary':
        $title = "Sumáriu Jerál";
        $columns = ['No', __('Kategoria'), __('Totál_Dadus')];
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
        $i = 1;
        foreach ($stats as $label => $count) {
            $data[] = [$i++, __($label), $count];
        }
        break;
    default:
        die("Invalid type");
}
?>

<!-- Print Controls (Hidden during print) -->
<div class="no-print bg-dark p-3 mb-4 d-flex justify-content-between align-items-center shadow-sm" style="position: sticky; top: 0; z-index: 9999;">
    <?php
    $back_url = ($type == 'summary') ? "?pntl=relatorio/view" : "?pntl=relatorio/details&type=" . $type;
    ?>
    <button onclick="window.location.href='<?= $back_url ?>'" class="btn btn-outline-light rounded-pill px-4">
        <i class="fa fa-arrow-left me-2"></i> <?= __('Fila') ?>
    </button>
    <div class="text-white fw-bold">PRINT PREVIEW</div>
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
        <i class="fa fa-print me-2"></i> <?= __('Print') ?>
    </button>
</div>

<div class="print-area bg-white p-5 text-dark" style="min-height: 297mm; width: 210mm; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.1); color: #000 !important;">
    <!-- Letterhead (Kop Leten) -->
    <div class="text-center mb-2" style="border-bottom: 4px solid #000; padding-bottom: 10px;">
        <div class="row align-items-center">
            <div class="col-2 text-start">
                <img src="img/pntl.png" style="width: 125px; height: auto;">
            </div>
            <div class="col-10 text-center">
                <h5 class="mb-0 fw-bold" style="font-family: 'Times New Roman', Times, serif; letter-spacing: 1px; font-size: 14pt; color: #000 !important; text-transform: uppercase; white-space: nowrap;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h5>
                <h2 class="mb-0 fw-bold" style="font-family: 'Times New Roman', Times, serif; font-size: 18pt; color: #000 !important; margin-top: 5px; white-space: nowrap;">POLÍCIA NASIONÁL TIMOR-LESTE</h2>
                <h4 class="mb-1 fw-bold" style="font-family: 'Times New Roman', Times, serif; font-size: 14pt; color: #000 !important; white-space: nowrap;">KOMANDU JERÁL</h4>
                <div style="margin-top: 5px; font-size: 10.5pt; line-height: 1.2; color: #000 !important;">
                    <p class="mb-0">Avenida Mártires da Pátria, Caicoli, Dili, Timor-Leste</p>
                    <p class="mb-0">Website: <u>www.pntl.tl</u> | Email: <u>info@pntl.tl</u></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Header (Date Right, Title Center) -->
    <div class="mb-3">
        <div class="text-end">
            <p class="mb-0 text-black fw-bold" style="color: #000 !important; font-size: 11pt;">Nuº______/<?= date('d/m/Y H:i') ?></p>
        </div>
        <div class="text-center mt-2">
            <h5 class="text-uppercase fw-bold text-black mb-0" style="text-decoration: underline; color: #000 !important; font-size: 14pt;"><?= __('Relatorio') ?> <?= $title ?></h5>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-bordered border-dark w-100 text-black">
            <thead style="background-color: #f2f2f2 !important;">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th class="text-center py-2 text-black fw-bold" style="color: #000 !important; border: 1px solid #000 !important; background-color: #e9ecef !important;"><?= $col ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $index => $row): ?>
                    <tr>
                        <td class="text-center text-black" style="color: #000 !important; border: 1px solid #000 !important;"><?= $index + 1 ?></td>
                        <?php for ($k = 1; $k < count($row); $k++): ?>
                            <td class="text-black" style="color: #000 !important; border: 1px solid #000 !important;"><?= $row[$k] ?></td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer / Signature Area -->
    <div class="row mt-5 pt-5 text-black">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-5 text-black" style="color: #000 !important;">Dili, <?= date('d') ?> <?= date('F') ?> <?= date('Y') ?></p>
            <br><br>
            <p class="fw-bold mb-0 text-black" style="color: #000 !important; text-decoration: underline;">( <?= $naran_konpletu ?> )</p>
            <p class="text-black" style="color: #000 !important;"><?= $user_level ?> PNTL</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: auto;
            margin: 0mm;
        }

        /* Force pure white background and remove UI artifacts */
        html, body {
            background-color: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Hide EVERYTHING by default */
        body * {
            visibility: hidden !important;
        }

        /* Show ONLY the print-area and its children */
        .print-area,
        .print-area * {
            visibility: visible !important;
        }

        /* Position the print-area at the top-left of the page and add internal margin */
        .print-area {
            position: absolute !important;
            left: 10mm !important;
            top: 10mm !important;
            width: calc(100% - 20mm) !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            background-color: #fff !important;
        }

        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important; /* Allow columns to fit content */
        }

        .table thead th {
            background-color: #fff !important; /* Pure white header */
            color: #000 !important;
            border: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
            font-weight: bold !important;
        }

        .table td {
            color: #000 !important;
            border: 1px solid #000 !important;
            background-color: transparent !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }

        /* Force black text for everything in print-area */
        .print-area, .print-area p, .print-area h1, .print-area h2, .print-area h3, .print-area h4, .print-area h5, .print-area span {
            color: #000 !important;
        }
    }

    /* Screen only styles */
    @media screen {
        body {
            background-color: #333 !important;
        }

        .print-area {
            margin-top: 20px;
            margin-bottom: 50px;
        }
    }

    table th,
    table td {
        padding: 8px !important;
        font-size: 11pt !important;
        color: #000 !important;
    }
</style>

<?php if (isset($_GET['download']) && $_GET['download'] == 'pdf'): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    window.onload = function() {
        const element = document.querySelector('.print-area');
        const opt = {
            margin:       [10, 10, 10, 10],
            filename:     'PNTL_Report_<?= $type ?>_<?= date('Ymd') ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true,
                logging: false,
                letterRendering: true
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Generate PDF
        html2pdf().set(opt).from(element).save();
    }
</script>
<?php else: ?>
<script>
    window.onload = function() {
        window.print();
    }
</script>
<?php endif; ?>