Miniflux.Feed = (function() {

    // List of subscriptions
    var feeds = [];

    // List of feeds currently updating
    var queue = [];

    // Number of concurrent requests when updating all feeds
    var queue_length = 5;

    // Update the items unread/total count for the feed
    function updateItemsCounter(feed_id, counts)
    {
        var container = document.getElementById("items-count-" + feed_id);
        if (container) container.innerHTML = counts["items_unread"] + "/" + counts['items_total'];
    }

    return {
        Update: function(feed_id, callback) {
            var container = document.getElementById("items-count-" + feed_id);
            if (! container) return;

            //add data-feed-id to article element and couse the first h2
            container.parentNode.className = "loading-icon";
            
            var request = new XMLHttpRequest();
            request.onload = function() {
                container.parentNode.className = "";

                var response = JSON.parse(this.responseText);
                if (response.result) updateItemsCounter(feed_id, response.items_count);
                
                if (callback) callback(response);
            };

            request.open("POST", "?action=refresh-feed&feed_id=" + feed_id, true);
            request.send();
        },
        UpdateAll: function() {
            var links = document.getElementsByTagName("a");

            for (var i = 0, ilen = links.length; i < ilen; i++) {
                var feed_id = links[i].getAttribute('data-feed-id');
                if (feed_id) feeds.push(parseInt(feed_id));
            }

            var interval = setInterval(function() {
                while (feeds.length > 0 && queue.length < queue_length) {
                    var feed_id = feeds.shift();
                    queue.push(feed_id);

                    Miniflux.Feed.Update(feed_id, function(response) {
                        var index = queue.indexOf(response.feed_id);
                        if (index >= 0) queue.splice(index, 1);

                        if (feeds.length === 0 && queue.length === 0) {
                            clearInterval(interval);
                            window.location.href = "?action=unread";
                        }
                    });
                }
            }, 100);
        }
    };
})();