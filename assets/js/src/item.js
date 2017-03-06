Miniflux.Item = (function() {

    // timestamp of the latest item per feed ever seen
    var latest_feeds_items = {};

    // indicator for new unread items
    var unreadItems = false;

    var nbUnreadItems = function() {
        var navCounterElement = document.getElementById("nav-counter");

        if (navCounterElement) {
            return parseInt(navCounterElement.textContent, 10) || 0;
        }
    }();

    var nbPageItems = function() {
        var pageCounterElement = document.getElementById("page-counter");

        if (pageCounterElement) {
            return parseInt(pageCounterElement.textContent, 10) || 0;
        }
    }();

    function simulateMouseClick(element)
    {
        var event = document.createEvent("MouseEvents");
        event.initEvent("mousedown", true, true);
        element.dispatchEvent(event);

        event = document.createEvent("MouseEvents");
        event.initEvent("mouseup", true, true);
        element.dispatchEvent(event);

        element.click();
    }

    function getItemID(item)
    {
        return item.getAttribute("data-item-id");
    }

    function changeLabel(links)
    {
        if (links.length === 0) {
            return;
        }

        for (var i = 0; i < links.length; i++) {
            var link = links[i];

            if (link.hasAttribute("data-reverse-label")) {
                var content = link.innerHTML;
                link.innerHTML = link.getAttribute("data-reverse-label");
                link.setAttribute("data-reverse-label", content);
            }

            if (link.hasAttribute("data-reverse-title")) {
                var title = link.getAttribute("title");
                link.setAttribute("title", link.getAttribute("data-reverse-title"));
                link.setAttribute("data-reverse-title", title);
            }
        }
    }

    function changeAction(links, action)
    {
        if (links.length === 0) {
            return;
        }

        for (var i = 0; i < links.length; i++) {
            links[i].setAttribute("data-action", action);
        }
    }

    function changeBookmarkLabel(item)
    {
        var links = item.querySelectorAll(".bookmark-icon, a.bookmark");
        changeLabel(links);
    }

    function changeStatusLabel(item)
    {
        var links = item.querySelectorAll(".read-icon, a.mark");
        changeLabel(links);
    }

    function showItemAsRead(item)
    {
        if (item.getAttribute("data-item-status") === 'read') {
            return;
        }

        nbUnreadItems--;

        if (item.getAttribute("data-hide")) {
            hideItem(item);
            return;
        }

        item.setAttribute("data-item-status", "read");
        changeStatusLabel(item);

        var links = item.querySelectorAll(".read-icon, a.mark");
        changeAction(links, "mark-unread");
    }

    function showItemAsUnread(item)
    {
        if (item.getAttribute("data-item-status") === 'unread') {
            return;
        }

        nbUnreadItems++;

        if (item.getAttribute("data-hide")) {
            hideItem(item);
            return;
        }

        item.setAttribute("data-item-status", "unread");
        changeStatusLabel(item);

        var links = item.querySelectorAll(".read-icon, a.mark");
        changeAction(links, "mark-read");
    }

    function hideItem(item)
    {
        if (Miniflux.Event.lastEventType !== "mouse" && Miniflux.Event.lastEventType !== "touch") {
            var items = document.getElementsByTagName("article");

            if (items[items.length-1].id === "current-item") {
                Miniflux.Nav.SelectPreviousItem();
            }
            else {
                Miniflux.Nav.SelectNextItem();
            }
        }

        item.parentNode.removeChild(item);
        nbPageItems--;
    }

    function updateCounters()
    {
        var pageHeading = null;

        var pageCounterElement = document.getElementById("page-counter");
        if (pageCounterElement) pageCounterElement.textContent = nbPageItems || '';

        var navCounterElement = document.getElementById("nav-counter");
        navCounterElement.textContent = nbUnreadItems || '';

        var pageHeadingElement = document.querySelector("div.page-header h2:first-of-type");
        if (pageHeadingElement) {
            pageHeading = pageHeadingElement.firstChild.nodeValue;
        }
        else {
            // special handling while viewing an article.
            // 1. The article does not have a page-header element
            // 2. An article could be opened from any page and has the original
            // page as data-item-page value
            var itemHeading = document.querySelector("article.item h1:first-of-type");
            if (itemHeading) {
                document.title = itemHeading.textContent;
                return;
            }
        }

        // pagetitle depends on current page
        var sectionElement = document.querySelector("section.page");
        switch (sectionElement.getAttribute("data-item-page")) {
            case "unread":
                document.title = "Miniflux (" + nbUnreadItems + ")";
                break;
            case "feed-items":
                document.title = "(" + nbPageItems + ") " + pageHeading;
                break;
            default:
                if (pageCounterElement) {
                    document.title = pageHeading + " (" + nbPageItems + ")";
                }
                else {
                    document.title = pageHeading;
                }
                break;
        }
    }

    function markAsRead(item)
    {
        var item_id = getItemID(item);
        var request = new XMLHttpRequest();

        request.onload = function() {
            if (Miniflux.Nav.IsListing()) {
                showItemAsRead(item);
                updateCounters();
            }
        };
        request.open("POST", "?action=mark-item-read&id=" + item_id, true);
        request.send();
    }

    function markAsUnread(item)
    {
        var item_id = getItemID(item);
        var request = new XMLHttpRequest();

        request.onload = function() {
            if (Miniflux.Nav.IsListing()) {
                showItemAsUnread(item);
                updateCounters();
            }
        };
        request.open("POST", "?action=mark-item-unread&id=" + item_id, true);
        request.send();
    }

    function markAsRemoved(item)
    {
        var item_id = getItemID(item);
        var request = new XMLHttpRequest();

        request.onload = function() {
            if (Miniflux.Nav.IsListing()) {
                hideItem(item);

                if (item.getAttribute("data-item-status") === "unread") nbUnreadItems--;
                updateCounters();
            }
        };
        request.open("POST", "?action=mark-item-removed&id=" + item_id, true);
        request.send();
    }

    return {
        MarkAsRead: markAsRead,
        MarkAsUnread: markAsUnread,
        MarkAsRemoved: markAsRemoved,
        SwitchBookmark: function(item) {
            var item_id = getItemID(item);
            var value = item.getAttribute("data-item-bookmark") === "1" ? "0" : "1";
            var request = new XMLHttpRequest();

            request.onload = function() {
                var sectionElement = document.querySelector("section.page");

                if (Miniflux.Nav.IsListing() && sectionElement.getAttribute("data-item-page") === "bookmarks") {
                    hideItem(item);
                    updateCounters();
                } else {
                    item.setAttribute("data-item-bookmark", value);
                    changeBookmarkLabel(item);
                }
            };

            request.open("POST", "?action=bookmark&id=" + item_id + "&value=" + value, true);
            request.send();
        },
        SwitchStatus: function(item) {
            var status = item.getAttribute("data-item-status");

            if (status === "read") {
                markAsUnread(item);
            }
            else if (status === "unread") {
                markAsRead(item);
            }
        },
        Show: function(item) {
            var link = item.querySelector("a.show");
            if (link) simulateMouseClick(link);
        },
        OpenOriginal: function(item) {
            var link = item.querySelector("a.original");
            if (link) {
                simulateMouseClick(link);
            }
        },
        DownloadContent: function(item) {
            var container = document.getElementById("download-item");
            if (! container) return;

            container.innerHTML = " " + container.getAttribute("data-before-message");
            container.className = "loading-icon";

            var request = new XMLHttpRequest();
            request.onload = function() {

                var response = JSON.parse(request.responseText);
                container.className = "";

                if (response.result) {
                    var content = document.getElementById("item-content");
                    if (content) content.innerHTML = response.content;

                    container.innerHTML = container.getAttribute("data-after-message");
                }
                else {
                    container.innerHTML = container.getAttribute("data-failure-message");
                }
            };

            var item_id = getItemID(item);
            request.open("POST", "?action=download-item&id=" + item_id, true);
            request.send();
        },
        MarkFeedAsRead: function(feed_id) {
            var request = new XMLHttpRequest();

            request.onload = function() {
                var articles = document.getElementsByTagName("article");

                for (var i = 0, ilen = articles.length; i < ilen; i++) {
                    showItemAsRead(articles[i]);
                }

                nbUnreadItems = this.responseText;
                updateCounters();
            };

            request.open("POST", "?action=mark-feed-as-read&feed_id=" + feed_id, true);
            request.send();
        },
        ToggleRTLMode: function() {
            var tags = [
                "#current-item h1",
                "#item-content",
                "#listing #current-item h2",
                "#listing #current-item .preview",
                "#listing #current-item .preview-full-content"
            ];

            for (var i = 0; i < tags.length; i++) {
                var tag = document.querySelector(tags[i]);

                if (tag) {
                    tag.dir = tag.dir === "" ? "rtl" : "";
                }
            }
        },
        hasNewUnread: function() {
            return unreadItems;
        },
        CheckForUpdates: function() {
           if (document.hidden && unreadItems) {
                Miniflux.App.Log('We already have updates, no need to check again');
                return;
            }

            var request = new XMLHttpRequest();
            request.onload = function() {
                var first_run = latest_feeds_items.length === 0;
                var current_unread = false;
                var response = JSON.parse(this.responseText);
                var last_items_timestamps = response.last_items_timestamps;

                for (var i = 0; i < last_items_timestamps.length; i++) {
                    var current_feed = last_items_timestamps[i];
                    var feed_id = current_feed.feed_id;

                    if (! latest_feeds_items.hasOwnProperty(feed_id) || current_feed.updated > latest_feeds_items[feed_id]) {
                        latest_feeds_items[feed_id] = current_feed.updated;
                        current_unread = true;
                    }
                }

                Miniflux.App.Log('first_run: ' + first_run + ', current_unread: ' + current_unread + ', response.nbUnread: ' + response.nbUnread + ', nbUnreadItems: ' + nbUnreadItems);

                if (! document.hidden && (response.nb_unread_items !== nbUnreadItems || unreadItems)) {
                    Miniflux.App.Log('Counter changed! Updating unread counter.');
                    unreadItems = false;
                    nbUnreadItems = response.nb_unread_items;
                    updateCounters();
                }
                else if (document.hidden && ! first_run && current_unread) {
                    Miniflux.App.Log('New Unread! Updating pagetitle.');
                    unreadItems = true;
                    document.title = "↻ " + document.title;
                }
                else {
                    Miniflux.App.Log('No update.');
                }

                Miniflux.App.Log('unreadItems: ' + unreadItems);
            };

            request.open("GET", "?action=latest-feeds-items", true);
            request.send();
        }
    };

})();
