<?php
    $params = array();
    if (isset($order)) {
        $params[] = 'order=' . $order;
    }
    if (isset($direction)) {
        $params[] = 'direction=' . $direction;
    }
    if (isset($feed_id)) {
        $params[] = 'feed_id=' . $feed_id;
    }
    if (isset($group_id)) {
        $params[] = 'group_id=' . $group_id;
    }
    if (isset($text)) {
        $params[] = 'text=' . $text;
    }
    $optionals = '';
    if ($params) {
        $optionals = '&amp;' . implode('&amp;', $params);
    }
?>
<div id="items-paging">
<?php if ($offset > 0): ?>
    <a id="previous-page" href="?action=<?php echo $menu ?>&amp;offset=<?php echo ($offset - $items_per_page) ?><?php echo $optionals; ?>">« <?php echo t('Previous page') ?></a>
<?php endif ?>
&nbsp;
<?php if (($nb_items - $offset) > $items_per_page): ?>
    <a id="next-page" href="?action=<?php echo $menu ?>&amp;offset=<?php echo ($offset + $items_per_page) ?><?php echo $optionals; ?>"><?php echo t('Next page') ?> »</a>
<?php endif ?>
</div>
