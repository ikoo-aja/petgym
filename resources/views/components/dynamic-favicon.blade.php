{{-- Component: Dynamic Favicon for Dark & Light theme detection --}}
<link rel="icon" id="dynamic-favicon" href="{{ asset('images/logo-dark.png') }}" type="image/png">
<link rel="icon" href="{{ asset('images/logo-white.png') }}" media="(prefers-color-scheme: dark)" type="image/png">
<link rel="icon" href="{{ asset('images/logo-dark.png') }}" media="(prefers-color-scheme: light)" type="image/png">

<script>
  (function() {
    var darkIcon = "{{ asset('images/logo-white.png') }}";
    var lightIcon = "{{ asset('images/logo-dark.png') }}";
    var matcher = window.matchMedia('(prefers-color-scheme: dark)');

    function setFavicon(isDark) {
      var fav = document.getElementById('dynamic-favicon');
      if (fav) {
        fav.href = isDark ? darkIcon : lightIcon;
      }
    }

    setFavicon(matcher.matches);

    if (matcher.addEventListener) {
      matcher.addEventListener('change', function(e) { setFavicon(e.matches); });
    } else if (matcher.addListener) {
      matcher.addListener(function(e) { setFavicon(e.matches); });
    }
  })();
</script>
