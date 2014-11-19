<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="icon" type="image/png" href="assets/img/favicon.png">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/touch-icon-ipad-retina.png">
        <title>Miniflux</title>
        <link href="<?= Helper\css() ?>" rel="stylesheet" media="screen">
    </head>
    <body id="login-page">
        <section class="page" id="login">
            <?php if (isset($errors['login'])): ?>
                <p class="alert alert-error"><?= Helper\escape($errors['login']) ?></p>
            <?php endif ?>

            <form method="post" action="?action=login">

                <?= Helper\form_hidden('csrf', $values) ?>

                <?= Helper\form_label(t('Username'), 'username') ?>
                <?= Helper\form_text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

                <?= Helper\form_label(t('Password'), 'password') ?>
                <?= Helper\form_password('password', $values, $errors, array('required')) ?>

                <?= Helper\form_checkbox('remember_me', t('Remember Me'), 1) ?><br/>

                <div class="form-actions">
                    <input type="submit" value="<?= t('Sign in') ?>" class="btn btn-blue"/>
                </div>
            </form>

            <?php if (ENABLE_MULTIPLE_DB && count($databases) > 1): ?>
            <div>
                <h3><?= t('Select another database') ?></h3>
                <ul>
                    <?php foreach ($databases as $filename => $dbname): ?>
                        <li>
                            <?php if ($current_database === $filename): ?>
                                <strong><?= Helper\escape($dbname) ?></strong>
                            <?php else: ?>
                                <a href="?action=select-db&amp;database=<?= Helper\escape($filename) ?>"><?= Helper\escape($dbname) ?></a>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <?php endif ?>
        </section>
    </body>
</html>
