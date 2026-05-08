<?php
require_once 'auth.php';
checkLogin();

$type = isset($_GET['type']) ? $_GET['type'] : 'munisipio';
$title = "Relatòriu " . ucfirst($type);
$data = [];
$columns = [];

// Configure columns and query based on type
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
        $columns = ['No', __('Foto'), __('Edifisio'), __('Tipo_Edifisio'), __('Diskrisaun'), __('Munisipiu'), __('Enderesu'), 'Status', 'Mapa'];
        $res = mysqli_query($conn, "SELECT e.*, t.naran_tipu as tipo_edifisio, m.naran_munisipio FROM tb_edifisio_pntl e 
                  JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo 
                  JOIN tb_suco s ON e.id_suco = s.id_suco
                  JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
                  JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio");
        while ($row = mysqli_fetch_assoc($res)) {
            $foto = !empty($row['foto']) ? '<img src="uploads/edifisio/'.$row['foto'].'" style="width:50px; height:35px; object-fit:cover;" class="rounded">' : '<i class="fa fa-image text-white-50"></i>';
            $mapa = '<a href="https://www.google.com/maps?q='.$row['latitude'].','.$row['longtitude'].'" target="_blank" class="btn btn-xs btn-outline-info p-0 px-2" style="font-size:0.7rem;"><i class="fa fa-map-marker-alt"></i> Google Maps</a>';
            
            $data[] = [
                $row['id_edifisio'], 
                $foto,
                $row['naran_edifisio'], 
                $row['tipo_edifisio'], 
                $row['diskrisaun'], 
                $row['naran_munisipio'], 
                $row['enderesu'], 
                $row['status'],
                $mapa
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
    default:
        die("Invalid type");
}
?>

<div class="container-fluid pt-4 px-4" id="report-details">
    <div class="bg-secondary rounded p-4">
        <!-- Header & Controls -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <h4 class="text-white mb-0">
                <a href="?pntl=relatorio/view" class="btn btn-sm btn-outline-light rounded-circle me-2"><i class="fa fa-arrow-left"></i></a>
                <?= __('Relatorio') ?>: <span class="text-primary"><?= $title ?></span>
            </h4>
            <div class="d-flex gap-2">
                <button onclick="exportToExcel('reportTable', '<?= $type ?>_report')" class="btn btn-success btn-sm rounded-pill px-3">
                    <i class="fa fa-file-excel me-2"></i> Excel
                </button>
                <a href="?pntl=relatorio/print&type=<?= $type ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fa fa-print me-2"></i> <?= __('Print') ?>
                </a>
            </div>
        </div>

        <!-- Report Table -->
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0" id="reportTable">
                <thead>
                    <tr class="text-white">
                        <?php foreach($columns as $col): ?>
                            <th scope="col"><?= $col ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data)): ?>
                        <tr>
                            <td colspan="<?= count($columns) ?>" class="text-center text-white-50 py-4">Laiha dadus atu hatudu.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data as $index => $row): ?>
                        <tr>
                            <td class="text-white-50"><?= $index + 1 ?></td>
                            <?php for($k = 1; $k < count($row); $k++): ?>
                                <td class="text-white">
                                    <?php 
                                        // If it contains HTML tags, don't escape
                                        if (strpos($row[$k], '<') !== false && strpos($row[$k], '>') !== false) {
                                            echo $row[$k];
                                        } else {
                                            echo htmlspecialchars($row[$k]);
                                        }
                                    ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Excel Export Script -->
<script>
function exportToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = filename?filename+'.xls':'excel_data.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}
</script>
