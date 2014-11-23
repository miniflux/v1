Miniflux.Item = (function() {

    function getItemID(item)
    {
        item_id = item.getAttribute("data-item-id");
        return item_id;
    }

    function changeLabel(link)
    {
        if (link && link.hasAttribute("data-reverse-label")) {
            var content = link.innerHTML;
            link.innerHTML = link.getAttribute("data-reverse-label");
            link.setAttribute("data-reverse-label", content);
        }
    }

    function changeBookmarkLabel(item)
    {
        var link = item.querySelector("a.bookmark");
        changeLabel(link);
    }

    function changeStatusLabel(item)
    {
        var link = item.querySelector("a.mark");
        changeLabel(link);
    }

    function showItemAsRead(item)
    {
        if (item.getAttribute("data-hide")) {
            hideItem(item);
        }
        else {
            item.setAttribute("data-item-status", "read");
            changeStatusLabel(item);

            // Change action
            var link = item.querySelector("a.mark");
            if (link) link.setAttribute("data-action", "mark-unread");
        }
    }

    function showItemAsUnread(item)
    {
        if (item.getAttribute("data-hide")) {
            hideItem(item);
        }
        else {
            item.setAttribute("data-item-status", "unread");
            changeStatusLabel(item);

            // Change action
            var link = item.querySelector("a.mark");
            if (link) link.setAttribute("data-action", "mark-read");
        }
    }

    function hideItem(item)
    {
        if (Miniflux.Event.lastEventType !== "mouse") {
            Miniflux.Nav.SelectNextItem();
        }

        item.parentNode.removeChild(item);
        var pageCounter = document.getElementById("page-counter");

        if (pageCounter) {
            var source = item.getAttribute("data-item-page");
            var counter = parseInt(pageCounter.textContent, 10) - 1;
            var articles = document.getElementsByTagName("article");
            
            if (counter === 0 || articles.length === 0) {
                window.location = location.href;
            }

            pageCounter.textContent = counter;

            switch (source) {
                case "unread":
                    document.title = "Miniflux (" + counter + ")";
                    document.getElementById("nav-counter").textContent = counter;
                    break;
                case "feed-items":
                    document.title = "(" + counter + ") " + pageCounter.parentNode.firstChild.nodeValue;
                    break;
                default:
                    document.title = pageCounter.parentNode.firstChild.nodeValue + " (" + counter + ")";
            }
        }
    }

    function markAsRead(item)
    {
        var item_id = getItemID(item);
        var request = new XMLHttpRequest();
        
        request.onload = function() {
            if (Miniflux.Nav.IsListing()) showItemAsRead(item);
        };
        request.open("POST", "?action=mark-item-read&id=" + item_id, true);
        request.send();
    }

    function markAsUnread(item)
    {
        var item_id = getItemID(item);
        var request = new XMLHttpRequest();
        
        request.onload = function() {
            if (Miniflux.Nav.IsListing()) showItemAsUnread(item);
        };
        request.open("POST", "?action=mark-item-unread&id=" + item_id, true);
        request.send();
    }

    function markAsRemoved(item)
    {
        var item_id = getItemID(item);
        var request = new XMLHttpRequest();
        
        request.onload = function() {
            if (Miniflux.Nav.IsListing()) hideItem(item);
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
                if (Miniflux.Nav.IsListing() && item.getAttribute("data-item-page") === "bookmarks") {
                    hideItem(item);
                }
                else {
                    item.setAttribute("data-item-bookmark", value);

                    if (Miniflux.Nav.IsListing()) {
                        changeBookmarkLabel(item);
                    }
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
            if (link) link.click();
        },
        OpenOriginal: function(item) {
            var link = item.querySelector("a.original");

            if (link) {
                if (item.getAttribute("data-item-status") === "unread") markAsRead(item);
                link.removeAttribute("data-action");
                link.click();
            }
        },
        DownloadContent: function(item) {
            var container = document.getElementById("download-item");
            if (! container) return;

            var item_id = getItemID(item);
            var message = container.getAttribute("data-before-message");

            var span = document.createElement("span");
            span.appendChild(document.createTextNode("☀"));
            span.className = "loading-icon";

            container.innerHTML = "";
            container.className = "downloading";
            container.appendChild(span);
            container.appendChild(document.createTextNode(" " + message));

            var icon_interval = setInterval(Miniflux.App.BlinkIcon, 250);

            var request = new XMLHttpRequest();

            request.onload = function() {

                var response = JSON.parse(request.responseText);
                clearInterval(icon_interval);

                if (response.result) {

                    var content = document.getElementById("item-content");
                    if (content) content.innerHTML = response.content;

                    if (container) {
                        var message = container.getAttribute("data-after-message");
                        container.innerHTML = "";
                        container.appendChild(document.createTextNode(" " + message));
                    }
                }
                else {

                    if (container) {
                        var message = container.getAttribute("data-failure-message");
                        container.innerHTML = "";
                        container.appendChild(document.createTextNode(" " + message));
                    }
                }
            };

            request.open("POST", "?action=download-item&id=" + item_id, true);
            request.send();
        },
        MarkListingAsRead: function(redirect) {
            var articles = document.getElementsByTagName("article");
            var listing = [];

            for (var i = 0, ilen = articles.length; i < ilen; i++) {
                listing.push(getItemID(articles[i]));
            }

            var request = new XMLHttpRequest();

            request.onload = function() {
                window.location.href = redirect;
            };

            request.open("POST", "?action=mark-items-as-read", true);
            request.send(JSON.stringify(listing));
        }
    };

})();