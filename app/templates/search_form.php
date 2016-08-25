<div class="search">
    <span id="search-opener"<?= isset($opened) && $opened ? ' class="hide"' : '' ?> data-action="show-search">&laquo; <?= t('Search')?></span>
    <form id="search-form"<?= isset($opened) && $opened ? '' : ' class="hide"' ?> action="?" method="get">
        <?= Miniflux\Helper\form_hidden('action', array('action' => 'search')) ?>
        <?= Miniflux\Helper\form_text('text', array('text' => isset($text) ? $text : ''), array(), array('required', 'placeholder="' . t('Search') . '"')) ?>
    </form>
</div>
