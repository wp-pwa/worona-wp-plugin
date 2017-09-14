// Uglify using "npx uglify-js injector.js --output injector.min.js --compress --mangle"

(function(document, window, navigator) {
  var isIpad = /ipad.*?OS (?![1-6]_|X)/i; // from iOS 7
  var isIphone = /ip(hone|od).*?OS (?![1-6]_|X)/i; // from iOS 7
  var isChromeMobile = /android (?![1-3]).*chrome\/[.0-9]* mobile/i; // from Android 4.4
  var isChromeTablet = /android (?![1-3]).*chrome\/[.0-9]* (?!mobile)/i; // from Android 4.4
  var isOldAndroidMobile = /android 4\.[0-3].* mobile/i; // from Android 4.1 to Android 4.4
  var isOldAndroidTablet = /android 4\.[0-3].* (?!mobile)/i; // from Android 4.1 to Android 4.4

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
    var id = options.id + (options.srcDev ? '-dev' : '-prod');
    if (document.getElementById(id)) return;
    var ref = document.getElementsByTagName(options.tag)[0];
    var js = document.createElement(options.tag);
    js.id = id;
    js.src = options.srcDev || options.srcProd;
    if (options.srcDev)
      js.onerror = function() {
        console.log('failed');
        options.srcDev = null;
        loadScript(options);
      };
    ref.parentNode.insertBefore(js, ref);
  };

  var loadHtml = function(html) {
    var newDoc = document.open('text/html', 'replace');
    newDoc.write(html);
    newDoc.close();
    document.body.scrollTop = 0;
  };

  var dev = typeof hostDev !== 'undefined';

  if (readCookie('woronaClassicVersion')) {
    var options = {
      tag: 'script',
      id: 'woronaClassic',
      srcProd: 'https://' + hostProd + '/static/go-back-to-worona.min.js',
    };
    if (dev) options.srcDev = 'https://' + hostDev + '/static/go-back-to-worona.min.js';
    loadScript(options);
  } else if (
    wpType !== 'none' &&
    !readCookie('woronaInjectorFailed') &&
    navigator &&
    isMobile(navigator.userAgent)
  ) {
    window.stop();
    document.write('<plaintext style="display:none">');

    var query = '?siteId=' + siteId + '&' + wpType + '=' + wpId;
    if (wpPage) query += '&paged=' + wpPage;

    var tryHostDev = function() {
      var devXhr = new XMLHttpRequest();
      devXhr.onreadystatechange = function() {
        if (devXhr.readyState === 4) {
          if (devXhr.status === 200) {
            loadHtml(devXhr.responseText);
          } else {
            tryHostProd();
          }
        }
      };
      devXhr.open('GET', 'https://' + hostDev + query, true);
      devXhr.send();
    };

    var tryHostProd = function() {
      var prodXhr = new XMLHttpRequest();
      prodXhr.onreadystatechange = function() {
        if (prodXhr.readyState === 4) {
          if (prodXhr.status === 200) {
            loadHtml(prodXhr.responseText);
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
                      error: prodXhr.statusText,
                    },
                  },
                },
              })
            );
            console.error(
              'Error loading the injector on: ' + window.location.href,
              prodXhr.statusText
            );
            setCookie('woronaInjectorFailed', 'true', 1);
            window.location.reload(true);
          }
        }
      };
      prodXhr.open('GET', 'https://' + hostProd + query, true);
      prodXhr.send();
    };

    if (dev) tryHostDev();
    else tryHostProd();
  }
})(document, window, navigator);
