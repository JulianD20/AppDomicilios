<?php
/**
 * Vista personalizada de paginación con Bootstrap 5
 * Ubicación: app/Views/Pager/bootstrap_full.php
 */

$pager->setSurroundCount(2);
?>

<?php if ($pager->hasPages()) : ?>
    <nav aria-label="<?= lang('Pager.pageNavigation') ?>">
        <ul class="pagination justify-content-center pagination-rounded shadow-sm">

            <!-- Botón "Anterior" -->
            <?php if ($pager->hasPreviousPage()) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="<?= lang('Pager.previous') ?>">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php else : ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            <?php endif; ?>

            <!-- Números de página -->
            <?php foreach ($pager->links() as $link): ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <!-- Botón "Siguiente" -->
            <?php if ($pager->hasNextPage()) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="<?= lang('Pager.next') ?>">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php else : ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
