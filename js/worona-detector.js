if (history.pushState) {
    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?appId=DDHJKDnJWu3yx6AEF';
    window.history.pushState({path:newurl},'',newurl);
}

var md = new MobileDetect(window.navigator.userAgent);

if (md.mobile()) {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
          var newDoc = document.open("text/html", "replace");
          newDoc.write(xhr.responseText);
          newDoc.close();
      }
  }
  xhr.open('GET', 'http://localhost:3000/', true);
  xhr.send();

}
else {
  alert("NO ES MOVIL!");
}
