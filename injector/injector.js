// Uglify using "npx uglify-js injector.js --output injector.min.js --compress --mangle"

(function(document, window, navigator) {
  var isIpad = /ipad.*?OS (?![1-6]_|X)/i; // from iOS 7
  var isIphone = /ip(hone|od).*?OS (?![1-6]_|X)/i; // from iOS 7
  var isChromeMobile = /android (?![1-3]).*chrome\/[.0-9]* mobile/i; // from Android 4.4
  var isChromeTablet = /android (?![1-3]).*chrome\/[.0-9]* (?!mobile)/i; // from Android 4.4
  var isOldAndroidMobile = /android 4\.[0-3].* mobile/i; // from Android 4.1 to Android 4.4
  var isOldAndroidTablet = /android 4\.[0-3].* (?!mobile)/i; // from Android 4.1 to Android 4.4

  ssr = ssr.replace(/\/$/g, '') + '/';
  cdn = cdn.replace(/\/$/g, '') + '/';

  var isMobile = function(ua) {
    return isIphone.test(ua) || isChromeMobile.test(ua) || isOldAndroidMobile.test(ua);
  };
  var isTablet = function(ua) {
    return isIpad.test(ua) || isChromeTablet.test(ua) || isOldAndroidTablet.test(ua);
  };

  var setCookie = function(name, value, minutes) {
    var expires = '';
    if (minutes) {
      var d = new Date();
      d.setTime(d.getTime() + minutes * 60 * 1000);
      expires = 'expires=' + d.toUTCString() + ';';
    }
    document.cookie = name + '=' + value + ';' + expires + 'path=/';
  };

  var readCookie = function(name) {
    var nameEQ = name + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  };

  var loadScript = function(options) {
    if (document.getElementById(id)) return;
    var ref = document.getElementsByTagName(options.tag)[0];
    var js = document.createElement(options.tag);
    js.id = options.id;
    js.src = options.src;
    ref.parentNode.insertBefore(js, ref);
  };

  var loadHtml = function(html) {
    var newDoc = document.open('text/html', 'replace');
    newDoc.write(html);
    newDoc.close();
    document.body.scrollTop = 0;
  };

  if (readCookie('woronaClassicVersion')) {
    var options = {
      tag: 'script',
      id: 'woronaClassic',
      src: cdn + '/static/go-back-to-worona.min.js',
    };
    loadScript(options);
  } else if (
    siteId !== 'none' &&
    wpType !== 'none' &&
    !readCookie('woronaInjectorFailed') &&
    navigator &&
    isMobile(navigator.userAgent)
  ) {
    window.stop();
    document.write('<head><style>@keyframes progress{from{width:0%;}to{width:80%;}}</style></head><body style="height:100%;background:#FDFDFD;display:flex;justify-content:center;align-items:center;"><div style="animation:6s ease-out 1s progress;height:2px;background:#000;"></div></body>');

    var query = '?siteId=' + siteId + '&' + wpType + '=' + wpId + '&cdn=' + cdn;
    if (wpPage) query += '&paged=' + wpPage;

    var loadWorona = function() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            loadHtml(xhr.responseText);
            window.__worona_public_path__ = cdn;
          } else {
            var rollbarXhr = new XMLHttpRequest();
            rollbarXhr.open('POST', 'https://api.rollbar.com/api/1/item/', true);
            rollbarXhr.send(
              JSON.stringify({
                access_token: 'd64fbebfade643439dad144ccb8c3635',
                data: {
                  environment: 'injector',
                  platform: 'browser',
                  body: {
                    message: {
                      body: 'Error loading the injector on: ' + window.location.href,
                      error: xhr.statusText,
                    },
                  },
                },
              })
            );
            console.error(
              'Error loading the injector on: ' + window.location.href,
              xhr.statusText
            );
            setCookie('woronaInjectorFailed', 'true', 1);
            window.location.reload(true);
          }
        }
      };
      xhr.open('GET', ssr + query, true);
      xhr.send();
    };

    loadWorona();
  }
})(document, window, navigator);
