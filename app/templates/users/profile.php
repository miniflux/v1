<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li class="active"><a href="?action=profile"><?php echo t('profile') ?></a></li>
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
    <form method="post" action="?action=profile" autocomplete="off" id="config-form">

        <h3><?php echo t('Authentication') ?></h3>
        <div class="options">
            <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>
            <?php echo Miniflux\Helper\form_hidden('id', $values) ?>

            <?php echo Miniflux\Helper\form_label(t('Username'), 'username') ?>
            <?php echo Miniflux\Helper\form_text('username', $values, $errors, array('required')) ?>

            <?php echo Miniflux\Helper\form_label(t('Current Password'), 'current_password') ?>
            <?php echo Miniflux\Helper\form_password('current_password', $values, $errors, array('required')) ?>

            <?php echo Miniflux\Helper\form_label(t('New Password'), 'password') ?>
            <?php echo Miniflux\Helper\form_password('password', $values, $errors) ?>

            <?php echo Miniflux\Helper\form_label(t('Confirmation'), 'confirmation') ?>
            <?php echo Miniflux\Helper\form_password('confirmation', $values, $errors) ?>
        </div>

        <div class="form-actions">
            <input type="submit" value="<?php echo t('Save') ?>" class="btn btn-blue"/>
        </div>
    </form>
</section>
