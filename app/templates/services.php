<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li class="active"><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=database"><?php echo t('database') ?></a></li>
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
        <?php echo Miniflux\Helper\form_text('pinboard_tags', $values, $errors) ?>
    </div>


    <h3><?php echo t('Instapaper') ?></h3>
    <div class="options">
        <?php echo Miniflux\Helper\form_checkbox('instapaper_enabled', t('Send bookmarks to Instapaper'), 1, isset($values['instapaper_enabled']) && $values['instapaper_enabled'] == 1) ?><br />

        <?php echo Miniflux\Helper\form_label(t('Instapaper username'), 'instapaper_username') ?>
        <?php echo Miniflux\Helper\form_text('instapaper_username', $values, $errors) ?><br/>

        <?php echo Miniflux\Helper\form_label(t('Instapaper password'), 'instapaper_password') ?>
        <?php echo Miniflux\Helper\form_password('instapaper_password', $values, $errors) ?><br/>
    </div>
    
    <div class="form-actions">
        <input type="submit" value="<?php echo t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>
