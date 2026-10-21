<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install {{ $restaurant->name }}</title>
@include('partials.pwa-head', ['restaurant' => $restaurant])
<style>body{font-family:system-ui,sans-serif;margin:0;background:#f8fafc;color:#334155;padding:24px 16px}main{max-width:480px;margin:0 auto;background:#fff;border-radius:24px;padding:24px;box-shadow:0 1px 3px #0001}h1{margin-top:0}li{margin-bottom:8px}button{padding:10px 24px;border:0;border-radius:12px;background:#059669;color:#fff;font-weight:700}</style></head>
<body><main>
<h1>Install {{ $restaurant->name }}</h1>
<p>Add this menu to your home screen for one-tap access.</p>
<button id="install" style="display:none">Install app</button>
<ol>
<li><b>Android / Chrome:</b> tap the menu (three dots), then "Install app" or "Add to Home screen".</li>
<li><b>iPhone / Safari:</b> tap the Share icon, then "Add to Home Screen".</li>
<li><b>Desktop Chrome / Edge:</b> click the install icon in the address bar.</li>
</ol>
<p style="font-size:13px;color:#64748b">Offline mode shows a friendly offline page; live menu and ordering need a connection.</p>
</main>
<script>
let deferred; const b = document.getElementById('install');
window.addEventListener('beforeinstallprompt', e => { e.preventDefault(); deferred = e; b.style.display = 'inline-block'; });
b.addEventListener('click', () => { if (deferred) { deferred.prompt(); } });
</script></body></html>
