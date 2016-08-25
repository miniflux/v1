<?php

namespace Miniflux\Handler\Opml;

use Miniflux\Model\Feed;
use Miniflux\Model\Group;
use PicoFeed\Serialization\Subscription;
use PicoFeed\Serialization\SubscriptionList;
use PicoFeed\Serialization\SubscriptionListBuilder;


function export_all_feeds()
{
    $feeds = Feed\get_all();
    $subscriptionList = SubscriptionList::create()->setTitle(t('Subscriptions'));

    foreach ($feeds as $feed) {
        $groups = Group\get_feed_groups($feed['id']);
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
