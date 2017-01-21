<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li class="active"><a href="?action=config"><?php echo t('general') ?></a></li>
            <li><a href="?action=profile"><?php echo t('profile') ?></a></li>
            <?php if (Miniflux\Helper\is_admin()): ?>
                <li><a href="?action=users"><?php echo t('users') ?></a></li>
            <?php endif ?>
            <li><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=help"><?php echo t('help') ?></a></li>
            <li><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
<form method="post" action="?action=config" autocomplete="off" id="config-form">
    <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>

    <h3><?php echo t('Application') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_label(t('Timezone'), 'timezone') ?>
        <?php echo Miniflux\Helper\form_select('timezone', $timezones, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Language'), 'language') ?>
        <?php echo Miniflux\Helper\form_select('language', $languages, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Theme'), 'theme') ?>
        <?php echo Miniflux\Helper\form_select('theme', $theme_options, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_checkbox('image_proxy', t('Enable image proxy'), 1, isset($values['image_proxy']) && $values['image_proxy'] == 1) ?>
        <div class="form-help"><?php echo t('Avoid mixed content warnings with HTTPS') ?></div>
    </div>

    <h3><?php echo t('Reading') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_label(t('Remove automatically read items'), 'autoflush') ?>
        <?php echo Miniflux\Helper\form_select('autoflush', $autoflush_read_options, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Remove automatically unread items'), 'autoflush_unread') ?>
        <?php echo Miniflux\Helper\form_select('autoflush_unread', $autoflush_unread_options, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Items per page'), 'items_per_page') ?>
        <?php echo Miniflux\Helper\form_select('items_per_page', $paging_options, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Default sorting order for items'), 'items_sorting_direction') ?>
        <?php echo Miniflux\Helper\form_select('items_sorting_direction', $sorting_options, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Display items on lists'), 'items_display_mode') ?>
        <?php echo Miniflux\Helper\form_select('items_display_mode', $display_mode, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Item title links to'), 'item_title_link') ?>
        <?php echo Miniflux\Helper\form_select('item_title_link', $item_title_link, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('When there is nothing to read, redirect me to this page'), 'redirect_nothing_to_read') ?>
        <?php echo Miniflux\Helper\form_select('redirect_nothing_to_read', $redirect_nothing_to_read_options, $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Refresh interval in minutes for unread counter'), 'frontend_updatecheck_interval') ?>
        <?php echo Miniflux\Helper\form_number('frontend_updatecheck_interval', $values, $errors, array('min="0"')) ?><br/>

        <?php echo Miniflux\Helper\form_checkbox('original_marks_read', t('Original link marks article as read'), 1, isset($values['original_marks_read']) && $values['original_marks_read'] == 1) ?><br/>
        <?php echo Miniflux\Helper\form_checkbox('nocontent', t('Do not fetch the content of articles'), 1, isset($values['nocontent']) && $values['nocontent'] == 1) ?><br/>
        <?php echo Miniflux\Helper\form_checkbox('favicons', t('Download favicons'), 1, isset($values['favicons']) && $values['favicons'] == 1) ?><br/>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?php echo t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>

<div class="page-section">
    <h2><?php echo t('Advanced') ?></h2>
</div>
<section class="panel panel-danger">
<ul>
    <li><a href="?action=generate-tokens&amp;csrf=<?php echo $values['csrf'] ?>"><?php echo t('Generate new tokens') ?></a> (<?php echo t('Miniflux API') ?>, <?php echo t('Fever API') ?>, <?php echo t('Bookmarklet') ?>, <?php echo t('Bookmark RSS Feed') ?>)</li>
</ul>
</section>
