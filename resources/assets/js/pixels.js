
window.fbAsyncInit = function() {
  FB.init({
    appId            : document.getElementById('facebookAppId').value,
    autoLogAppEvents : true,
    xfbml            : true,
    version          : 'v3.1'
  });
};

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/pt_BR/sdk/xfbml.customerchat.js#xfbml=1&version=v2.12&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

(adsbygoogle = window.adsbygoogle || []).push({
  google_ad_client: "ca-pub-1838850455744590",
  enable_page_level_ads: true
});

window.onload = function() {
  document.getElementById('loading-store').remove();
  document.getElementById('store').style.display = 'block';
};
