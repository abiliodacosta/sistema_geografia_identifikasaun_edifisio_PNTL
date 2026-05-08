
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h3 class="mb-4"><?= __('Tabela_Munisipiu') ?></h3>

                <!-- Tabela start -->
                <a href="?pntl=munisipio/add">
                    <button class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i> <?= __('Rejistu_Munisipiu_Foun') ?>
                    </button>
                </a>
                
                <div class="table-responsive">
                    <table class="table text-center table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('No') ?></th>
                                <th scope="col"><?= __('Naran_Munisipiu') ?></th>
                                <th scope="col"><?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nu = 1;
                            $query = mysqli_query($conn, "SELECT * FROM tb_munisipio ORDER BY naran_munisipio ASC");
                            while ($data = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><?php echo $nu++; ?></td>
                                <td><?php echo htmlspecialchars($data['naran_munisipio']); ?></td>
                                <td>
                                    <a href="?pntl=munisipio/edit&id=<?php echo $data['id_munisipio']; ?>" class="btn btn-sm btn-warning action-btn">
                                        <i class="fas fa-pen"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                    </a>
                                    <a href="?pntl=munisipio/delete&id=<?php echo $data['id_munisipio']; ?>" class="btn btn-sm btn-danger action-btn delete-confirm" data-message="<?= __('Konfirma_Hamos') ?> <?php echo htmlspecialchars($data['naran_munisipio']); ?>?">
                                         <i class="fas fa-trash"></i><span class="btn-label"> <?= __('Hamos') ?></span>
                                     </a>
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
