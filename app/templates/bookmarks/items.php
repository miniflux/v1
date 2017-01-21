<?php if (empty($items) && $group_id === null): ?>
    <p class="alert alert-info"><?php echo t('No bookmark') ?></p>
<?php else: ?>
    <?php echo Miniflux\Template\load('common/search') ?>

    <div class="page-header">
        <h2><?php echo t('Bookmarks') ?><span id="page-counter"><?php echo isset($nb_items) ? $nb_items : '' ?></span></h2>
        <?php if (!empty($groups)): ?>
        <nav>
            <ul id="grouplist">
                <?php foreach ($groups as $group): ?>
                <li  <?php echo $group['id'] == $group_id ? 'class="active"' : '' ?>>
                    <a href="?action=bookmarks&group_id=<?php echo$group['id']?>"><?php echo$group['title']?></a>
                </li>
                <?php endforeach ?>
            </ul>
        </nav>
        <?php endif ?>
    </div>

    <?php if ($nothing_to_read): ?>
        <p class="alert alert-info"><?php echo t('There is nothing new to read, enjoy your favorites articles!') ?></p>
    <?php endif ?>

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

        <?php echo Miniflux\Template\load('items/paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'order' => $order, 'direction' => $direction, 'group_id' => $group_id)) ?>
    </section>
<?php endif ?>
