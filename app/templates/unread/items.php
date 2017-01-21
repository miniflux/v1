<?php echo Miniflux\Template\load('common/search') ?>

<div class="page-header">
    <h2><?php echo t('Unread') ?><span id="page-counter"><?php echo isset($nb_items) ? $nb_items : '' ?></span></h2>
    <?php if (!empty($groups)): ?>
    <nav>
        <ul id="grouplist">
            <?php foreach ($groups as $group): ?>
            <li  <?php echo $group['id'] == $group_id ? 'class="active"' : '' ?>>
                <a href="?action=unread&group_id=<?php echo$group['id']?>"><?php echo$group['title']?></a>
            </li>
            <?php endforeach ?>
        </ul>
    </nav>
    <?php endif ?>

    <ul>
        <li>
            <a href="?action=unread<?php echo $group_id === null ? '' : '&amp;group_id='.$group_id ?>&amp;order=updated&amp;direction=<?php echo $direction == 'asc' ? 'desc' : 'asc' ?>"><?php echo tne('sort by date %s(%s)%s', '<span class="hide-mobile">',$direction == 'desc' ? t('older first') : t('most recent first'), '</span>') ?></a>
        </li>
        <li>
            <a href="?action=mark-all-read<?php echo $group_id === null ? '' : '&amp;group_id='.$group_id ?>"><?php echo t('mark all as read') ?></a>
        </li>
    </ul>
</div>

<section class="items" id="listing">
    <?php if (empty($items)): ?>
        <p class="alert alert-info"><?php echo t('Nothing to read') ?></p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <?php echo Miniflux\Template\load('items/item', array(
                'item' => $item,
                'menu' => $menu,
                'offset' => $offset,
                'hide' => true,
                'display_mode' => $display_mode,
                'item_title_link' => $item_title_link,
                'favicons' => $favicons,
                'original_marks_read' => $original_marks_read,
                'group_id' => $group_id,
            )) ?>
        <?php endforeach ?>

        <div id="bottom-menu">
            <a href="?action=mark-all-read<?php echo $group_id === null ? '' : '&amp;group_id='.$group_id ?>"><?php echo t('mark all as read') ?></a>
        </div>

        <?php echo Miniflux\Template\load('items/paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'order' => $order, 'direction' => $direction, 'group_id' => $group_id)) ?>
    <?php endif ?>
</section>
