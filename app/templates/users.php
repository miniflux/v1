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
    <p><a href="?action=new-user"><?php echo t('New User') ?></a><br><br></p>
    <table>
        <tr>
            <th><?php echo t('Username') ?></th>
            <th><?php echo t('Administrator') ?></th>
            <th><?php echo t('Last Login') ?></th>
            <th><?php echo t('Action') ?></th>
        </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td>
                <?php echo Miniflux\Helper\escape($user['username']) ?>
            </td>
            <td>
                <?php echo $user['is_admin'] ? t('Yes') : t('No') ?>
            </td>
            <td>
                <?php echo $user['last_login'] ? dt('%e %B %Y %k:%M', $user['last_login']) : t('Never') ?>
            </td>
            <td>
                <?php if (Miniflux\Helper\get_user_id() != $user['id']): ?>
                    <a href="?action=edit-user&amp;user_id=<?php echo $user['id'] ?>"><?php echo t('Edit') ?></a> -
                    <a href="?action=confirm-remove-user&amp;user_id=<?php echo $user['id'] ?>"><?php echo t('Remove') ?></a>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    </table>
</section>
