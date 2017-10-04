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
    if (document.getElementById(options.id)) return;
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
      src: cdn + 'static/go-back-to-worona.min.js',
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
    var html = '%3Chead%3E%3Cstyle%3E@keyframes%20progress%7Bfrom%7Bwidth%3A0%25%3B%7Dto%7Bwidth%3A80%25%3B%7D%7D%3C/style%3E%3C/head%3E%3Cbody%20style%3D%22height%3A100%25%3Bbackground%3A%23FDFDFD%3Bdisplay%3Aflex%3Bjustify-content%3Acenter%3Balign-items%3Acenter%3B%22%3E%3Cdiv%20style%3D%22animation%3A6s%20ease-out%201s%201%20forwards%20progress%3Bheight%3A2px%3Bbackground%3A%23000%3B%22%3E%3C/div%3E%3C/body%3E';
    document.write(unescape(html));

    var query = '?siteId=' + siteId + '&' + wpType + '=' + wpId;
    if (wpPage) query += '&paged=' + wpPage;

    var loadWorona = function() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            window.__worona_public_path__ = cdn;
            loadHtml(xhr.responseText);
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
            console.error('Error loading the injector on: ' + window.location.href, xhr.statusText);
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
