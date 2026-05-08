
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h3 class="mb-4"><?= __('Tabela_Tipu_Edifisio') ?></h3>

                <a href="?pntl=tipo_edifisio/add">
                    <button class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i> <?= __('Rejistu_Tipu_Foun') ?>
                    </button>
                </a>
                
                <div class="table-responsive">
                    <table class="table text-center table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('No') ?></th>
                                <th scope="col"><?= __('Naran_Tipu') ?></th>
                                <th scope="col"><?= __('Diskrisaun') ?></th>
                                <th scope="col"><?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nu = 1;
                            $query = mysqli_query($conn, "SELECT * FROM tb_tipo_edifisio ORDER BY naran_tipu ASC");
                            while ($data = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><?php echo $nu++; ?></td>
                                <td><?php echo htmlspecialchars($data['naran_tipu']); ?></td>
                                <td><?php echo htmlspecialchars($data['diskrisaun']); ?></td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="?pntl=tipo_edifisio/edit&id=<?php echo $data['id_tipo']; ?>" 
                                           class="btn btn-sm btn-warning action-btn" 
                                           data-bs-toggle="tooltip" title="<?= __('Edit') ?>">
                                            <i class="fas fa-pen"></i>
                                            <span class="btn-label"><?= __('Edit') ?></span>
                                        </a>
                                        <a href="?pntl=tipo_edifisio/delete&id=<?php echo $data['id_tipo']; ?>" 
                                           class="btn btn-sm btn-danger action-btn delete-confirm" 
                                           data-message="<?= __('Konfirma_Hamos') ?> <?php echo htmlspecialchars($data['naran_tipu']); ?>?"
                                           data-bs-toggle="tooltip" title="<?= __('Delete') ?>">
                                            <i class="fas fa-trash"></i>
                                            <span class="btn-label"><?= __('Delete') ?></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
