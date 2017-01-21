<?php echo Miniflux\Template\load('common/search', array('opened' => true, 'text' => $text)) ?>

<?php if (empty($items)): ?>
    <p class="alert alert-info"><?php echo t('There are no results for your search') ?></p>
<?php else: ?>
    <div class="page-header">
        <h2><?php echo t('Search') ?><span id="page-counter"><?php echo isset($nb_items) ? $nb_items : '' ?></span></h2>
    </div>

    <section class="items" id="listing">
        <?php foreach ($items as $item): ?>
            <?php echo Miniflux\Template\load('items/item', array(
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

        <?php echo Miniflux\Template\load('items/paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'text' => $text)) ?>
    </section>

<?php endif ?>
