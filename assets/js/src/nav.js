Miniflux.Nav = (function() {

    function scrollPageTo(item)
    {
        var clientHeight = pageYOffset + document.documentElement.clientHeight;
        var itemPosition = item.offsetTop + item.offsetHeight;

        if (clientHeight - itemPosition < 0 || clientHeight - item.offsetTop > document.documentElement.clientHeight) {
            window.scrollTo(0, item.offsetTop - 10);
        }
    }

    function findNextItem()
    {
        var items = document.getElementsByTagName("article");

        if (! document.getElementById("current-item")) {

            items[0].id = "current-item";
            scrollPageTo(items[0]);
        }
        else {

            for (var i = 0, ilen = items.length; i < ilen; i++) {

                if (items[i].id === "current-item") {

                    if (i + 1 < ilen) {
                        items[i].id = "item-" + items[i].getAttribute("data-item-id");

                        items[i + 1].id = "current-item";
                        scrollPageTo(items[i + 1]);
                    }

                    break;
                }
            }
        }
    }

    function findPreviousItem()
    {
        var items = document.getElementsByTagName("article");

        if (! document.getElementById("current-item")) {

            items[items.length - 1].id = "current-item";
            scrollPageTo(items[items.length - 1]);
        }
        else {

            for (var i = items.length - 1; i >= 0; i--) {

                if (items[i].id === "current-item") {

                    if (i - 1 >= 0) {
                        items[i].id = "item-" + items[i].getAttribute("data-item-id");
                        items[i - 1].id = "current-item";
                        scrollPageTo(items[i - 1]);
                    }

                    break;
                }
            }
        }
    }

    function isListing()
    {
        return !!document.getElementById("listing");
    }

    return {
        OpenNextPage: function() {
            var link = document.getElementById("next-page");
            if (link) link.click();
        },
        OpenPreviousPage: function() {
            var link = document.getElementById("previous-page");
            if (link) link.click();
        },
        SelectNextItem: function() {
            var link = document.getElementById("next-item");

            if (link) {
                link.click();
            }
            else if (isListing()) {
                findNextItem();
            }
        },
        SelectPreviousItem: function() {
            var link = document.getElementById("previous-item");

            if (link) {
                link.click();
            }
            else if (isListing()) {
                findPreviousItem();
            }
        },
        ShowHelp: function() {
            var help_layer = document.getElementById("help-layer");
            help_layer.removeAttribute("class");
        },
        CloseHelp: function() {
            var help_layer = document.getElementById("help-layer");
            help_layer.setAttribute("class", "hide");
        },
        ShowSearch: function() {
            document.getElementById("search-opener").setAttribute("class", "hide");
            document.getElementById("search-form").removeAttribute("class");
            document.getElementById("form-text").focus();
        },
        ToggleMenuMore: function () {
            var menu = document.getElementById("menu-more");

            if (menu.hasAttribute("class")) {
                menu.removeAttribute("class");
            } else {
                menu.setAttribute("class", "hide");
            }
        },
        IsListing: isListing
    };

})();
