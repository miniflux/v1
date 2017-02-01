<div class="page-header">
    <h2><?php echo t('Subscriptions') ?></h2>
    <nav>
        <ul>
            <li><a href="?action=add"><?php echo t('add') ?></a></li>
            <li class="active"><a href="?action=feeds"><?php echo t('feeds') ?></a></li>
            <li><a href="?action=groups"><?php echo t('groups') ?></a></li>
            <li><a href="?action=import"><?php echo t('import') ?></a></li>
            <li><a href="?action=export"><?php echo t('export') ?></a></li>
            <li><a href="?action=refresh-all" data-action="refresh-all" data-concurrent-requests="<?php echo SUBSCRIPTION_CONCURRENT_REQUESTS ?>"><?php echo t('refresh all') ?></a></li>
        </ul>
    </nav>
</div>

<?php if (empty($feeds)): ?>
    <p class="alert alert-info"><?php echo t('No subscription') ?></p>
<?php else: ?>

    <?php if ($nothing_to_read): ?>
        <p class="alert alert-info"><?php echo tne('Nothing to read, do you want to %supdate your subscriptions%s?','<a href="?action=refresh-all" data-action="refresh-all">','</a>') ?></p>
    <?php endif ?>

    <section class="items">
    <?php foreach ($feeds as $feed): ?>
        <article data-feed-id="<?php echo $feed['id'] ?>" <?php echo (! $feed['enabled']) ? 'data-feed-disabled="1"' : '' ?> <?php echo $feed['parsing_error'] > 0 ? 'data-feed-error="1"' : '' ?>>
            <h2>
                <?php if (! $feed['enabled']): ?>
                    <span title="<?php echo t('Subscription disabled') ?>">✖</span>
                <?php endif ?>

                <?php echo Miniflux\Helper\favicon($favicons, $feed['id']) ?>

                <a href="?action=feed-items&amp;feed_id=<?php echo $feed['id'] ?>" title="<?php echo t('Show only this subscription') ?>"><?php echo Miniflux\Helper\escape($feed['title']) ?></a>
                &lrm;<span class="items-count"><?php echo $feed['items_unread'], '/', $feed['items_total'] ?></span>

                <?php if ($feed['enabled']): ?>

                    <br/>

                    <?php if ($feed['last_checked']): ?>
                        <time class="feed-last-checked" data-after-update="<?php echo t('updated just now') ?>">
                            <?php echo t('checked at'), ' ', dt('%e %B %Y %k:%M', $feed['last_checked']) ?>
                        </time>
                    <?php else: ?>
                        <span class="feed-last-checked" data-after-update="<?php echo t('updated just now') ?>">
                            <?php echo t('never updated after creation') ?>
                        </span>
                    <?php endif ?>
                <?php endif ?>

                <span class="feed-parsing-error">
                    <?php echo t('An error occurred during the last check: "%s".', $feed['parsing_error_message']) ?>
                </span>
            </h2>

            <?php if (! empty($feed['groups'])): ?>
                <ul class="feed-groups">
                    <?php foreach ($feed['groups'] as $group): ?>
                        <li>
                            <?php echo Miniflux\Helper\link($group['title'], 'unread', array('group_id' => $group['id'])) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>

            <ul class="item-menu">
                <li>
                    <a href="<?php echo $feed['site_url'] ?>" rel="noreferrer" target="_blank"><?php echo Miniflux\Helper\get_host_from_url($feed['site_url']) ?></a>
                </li>

                <?php if ($feed['enabled']): ?>
                <li>
                    <a href="?action=refresh-feed&amp;feed_id=<?php echo $feed['id'] ?>" data-action="refresh-feed"><?php echo t('refresh') ?></a>
                </li>
                <?php endif ?>

                <li><a href="?action=edit-feed&amp;feed_id=<?php echo $feed['id'] ?>"><?php echo t('edit') ?></a></li>
            </ul>
        </article>
    <?php endforeach ?>
    </section>

<?php endif ?>
