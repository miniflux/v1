Miniflux.Item = (function() {

    var nbUnreadItems = function() {
        var navCounterElement = document.getElementById("nav-counter");
        
        if (navCounterElement) {
            counter = parseInt(navCounterElement.textContent, 10) || 0;
            return counter;
        }
    }();

    var nbPageItems = function() {
        var pageCounterElement = document.getElementById("page-counter");
        
        if (pageCounterElement) {
            counter = parseInt(pageCounterElement.textContent, 10) || 0;
            return counter;
        }
    }();

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
        
        nbUnreadItems--;
        updateCounters();
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
        
        nbUnreadItems++;
        updateCounters();
    }

    function hideItem(item)
    {
        if (Miniflux.Event.lastEventType !== "mouse") {
            Miniflux.Nav.SelectNextItem();
        }

        item.parentNode.removeChild(item);
        nbPageItems--;
    }

    function updateCounters()
    {
        // imitate special handling within miniflux
        if (nbPageItems  === 0) {
            window.location.reload();
        }

        var pageCounterElement = document.getElementById("page-counter");
        pageCounterElement.textContent = nbPageItems || '';
        
        var navCounterElement = document.getElementById("nav-counter");
        navCounterElement.textContent = nbUnreadItems || '';        

        // pagetitle depends on current page
        var sectionElement = document.querySelector("section.page");
        switch (sectionElement.getAttribute("data-item-page")) {
            case "unread":
                document.title = "Miniflux (" + nbUnreadItems + ")";
                break;
            case "feed-items":
                document.title = "(" + nbPageItems  + ") " + pageCounterElement.parentNode.firstChild.nodeValue;
                break;
            default:
                document.title = pageCounterElement.parentNode.firstChild.nodeValue + " (" + nbPageItems + ")";
                break;
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
                }
                else {
                    item.setAttribute("data-item-bookmark", value);

                    if (Miniflux.Nav.IsListing()) {
                        changeBookmarkLabel(item);
                    }
                    else {
                        var link = item.querySelector("a.bookmark-icon");
                        if (link && link.hasAttribute("data-reverse-title")) {
                            var title = link.getAttribute("title");

                            link.setAttribute("title", link.getAttribute("data-reverse-title"));
                            link.setAttribute("data-reverse-title", title);
                        }
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

                if (Miniflux.Event.lastEventType !== "mouse") {
                    link.click();
                }
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
        },
        ToggleRTLMode: function() {
            var tags = [
                "#current-item h1",
                "#item-content",
                "#listing #current-item h2",
                "#listing #current-item .preview"
            ];

            for (var i = 0; i < tags.length; i++) {
                var tag = document.querySelector(tags[i]);

                if (tag) {
                    tag.dir = tag.dir == "" ? "rtl" : "";
                }
            }
        }
    };

})();