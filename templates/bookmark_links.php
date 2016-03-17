<?php if ($item['bookmark']): ?>
    <span
        class="bookmark-icon"
        data-action="bookmark"
        data-reverse-title="<?= t('bookmark') ?>"
        title="<?= t('remove bookmark') ?>"
    ></span>
<?php else: ?>
    <span
        class="bookmark-icon"
        data-action="bookmark"
        data-reverse-title="<?= t('remove bookmark') ?>"
        title="<?= t('bookmark') ?>"
    ></span>
<?php endif ?>
