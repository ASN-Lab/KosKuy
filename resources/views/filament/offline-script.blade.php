{{--
    Registered on the Filament panel's HEAD_END hook (see AdminPanelProvider).

    The admin panel ships its own JS bundle and never loads resources/js/app.js,
    so the `offlineScreen` Alpine component and the network-error plumbing are
    re-declared here. It is a classic (non-module) script that runs before the
    panel's deferred Alpine bundle, so it is registered in time.
--}}
<script>
(function () {
    if (window.__koskuyOffline) {
        return;
    }
    window.__koskuyOffline = true;

    function notifyNetworkError() {
        window.dispatchEvent(new Event('network-error'));
    }

    if (window.fetch && !window.__koskuyFetchWrapped) {
        window.__koskuyFetchWrapped = true;
        var nativeFetch = window.fetch.bind(window);
        window.fetch = function () {
            return nativeFetch.apply(null, arguments).catch(function (error) {
                if (error instanceof TypeError) {
                    notifyNetworkError();
                }
                throw error;
            });
        };
    }

    if (window.axios && !window.__koskuyAxiosWrapped) {
        window.__koskuyAxiosWrapped = true;
        window.axios.interceptors.response.use(
            function (response) { return response; },
            function (error) {
                if (!error.response) {
                    notifyNetworkError();
                }
                return Promise.reject(error);
            }
        );
    }

    function offlineScreen() {
        return {
            online: navigator.onLine,
            dismissed: false,
            retrying: false,
            restored: false,
            _restoreTimer: null,

            init: function () {
                var self = this;
                window.addEventListener('offline', function () { self.goOffline(); });
                window.addEventListener('network-error', function () { self.goOffline(); });
                window.addEventListener('online', function () { self.verify(); });
                document.addEventListener('click', function (event) { self.guardNavigation(event); }, true);
                document.addEventListener('submit', function (event) { self.guardSubmission(event); }, true);

                if (!this.online) {
                    this.goOffline();
                }
            },

            goOffline: function () {
                this.online = false;
                this.dismissed = false;
                this.restored = false;
                clearTimeout(this._restoreTimer);
            },

            reopen: function () { this.dismissed = false; },

            dismiss: function () {
                if (!this.online) {
                    this.dismissed = true;
                }
            },

            guardNavigation: function (event) {
                if (this.online) {
                    return;
                }

                var link = event.target.closest('a[href]');
                if (!link) {
                    return;
                }

                var href = link.getAttribute('href') || '';
                var external = /^(https?:)?\/\//i.test(href) && href.indexOf(window.location.origin) !== 0;
                var skip = !href || href.charAt(0) === '#' || external
                    || /^(mailto:|tel:|javascript:)/i.test(href)
                    || link.target === '_blank' || link.hasAttribute('download');

                if (skip) {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();
                this.reopen();
            },

            guardSubmission: function (event) {
                if (this.online) {
                    return;
                }
                event.preventDefault();
                event.stopImmediatePropagation();
                this.reopen();
            },

            verify: function () {
                var self = this;
                fetch(window.location.href, { method: 'HEAD', cache: 'no-store' })
                    .then(function () { self.reconnected(); })
                    .catch(function () { self.goOffline(); });
            },

            reconnected: function () {
                var self = this;
                this.online = true;
                this.dismissed = false;
                this.restored = true;
                clearTimeout(this._restoreTimer);
                this._restoreTimer = setTimeout(function () { self.restored = false; }, 1600);
            },

            retry: function () {
                var self = this;
                this.retrying = true;
                fetch(window.location.href, { method: 'HEAD', cache: 'no-store' })
                    .then(function () { window.location.reload(); })
                    .catch(function () { self.retrying = false; self.goOffline(); });
            },
        };
    }

    function register() {
        window.Alpine.data('offlineScreen', offlineScreen);
    }

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', register);
    }
})();
</script>
