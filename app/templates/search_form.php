<div class="search">
    <span id="search-opener"<?php echo isset($opened) && $opened ? ' class="hide"' : '' ?> data-action="show-search">&laquo; <?php echo t('Search')?></span>
    <form id="search-form"<?php echo isset($opened) && $opened ? '' : ' class="hide"' ?> action="?" method="get">
        <?php echo Miniflux\Helper\form_hidden('action', array('action' => 'search')) ?>
        <?php echo Miniflux\Helper\form_text('text', array('text' => isset($text) ? $text : ''), array(), array('required', 'placeholder="' . t('Search') . '"')) ?>
    </form>
</div>
