<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li><a href="?action=profile"><?php echo t('profile') ?></a></li>
            <?php if (Miniflux\Helper\is_admin()): ?>
                <li class="active"><a href="?action=users"><?php echo t('users') ?></a></li>
            <?php endif ?>
            <li><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=help"><?php echo t('help') ?></a></li>
            <li><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
    <form method="post" action="?action=edit-user" autocomplete="off" id="config-form">
        <div class="options">
            <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>
            <?php echo Miniflux\Helper\form_hidden('id', $values) ?>

            <?php echo Miniflux\Helper\form_label(t('Username'), 'username') ?>
            <?php echo Miniflux\Helper\form_text('username', $values, $errors, array('required')) ?>

            <?php echo Miniflux\Helper\form_label(t('Password'), 'password') ?>
            <?php echo Miniflux\Helper\form_password('password', $values, $errors) ?>

            <?php echo Miniflux\Helper\form_label(t('Confirmation'), 'confirmation') ?>
            <?php echo Miniflux\Helper\form_password('confirmation', $values, $errors) ?>

            <?php echo Miniflux\Helper\form_checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1) ?>
        </div>

        <div class="form-actions">
            <input type="submit" value="<?php echo t('Save') ?>" class="btn btn-blue">
        </div>
    </form>
</section>
