var Miniflux = {};

Miniflux.App = (function() {

    return {
        Run: function() {
            Miniflux.Event.ListenKeyboardEvents();
            Miniflux.Event.ListenMouseEvents();
        }
    }

})();
