<?php if ($item['status'] == 'unread'): ?>
    <span
        class="read-icon"
        data-action="mark-read"
        data-reverse-title="<?= t('mark as unread') ?>"
        title="<?= t('mark as read') ?>"
    ></span>
<?php else: ?>
    <span
        class="read-icon"
        data-action="mark-unread"
        data-reverse-title="<?= t('mark as read') ?>"
        title="<?= t('mark as unread') ?>"
    ></span>
<?php endif ?>
