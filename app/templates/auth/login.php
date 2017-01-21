<!DOCTYPE html>
<html
    <?php if (Miniflux\Model\Config\is_language_rtl()): ?>
        dir="rtl"
    <?php endif ?>
>
    <head>
        <?php echo Miniflux\Template\load('common/head') ?>
        <link href="<?php echo Miniflux\Helper\css() ?>" rel="stylesheet" media="screen">
    </head>
    <body id="login-page">
        <section class="page" id="login">
            <?php if (isset($errors['login'])): ?>
                <p class="alert alert-error"><?php echo Miniflux\Helper\escape($errors['login']) ?></p>
            <?php endif ?>

            <form method="post" action="?action=login">

                <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>

                <?php echo Miniflux\Helper\form_label(t('Username'), 'username') ?>
                <?php echo Miniflux\Helper\form_text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

                <?php echo Miniflux\Helper\form_label(t('Password'), 'password') ?>
                <?php echo Miniflux\Helper\form_password('password', $values, $errors, array('required')) ?>

                <?php echo Miniflux\Helper\form_checkbox('remember_me', t('Remember Me'), 1) ?><br/>

                <div class="form-actions">
                    <input type="submit" value="<?php echo t('Sign in') ?>" class="btn btn-blue"/>
                </div>
            </form>

        </section>
    </body>
</html>
