<?php

namespace Miniflux\Handler\Opml;

use Miniflux\Model;
use PicoDb\Database;
use PicoFeed\Serialization\Subscription;
use PicoFeed\Serialization\SubscriptionList;
use PicoFeed\Serialization\SubscriptionListBuilder;
use PicoFeed\Serialization\SubscriptionListParser;

function export_all_feeds($user_id)
{
    $feeds = Model\Feed\get_feeds($user_id);
    $subscriptionList = SubscriptionList::create()->setTitle(t('Subscriptions'));

    foreach ($feeds as $feed) {
        $groups = Model\Group\get_feed_groups($feed['id']);
        $category = '';

        if (!empty($groups)) {
            $category = $groups[0]['title'];
        }

        $subscriptionList->addSubscription(Subscription::create()
            ->setTitle($feed['title'])
            ->setSiteUrl($feed['site_url'])
            ->setFeedUrl($feed['feed_url'])
            ->setCategory($category)
        );
    }

    return SubscriptionListBuilder::create($subscriptionList)->build();
}

function import_opml($user_id, $content)
{
    $subscriptionList = SubscriptionListParser::create($content)->parse();

    $db = Database::getInstance('db');
    $db->startTransaction();

    foreach ($subscriptionList->subscriptions as $subscription) {
        if (! $db->table('feeds')->eq('user_id', $user_id)->eq('feed_url', $subscription->getFeedUrl())->exists()) {
            $db->table('feeds')->insert(array(
                'user_id'  => $user_id,
                'title'    => $subscription->getTitle(),
                'site_url' => $subscription->getSiteUrl(),
                'feed_url' => $subscription->getFeedUrl(),
            ));

            if ($subscription->getCategory() !== '') {
                $feed_id = $db->getLastId();
                $group_id = Model\Group\get_group_id_from_title($user_id, $subscription->getCategory());

                if (empty($group_id)) {
                    $group_id = Model\Group\create_group($user_id, $subscription->getCategory());
                }

                Model\Group\associate_feed_groups($feed_id, array($group_id));
            }
        }
    }

    $db->closeTransaction();
    return true;
}
