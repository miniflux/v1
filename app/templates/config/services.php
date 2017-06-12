<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li><a href="?action=profile"><?php echo t('profile') ?></a></li>
            <?php if (Miniflux\Helper\is_admin()): ?>
                <li><a href="?action=users"><?php echo t('users') ?></a></li>
            <?php endif ?>
            <li class="active"><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=help"><?php echo t('help') ?></a></li>
            <li><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>

<form method="post" action="?action=services" autocomplete="off" id="config-form">

    <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>

    <h3><?php echo t('Pinboard') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_checkbox('pinboard_enabled', t('Send bookmarks to Pinboard'), 1, isset($values['pinboard_enabled']) && $values['pinboard_enabled'] == 1) ?><br />

        <?php echo Miniflux\Helper\form_label(t('Pinboard API token'), 'pinboard_token') ?>
        <?php echo Miniflux\Helper\form_text('pinboard_token', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Pinboard tags'), 'pinboard_tags') ?>
        <?php echo Miniflux\Helper\form_text('pinboard_tags', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_checkbox('pinboard_mark_unread', t('Mark bookmarks as unread'), 1, isset($values['pinboard_mark_unread']) && $values['pinboard_mark_unread'] == 1) ?>
    </div>


    <h3><?php echo t('Instapaper') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_checkbox('instapaper_enabled', t('Send bookmarks to Instapaper'), 1, isset($values['instapaper_enabled']) && $values['instapaper_enabled'] == 1) ?><br />

        <?php echo Miniflux\Helper\form_label(t('Instapaper username'), 'instapaper_username') ?>
        <?php echo Miniflux\Helper\form_text('instapaper_username', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Instapaper password'), 'instapaper_password') ?>
        <?php echo Miniflux\Helper\form_password('instapaper_password', $values, $errors) ?><br/>
    </div>

    <h3><?php echo t('Wallabag') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_checkbox('wallabag_enabled', t('Send bookmarks to Wallabag'), 1, isset($values['wallabag_enabled']) && $values['wallabag_enabled'] == 1) ?><br />

        <?php echo Miniflux\Helper\form_label(t('Wallabag URL'), 'wallabag_url') ?>
        <?php echo Miniflux\Helper\form_text('wallabag_url', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Wallabag Client ID'), 'wallabag_client_id') ?>
        <?php echo Miniflux\Helper\form_text('wallabag_client_id', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Wallabag Client Secret'), 'wallabag_client_secret') ?>
        <?php echo Miniflux\Helper\form_text('wallabag_client_secret', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Wallabag username'), 'wallabag_username') ?>
        <?php echo Miniflux\Helper\form_text('wallabag_username', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Wallabag password'), 'wallabag_password') ?>
        <?php echo Miniflux\Helper\form_text('wallabag_password', $values, $errors) ?><br/>
    </div>

    <h3><?php echo t('Shaarli') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_checkbox('shaarli_enabled', t('Send bookmarks to Shaarli'), 1, isset($values['shaarli_enabled']) && $values['shaarli_enabled'] == 1) ?><br />

        <?php echo Miniflux\Helper\form_label(t('Shaarli URL'), 'shaarli_url') ?>
        <?php echo Miniflux\Helper\form_text('shaarli_url', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Shaarli secret'), 'shaarli_secret') ?>
        <?php echo Miniflux\Helper\form_text('shaarli_secret', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Shaarli tags'), 'shaarli_tags') ?>
        <?php echo Miniflux\Helper\form_text('shaarli_tags', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_checkbox('shaarli_private', t('Private Bookmarks'), 1, isset($values['shaarli_private']) && $values['shaarli_private'] == 1) ?><br />
    </div>

    <div class="form-actions">
        <input type="submit" value="<?php echo t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>
