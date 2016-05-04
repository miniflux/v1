<?= \Template\load('search_form', array('text' => $text, 'opened' => true)) ?>
<?php if (empty($items)): ?>
    <p class="alert alert-info"><?= t('There are no results for your search') ?></p>
<?php else: ?>
    <div class="page-header">
        <h2><?= t('Search') ?><span id="page-counter"><?= isset($nb_items) ? $nb_items : '' ?></span></h2>
    </div>

    <section class="items" id="listing">
        <?php foreach ($items as $item): ?>
            <?= \Template\load('item', array(
                'item' => $item,
                'menu' => $menu,
                'offset' => $offset,
                'hide' => false,
                'display_mode' => $display_mode,
                'item_title_link' => $item_title_link,
                'favicons' => $favicons,
                'original_marks_read' => $original_marks_read,
            )) ?>
        <?php endforeach ?>

        <?= \Template\load('paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'text' => $text)) ?>
    </section>

<?php endif ?>
