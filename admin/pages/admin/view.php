
<!-- Table Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h3 class="mb-4"><?= __('Tabela_Admin') ?></h3>

                <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'administrator'): ?>
                <a href="?pntl=admin/add" class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> <?= __('Rejistu_Admin') ?>
                </a>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('No') ?></th>
                                <th scope="col"><?= __('Naran_Admin') ?></th>
                                <th scope="col"><?= __('Username') ?></th>
                                <th scope="col"><?= __('Level') ?></th>
                                <th scope="col"><?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                            $nu = 1;
                            $query = mysqli_query($conn, "SELECT * FROM tb_admin");
                            while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <th scope="row"><?php echo $nu++; ?></th>
                                <td><?php echo htmlspecialchars($data['naran_konpletu']); ?></td>
                                <td><?php echo htmlspecialchars($data['username']); ?></td>
                                <td><span class="badge <?php echo $data['level'] == 'administrator' ? 'bg-primary' : 'bg-info'; ?>"><?php echo ucfirst($data['level']); ?></span></td>
                                 <td>
                                    <a href="?pntl=admin/view_detail&id=<?php echo $data['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> <?= __('View') ?>
                                    </a>
                                    <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'administrator'): ?>
                                    <a href="?pntl=admin/edit&id=<?php echo $data['id']; ?>" class="btn btn-sm btn-warning action-btn">
                                        <i class="fas fa-pen"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                    </a>
                                    <a href="?pntl=admin/delete&id=<?php echo $data['id']; ?>" class="btn btn-sm btn-danger action-btn delete-confirm" data-message="<?= __('Konfirma_Hamos') ?> <?php echo htmlspecialchars($data['naran_konpletu']); ?>?">
                                         <i class="fas fa-trash"></i><span class="btn-label"> <?= __('Hamos') ?></span>
                                     </a>
                                     <?php endif; ?>
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
<!-- Table End -->
