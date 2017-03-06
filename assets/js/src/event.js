Miniflux.Event = (function() {

    var queue = [];

    function isEventIgnored(e)
    {
        if (e.keyCode !== 63 && e.which !== 63 && (e.ctrlKey || e.altKey || e.metaKey)) {
            return true;
        }

        // Do not handle events when there is a focus in form fields
        var target = e.target || e.srcElement;
        return target.tagName === 'INPUT' || target.tagName === 'TEXTAREA';
    }

    return {
        lastEventType: "",
        ListenMouseEvents: function() {

            document.onclick = function(e) {
                if (e.target.hasAttribute("data-action") && e.target.className !== 'original') {
                    e.preventDefault();
                }
            };

            document.onmouseup = function(e) {
                // Ignore right mouse button (context menu)
                if (e.button === 2) {
                    return;
                }

                // Auto-select input content
                if (e.target.nodeName === "INPUT" && e.target.className === "auto-select") {
                    e.target.select();
                    return;
                }

                // Application actions
                var action = e.target.getAttribute("data-action");

                if (action) {

                    Miniflux.Event.lastEventType = "mouse";

                    var currentItem = function () {
                        var element = e.target;

                        while (element && element.parentNode) {
                            element = element.parentNode;
                            if (element.tagName && element.tagName.toLowerCase() === 'article') {
                                return element;
                            }
                        }
                    }();

                    switch (action) {
                        case 'refresh-all':
                            Miniflux.Feed.UpdateAll(e.target.getAttribute("data-concurrent-requests"));
                            break;
                        case 'refresh-feed':
                            if (currentItem) {
                                Miniflux.Feed.Update(currentItem);
                            }
                            break;
                        case 'mark-read':
                            if (currentItem) {
                                Miniflux.Item.MarkAsRead(currentItem);
                            }
                            break;
                        case 'mark-unread':
                            if (currentItem) {
                                Miniflux.Item.MarkAsUnread(currentItem);
                            }
                            break;
                        case 'mark-removed':
                            if (currentItem) {
                                Miniflux.Item.MarkAsRemoved(currentItem);
                            }
                            break;
                        case 'bookmark':
                            if (currentItem) {
                                Miniflux.Item.SwitchBookmark(currentItem);
                            }
                            break;
                        case 'download-item':
                            if (currentItem) {
                                Miniflux.Item.DownloadContent(currentItem);
                            }
                            break;
                        case 'mark-feed-read':
                            var feed_id = document.getElementById('listing').getAttribute('data-feed-id');
                            Miniflux.Item.MarkFeedAsRead(feed_id);
                            break;
                        case 'close-help':
                            Miniflux.Nav.CloseHelp();
                            break;
                        case 'show-search':
                            Miniflux.Nav.ShowSearch();
                            break;
                       case 'toggle-menu-more':
                            Miniflux.Nav.ToggleMenuMore();
                            break;
                    }
                }
            };
        },
        ListenKeyboardEvents: function() {

            document.onkeypress = function(e) {

                if (isEventIgnored(e)) {
                    return;
                }

                Miniflux.Event.lastEventType = "keyboard";

                queue.push(e.key || e.which);

                if (queue[0] === 'g' || queue[0] === 103) {

                    switch (queue[1]) {
                        case undefined:
                            break;
                        case 'u':
                        case 117:
                            window.location.href = "?action=unread";
                            queue = [];
                            break;
                        case 'b':
                        case 98:
                            window.location.href = "?action=bookmarks";
                            queue = [];
                            break;
                        case 'h':
                        case 104:
                            window.location.href = "?action=history";
                            queue = [];
                            break;
                        case 's':
                        case 115:
                            window.location.href = "?action=feeds";
                            queue = [];
                            break;
                        case 'p':
                        case 112:
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

                    switch (e.key || e.which) {
                        case 'A':
                        case 65:
                            if (e.shiftKey) {
                                window.location.href='?action=mark-all-read';
                            }
                            break;
                        case 'd':
                        case 100:
                            if (currentItem) {
                                Miniflux.Item.DownloadContent(currentItem);
                            }
                            break;
                        case 'p':
                        case 112:
                        case 'k':
                        case 107:
                            Miniflux.Nav.SelectPreviousItem();
                            break;
                        case 'n':
                        case 110:
                        case 'j':
                        case 106:
                            Miniflux.Nav.SelectNextItem();
                            break;
                        case 'v':
                        case 118:
                            if (currentItem) {
                                Miniflux.Item.OpenOriginal(currentItem);

                                if (document.querySelectorAll('article.item') > 1) {
                                    Miniflux.Nav.SelectNextItem();
                                }
                            }
                            break;
                        case 'o':
                        case 111:
                            if (currentItem) {
                                Miniflux.Item.Show(currentItem);
                            }
                            break;
                        case 'm':
                        case 109:
                            if (currentItem) {
                                Miniflux.Item.SwitchStatus(currentItem);
                            }
                            break;
                        case 'f':
                        case 102:
                            if (currentItem) {
                                Miniflux.Item.SwitchBookmark(currentItem);
                            }
                            break;
                        case 'h':
                        case 104:
                            Miniflux.Nav.OpenPreviousPage();
                            break;
                        case 'l':
                        case 108:
                            Miniflux.Nav.OpenNextPage();
                            break;
                        case 'r':
                        case 114:
                            Miniflux.Feed.UpdateAll();
                            break;
                        case '?':
                        case 63:
                            Miniflux.Nav.ShowHelp();
                            break;
                        case 'Q':
                        case 81:  // Q
                        case 'q':
                        case 113: // q
                            Miniflux.Nav.CloseHelp();
                            break;
                        case 'z':
                        case 122:
                            Miniflux.Item.ToggleRTLMode();
                            break;
                    }
                }
            };

            document.onkeydown = function(e) {

                if (isEventIgnored(e)) {
                    return;
                }

                Miniflux.Event.lastEventType = "keyboard";

                switch (e.key || e.which) {
                    case "ArrowLeft":
                    case "Left":
                    case 37:
                        Miniflux.Nav.SelectPreviousItem();
                        break;
                    case "ArrowRight":
                    case "Right":
                    case 39:
                        Miniflux.Nav.SelectNextItem();
                        break;
                    case '/':
                    case 191:
                        e.preventDefault();
                        e.stopPropagation();
                        Miniflux.Nav.ShowSearch();
                        break;
                }
            };
        },
        ListenVisibilityEvents: function() {
            document.addEventListener('visibilitychange', function() {
                Miniflux.App.Log('document.visibilityState: ' + document.visibilityState);

                if (!document.hidden && Miniflux.Item.hasNewUnread()) {
                    Miniflux.App.Log('Need to update the unread counter with fresh values from the database');
                    Miniflux.Item.CheckForUpdates();
                }
            });
        },
        ListenTouchEvents: function() {
            var touches = null;
            var resetTouch = function () {
              if (touches && touches.element) {
                  touches.element.style.opacity = 1;
                  touches.element.style.transform = "";
              }
              touches = {
                "touchstart": {"x":-1, "y":-1},
                "touchmove" : {"x":-1, "y":-1},
                "touchend"  : false,
                "direction" : "undetermined",
                "swipestarted" : false,
                "element" : null
              };
            };
            var horizontalSwipe = function () {
              if((touches.touchstart.x > -1 && touches.touchmove.x > -1 &&
                (Math.abs(touches.touchmove.x - touches.touchstart.x) > 30 || touches.swipestarted) &&
                 Math.abs(touches.touchmove.y - touches.touchstart.y) < 75)) {
                     touches.swipestarted = true;
                     return touches.touchmove.x - touches.touchstart.x;
              }
              return 0;
            };
            var closest = function(el, fn) {
                return el && (fn(el) ? el : closest(el.parentNode, fn));
            };
            var getTouchElement = function() {
              return touches.element ? touches.element :
               closest(document.elementFromPoint(touches.touchstart.x, touches.touchstart.y),
                 function(el) {
                 return el.tagName === 'ARTICLE';
               });
            };
            var drawElement = function(){
              if(touches &&
                 (touches.touchend === true || touches.touchstart.x == -1)) {
                return;
              }
              if(touches.element === null) {
                touches.element = getTouchElement();
              }
              var swipedistance = horizontalSwipe();
              if(swipedistance !== 0) {
                  var element = getTouchElement();
                  if(!element) {resetTouch(); return;}

                  var distance = Math.abs(swipedistance);
                  touches.element.style.opacity = 1 -
                  (distance > 75 ? 0.9 : distance / 75 * 0.9);

                  var tx = swipedistance > 75 ? 75 :
                    (swipedistance < -75 ? -75 : swipedistance);
                  touches.element.style.transform = "translateX("+tx+"px)";
                  touches.element = element;
              }
              window.requestAnimationFrame(drawElement);
            };
            var touchHandler = function (e) {
                if (typeof e.touches != 'undefined' && e.touches.length <= 1) {
                    Miniflux.Event.lastEventType = "touch";
                    var touch = e.touches[0];
                    var swipedistance = null;
                    var element = null;
                    switch (e.type) {
                      case 'touchstart':
                          resetTouch();
                          touches[e.type].x = touch.clientX;
                          touches[e.type].y = touch.clientY;
                          drawElement();
                          break;
                      case 'touchmove':
                          touches[e.type].x = touch.clientX;
                          touches[e.type].y = touch.clientY;
                          break;
                      case 'touchend':
                          touches[e.type] = true;
                          element = getTouchElement();
                          swipedistance = horizontalSwipe();
                          if(swipedistance > 75 || swipedistance < -75) {
                              if (element) {
                                  Miniflux.Item.MarkAsRead(element);
                              }
                              if(!element.getAttribute("data-hide")){
                                  resetTouch();
                              }
                          } else {
                            resetTouch();
                          }
                          break;
                      case 'touchcancel':
                          resetTouch();
                          break;
                      default:
                          break;
                    }
                } else {
                  resetTouch();
                }

            };

            resetTouch();
            document.addEventListener('touchstart', touchHandler, false);
            document.addEventListener('touchmove', touchHandler, false);
            document.addEventListener('touchend', touchHandler, false);
            document.addEventListener('touchcancel', touchHandler, false);
        }
    };
})();
