Miniflux.Event = (function() {

    var queue = [];

    return {
        lastEventType: "",
        ListenMouseEvents: function() {

            document.onclick = function(e) {

                var action = e.target.getAttribute("data-action");

                if (action) {

                    Miniflux.Event.lastEventType = "mouse";

                    var currentItem = function () {
                        element = e.target;

                        while (element && element.parentNode) {
                            element = element.parentNode;
                            if (element.tagName && element.tagName.toLowerCase() === 'article') {
                                return element;
                            }
                        }

                        return null;
                    }();

                    switch (action) {
                        case 'refresh-all':
                            e.preventDefault();
                            Miniflux.Feed.UpdateAll();
                            break;
                        case 'refresh-feed':
                            e.preventDefault();
                            Miniflux.Feed.Update(e.target.getAttribute("data-feed-id"));
                            break;
                        case 'mark-read':
                            e.preventDefault();
                            Miniflux.Item.MarkAsRead(currentItem);
                            break;
                        case 'mark-unread':
                            e.preventDefault();
                            Miniflux.Item.MarkAsUnread(currentItem);
                            break;
                        case 'mark-removed':
                            e.preventDefault();
                            Miniflux.Item.MarkAsRemoved(currentItem);
                            break;
                        case 'bookmark':
                            e.preventDefault();
                            Miniflux.Item.SwitchBookmark(currentItem);
                            break;
                        case 'download-item':
                            e.preventDefault();
                            Miniflux.Item.DownloadContent(currentItem);
                            break;
                        case 'original-link':
                            e.preventDefault();
                            Miniflux.Item.OpenOriginal(currentItem);
                            break;
                        case 'mark-all-read':
                            e.preventDefault();
                            Miniflux.Item.MarkListingAsRead("?action=unread");
                            break;
                        case 'mark-feed-read':
                            e.preventDefault();
                            Miniflux.Item.MarkListingAsRead("?action=feed-items&feed_id=" + e.target.getAttribute("data-feed-id"));
                            break;
                    }
                }
            };
        },
        ListenKeyboardEvents: function() {

            document.onkeypress = function(e) {

                if (e.keyCode != 63 && (e.ctrlKey || e.shiftKey || e.altKey || e.metaKey)) {
                    return;
                }

                // Do not handle events when there is a focus in form fields
                var target = e.target || e.srcElement;
                if (target.tagName == 'INPUT' || target.tagName == 'TEXTAREA') {
                    return;
                }

                Miniflux.Event.lastEventType = "keyboard";

                queue.push(e.keyCode || e.which);

                if (queue[0] === 103) { // g

                    switch (queue[1]) {
                        case undefined:
                            break;
                        case 117: // u
                            window.location.href = "?action=unread";
                            queue = [];
                            break;
                        case 98: // b
                            window.location.href = "?action=bookmarks";
                            queue = [];
                            break;
                        case 104: // h
                            window.location.href = "?action=history";
                            queue = [];
                            break;
                        case 115: // s
                            window.location.href = "?action=feeds";
                            queue = [];
                            break;
                        case 112: // p
                            window.location.href = "?action=config";
                            queue = [];
                            break;
                        default:
                            queue = [];
                            break;
                    }
                }
                else {

                    queue = [];

                    var currentItem = function () {
                        return document.getElementById("current-item");
                    }();

                    switch (e.keyCode || e.which) {
                        case 100: // d
                            Miniflux.Item.DownloadContent(currentItem);
                            break;
                        case 112: // p
                        case 107: // k
                            Miniflux.Nav.SelectPreviousItem();
                            break;
                        case 110: // n
                        case 106: // j
                            Miniflux.Nav.SelectNextItem();
                            break;
                        case 118: // v
                            Miniflux.Item.OpenOriginal(currentItem);
                            break;
                        case 111: // o
                            Miniflux.Item.Show(currentItem);
                            break;
                        case 109: // m
                            Miniflux.Item.SwitchStatus(currentItem);
                            break;
                        case 102: // f
                            Miniflux.Item.SwitchBookmark(currentItem);
                            break;
                        case 104: // h
                            Miniflux.Nav.OpenPreviousPage();
                            break
                        case 108: // l
                            Miniflux.Nav.OpenNextPage();
                            break;
                        case 114: // r
                            Miniflux.Feed.UpdateAll();
                            break;
                        case 63: // ?
                            Miniflux.Nav.ShowHelp();
                            break;
                    }
                }
            }
        }
    };

})();
