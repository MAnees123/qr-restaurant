<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Offline - {{ $restaurant->name }}</title>
<style>body{font-family:system-ui,sans-serif;display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;background:#f8fafc;color:#334155;text-align:center;padding:16px}h1{margin:0 0 8px}button{margin-top:16px;padding:10px 24px;border:0;border-radius:12px;background:#059669;color:#fff;font-weight:700}</style></head>
<body><div><h1>You are offline</h1><p>{{ $restaurant->name }} needs an internet connection to show the live menu and take orders.</p><button onclick="location.reload()">Try again</button></div></body></html>
