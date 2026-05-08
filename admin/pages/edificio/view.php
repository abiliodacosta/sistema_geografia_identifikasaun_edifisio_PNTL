
<?php
$q_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_edifisio_pntl");
$total_edifisio = mysqli_fetch_assoc($q_count)['total'];
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="widget-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:46px;height:46px;background:linear-gradient(135deg,#002366,#0044aa);">
                            <i class="fa fa-hotel" style="color:#FFCC00;font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white fw-bold"><?= __('Tabela_Edifisio') ?></h4>
                            <span class="badge bg-primary rounded-pill"><?= $total_edifisio ?> <?= __('Total_Edifisio') ?></span>
                        </div>
                    </div>
                    <a href="?pntl=edificio/add" class="btn btn-primary rounded-pill px-4" 
                       style="background:linear-gradient(135deg,#FFCC00,#ff9800);color:#002366;border:none;box-shadow:0 4px 15px rgba(255,204,0,0.3);">
                        <i class="fa fa-plus me-2"></i> <?= __('Rejistu_Edifisio_Foun') ?>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table text-center align-middle table-bordered table-hover mb-0">
                        <thead class="premium-header">
                            <tr>
                                <th scope="col" style="width: 50px;">No</th>
                                <th scope="col"><?= __('Foto') ?></th>
                                <th scope="col"><?= __('Naran_Edifisio') ?></th>
                                <th scope="col"><?= __('Tipu_Edifisio') ?></th>
                                <th scope="col"><?= __('Diskrisaun') ?></th>
                                <th scope="col"><?= __('Enderesu') ?></th>
                                <th scope="col"><?= __('Status') ?></th>
                                <th scope="col" style="width: 180px;"><?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $nu = 1;
                            $query = mysqli_query($conn, "SELECT e.*, s.naran_suco, t.naran_tipu 
                                                          FROM tb_edifisio_pntl e 
                                                          JOIN tb_suco s ON e.id_suco = s.id_suco 
                                                          JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo 
                                                          ORDER BY e.naran_edifisio ASC");
                            while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo $nu++; ?></span></td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <?php if (!empty($data['foto'])): ?>
                                            <img src="uploads/edifisio/<?php echo htmlspecialchars($data['foto']); ?>" 
                                                 alt="Foto" class="rounded shadow-sm border border-secondary"
                                                 style="width: 45px; height: 45px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                                <i class="fa fa-image text-white-50 small"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold text-white"><?php echo htmlspecialchars($data['naran_edifisio']); ?></div>
                                    <small class="text-white-50"><?php echo htmlspecialchars($data['naran_suco']); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-dark-50 border border-secondary text-white-50 px-3 py-2 rounded-pill">
                                        <?php echo htmlspecialchars($data['naran_tipu']); ?>
                                    </span>
                                </td>
                                <td class="text-start text-white-50 small" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($data['diskrisaun']); ?>
                                </td>
                                <td class="text-start text-white-50 small" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($data['enderesu']); ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($data['status'] == 'Uza hela') echo '<span class="badge bg-success rounded-pill px-3">'.__('Uza_Hela').'</span>';
                                    elseif ($data['status'] == 'Labele uza') echo '<span class="badge bg-danger rounded-pill px-3">'.__('Labele_Uza').'</span>';
                                    else echo '<span class="badge bg-warning text-dark rounded-pill px-3">'.__('Konstrusaun_Hela').'</span>';
                                    ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="?pntl=edificio/detail&id=<?php echo $data['id_edifisio']; ?>" 
                                           class="btn btn-sm btn-info action-btn" data-bs-toggle="tooltip" title="<?= __('Haree') ?>">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="?pntl=edificio/edit&id=<?php echo $data['id_edifisio']; ?>" 
                                           class="btn btn-sm btn-warning action-btn" data-bs-toggle="tooltip" title="<?= __('Edit') ?>">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                        <a href="?pntl=edificio/delete&id=<?php echo $data['id_edifisio']; ?>" 
                                           class="btn btn-sm btn-danger action-btn delete-confirm" 
                                           data-message="<?= __('Konfirma_Hamos') ?> <?php echo htmlspecialchars($data['naran_edifisio']); ?>?" 
                                           data-bs-toggle="tooltip" title="<?= __('Delete') ?>">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-dark-50 { background: rgba(0, 0, 0, 0.2); }
</style>
