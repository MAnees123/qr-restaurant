@extends('layouts.guest')
@section('title', 'Login - QR Restaurant')

@section('content')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>

<style>
    /* ── Reset & Base ── */
    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
      background: #111;
      margin: 0;
      padding: 0;
    }

    /* ── Background ── */
    .bg-scene {
      position: fixed;
      inset: 0;
      z-index: 0;
      overflow: hidden;
      background-image: url('{{ asset("images/login-bg.png") }}');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
    .bg-scene::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(0,0,0,0.45) 0%, rgba(20,10,5,0.35) 100%);
      z-index: 1;
    }

    /* ── Glassmorphism Card ── */
    .glass-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 520px;
      /* True glass: very low opacity white so background bleeds through */
      background: rgba(255, 255, 255, 0.10);
      backdrop-filter: blur(28px) saturate(1.8) brightness(1.08);
      -webkit-backdrop-filter: blur(28px) saturate(1.8) brightness(1.08);
      border-radius: 28px;
      padding: 48px 44px 40px;
      /* Multi-layer glow border */
      border: 1px solid rgba(255,255,255,0.28);
      box-shadow:
        0 0 0 1px rgba(255,180,80,0.10),
        0 8px 32px rgba(0,0,0,0.45),
        0 2px 8px rgba(0,0,0,0.30),
        inset 0 1px 0 rgba(255,255,255,0.35),
        inset 0 -1px 0 rgba(255,255,255,0.08);
      animation: slideUp 0.65s cubic-bezier(0.22,1,0.36,1) both;
      overflow: hidden;
    }
    .glass-card::before {
      content: '';
      position: absolute;
      top: -60px; left: -60px;
      width: 260px; height: 180px;
      background: linear-gradient(135deg, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 60%);
      border-radius: 50%;
      pointer-events: none;
      z-index: 0;
    }
    .glass-card > * { position: relative; z-index: 1; }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(40px) scale(0.97); }
      to   { opacity: 1; transform: translateY(0)   scale(1); }
    }

    /* ── Logo Icon ── */
    .logo-wrap {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg, #ff8c42 0%, #e6450f 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px;
      box-shadow: 0 6px 20px rgba(230,69,15,0.45);
      animation: popIn 0.7s 0.25s cubic-bezier(0.22,1,0.36,1) both;
    }
    @keyframes popIn {
      from { opacity:0; transform: scale(0.5); }
      to   { opacity:1; transform: scale(1); }
    }
    .logo-wrap i { font-size: 30px; color: #fff; }

    /* ── Title ── */
    .title-main {
      font-size: 2.1rem;
      font-weight: 800;
      text-align: center;
      background: linear-gradient(90deg, #ff6b35 20%, #ffd166 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1.15;
      letter-spacing: -0.5px;
      filter: drop-shadow(0 2px 12px rgba(255,140,50,0.35));
    }
    .subtitle {
      color: rgba(255,255,255,0.65);
      font-size: 0.93rem;
      font-weight: 400;
      text-align: center;
      margin-top: 6px;
      margin-bottom: 28px;
    }

    /* ── Select Role ── */
    .role-label {
      font-size: 0.78rem;
      font-weight: 600;
      color: rgba(255,255,255,0.70);
      letter-spacing: 0.3px;
      margin-bottom: 6px;
      display: block;
    }
    .role-selector {
      width: 100%;
      border: 1px solid rgba(255,255,255,0.22);
      border-radius: 14px;
      padding: 12px 16px;
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      position: relative;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      user-select: none;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), 0 2px 8px rgba(0,0,0,0.18);
    }
    .role-selector:hover {
      border-color: rgba(255,160,60,0.55);
      background: rgba(255,255,255,0.13);
      box-shadow: 0 0 0 3px rgba(255,140,50,0.12), inset 0 1px 0 rgba(255,255,255,0.18);
    }
    .role-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg,#e63911,#f5a623);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 2px 8px rgba(230,69,15,0.4);
    }
    .role-avatar i { font-size: 16px; color:#fff; }
    .role-text { flex: 1; }
    .role-name  { font-weight: 700; font-size: 0.93rem; color: rgba(255,255,255,0.95); line-height: 1.2; }
    .role-desc  { font-size: 0.76rem; color: rgba(255,255,255,0.50); }
    .role-caret { color: rgba(255,255,255,0.50); font-size: 1rem; }

    /* Dropdown */
    .role-dropdown {
      display: none;
      position: absolute;
      top: calc(100% + 8px);
      left: 0; right: 0;
      background: rgba(20,12,6,0.85);
      backdrop-filter: blur(24px) saturate(1.5);
      -webkit-backdrop-filter: blur(24px) saturate(1.5);
      border: 1px solid rgba(255,255,255,0.18);
      border-radius: 14px;
      overflow: hidden;
      z-index: 50;
      box-shadow: 0 16px 40px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.1);
    }
    .role-dropdown.open { display: block; animation: fadeDown 0.22s ease; }
    @keyframes fadeDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

    .role-option {
      display: flex; align-items: center; gap: 12px;
      padding: 11px 16px;
      cursor: pointer; transition: background 0.15s;
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .role-option:last-child { border-bottom: none; }
    .role-option:hover { background: rgba(255,255,255,0.10); }
    .role-option.active { background: rgba(255,140,50,0.14); }
    .role-option .role-name  { color: rgba(255,255,255,0.92); }
    .role-option .role-desc  { color: rgba(255,255,255,0.45); }

    /* ── Floating Label Inputs ── */
    .input-group-float {
      position: relative;
      margin-top: 20px;
    }
    .input-group-float input {
      width: 100%;
      padding: 18px 48px 8px 48px;
      border: 1px solid rgba(255,255,255,0.22);
      border-radius: 14px;
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      font-family: 'Poppins', sans-serif;
      font-size: 0.9rem;
      color: rgba(255,255,255,0.95);
      outline: none;
      transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
      appearance: none;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.12), 0 2px 8px rgba(0,0,0,0.18);
    }
    .input-group-float input::placeholder { color: transparent; }
    .input-group-float input:focus {
      border-color: rgba(255,160,60,0.65);
      box-shadow: 0 0 0 3.5px rgba(255,140,50,0.18), inset 0 1px 0 rgba(255,255,255,0.18);
      background: rgba(255,255,255,0.13);
    }
    .input-group-float label {
      position: absolute;
      left: 48px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 0.88rem;
      color: rgba(255,255,255,0.50);
      pointer-events: none;
      transition: all 0.2s ease;
      background: transparent;
    }
    .input-group-float input:focus ~ label,
    .input-group-float input:not(:placeholder-shown) ~ label {
      top: 10px;
      transform: translateY(0);
      font-size: 0.68rem;
      font-weight: 600;
      color: rgba(255,180,80,0.95);
      letter-spacing: 0.2px;
    }
    .input-group-float .required-star { color: #ff6b35; }

    .input-icon-left {
      position: absolute; left: 14px; top: 50%;
      transform: translateY(-50%);
      font-size: 1.05rem; color: rgba(255,160,60,0.85);
      pointer-events: none;
    }
    .input-icon-right {
      position: absolute; right: 14px; top: 50%;
      transform: translateY(-50%);
      color: rgba(255,255,255,0.45);
      cursor: pointer; transition: color 0.2s;
      background: none; border: none;
      display: flex; align-items: center; justify-content: center;
      padding: 0;
    }
    .input-icon-right:hover { color: rgba(255,180,80,0.95); }

    /* ── Forgot ── */
    .forgot-link {
      display: block; text-align: right;
      color: rgba(255,180,80,0.85); font-size: 0.82rem; font-weight: 600;
      text-decoration: none; margin-top: 10px;
      transition: color 0.2s;
    }
    .forgot-link:hover { color: #ff6b35; }

    /* ── Submit Button ── */
    .btn-login {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      width: 100%; margin-top: 26px;
      padding: 14px;
      border: none; border-radius: 14px;
      background: linear-gradient(90deg, #e63911 0%, #f5a623 100%);
      color: #fff; font-family: 'Poppins', sans-serif;
      font-size: 1rem; font-weight: 700; letter-spacing: 0.3px;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(230,57,17,0.45), 0 1px 0 rgba(255,255,255,0.15) inset;
      transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
      position: relative; overflow: hidden;
    }
    .btn-login::before {
      content:''; position:absolute; inset:0;
      background: linear-gradient(90deg,transparent 0%,rgba(255,255,255,0.22) 50%,transparent 100%);
      transform: translateX(-100%);
      transition: transform 0.45s ease;
    }
    .btn-login:hover::before { transform: translateX(100%); }
    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(230,57,17,0.50); color: #fff; }
    .btn-login:active { transform: translateY(0); }
    .btn-login.loading i { animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Divider / Register ── */
    .divider { display:flex; align-items:center; gap:10px; margin-top:22px; }
    .divider hr { flex:1; border:none; border-top:1px solid rgba(255,255,255,0.15); }
    .divider span { font-size:0.78rem; color:rgba(255,255,255,0.40); white-space: nowrap; }

    .register-row { text-align:center; margin-top:14px; font-size:0.84rem; color:rgba(255,255,255,0.45); }
    .register-row a { color:rgba(255,180,80,0.90); font-weight:600; text-decoration:none; }
    .register-row a:hover { color: #ff6b35; }

    /* ── Toast ── */
    #toast {
      position: fixed; bottom: 28px; right: 28px; z-index: 9999;
      background: #111827; color: #fff;
      border-radius: 10px; padding: 12px 20px;
      font-size: 0.85rem; font-family: 'Poppins',sans-serif;
      box-shadow: 0 8px 24px rgba(0,0,0,0.22);
      display:none; animation: toastIn 0.3s ease;
    }
    @keyframes toastIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    #toast.error  { border-left: 4px solid #e63911; }
    #toast.success{ border-left: 4px solid #22c55e; }

    /* ── Responsive Adjustments ── */
    @media(max-width:540px){
            .glass-card { padding:28px 18px 24px; border-radius:14px; max-width:100%; margin:0 1rem; }
      .title-main { font-size:1.5rem; }
      .logo-wrap { width:56px; height:56px; margin-bottom:14px; }
      .role-selector, .input-group-float input, .btn-login { font-size:0.85rem; padding:12px 36px 6px; }
      .btn-login { padding:10px; }
    }
    @media(max-width:375px){
      .glass-card { padding:20px 12px 18px; }
      .title-main { font-size:1.3rem; }
      .logo-wrap { width:48px; height:48px; margin-bottom:10px; }
      .role-selector, .input-group-float input, .btn-login { font-size:0.78rem; padding:10px 30px 4px; }
      .btn-login { padding:8px; }
    }
</style>

<!-- Background -->
<div class="bg-scene"></div>

<!-- Glassmorphism Card -->
<div class="glass-card mx-3">

    <!-- Logo -->
    <div class="logo-wrap">
      <i class="bi bi-egg-fried"></i>
    </div>

    <!-- Title -->
    <h1 class="title-main">Restaurant System</h1>
    <p class="subtitle">Welcome back! Please sign in to continue</p>

    <!-- Form -->
    <form id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Role Selector -->
        <div style="position:relative;" class="mb-2">
          <span class="role-label">Select Role (Quick Load)</span>
          <div class="role-selector" id="roleSelector" onclick="toggleDropdown()">
            <div class="role-avatar"><i class="bi bi-shield-fill" id="roleIcon"></i></div>
            <div class="role-text">
              <div class="role-name" id="roleName">Super Admin</div>
              <div class="role-desc" id="roleDesc">Full system access</div>
            </div>
            <i class="bi bi-chevron-down role-caret" id="roleCaret"></i>
          </div>

          <div class="role-dropdown" id="roleDropdown">
            <div class="role-option active" onclick="selectRole(this,'Super Admin','Full system access','shield-fill')">
              <div class="role-avatar"><i class="bi bi-shield-fill"></i></div>
              <div class="role-text"><div class="role-name">Super Admin</div><div class="role-desc">Full system access</div></div>
            </div>
            <div class="role-option" onclick="selectRole(this,'Kitchen Staff','Kitchen & Orders management','fire')">
              <div class="role-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i class="bi bi-fire"></i></div>
              <div class="role-text"><div class="role-name">Kitchen Staff</div><div class="role-desc">Kitchen & Orders management</div></div>
            </div>
          </div>
        </div>

        <!-- Email -->
        <div class="input-group-float">
          <i class="bi bi-envelope-fill input-icon-left"></i>
          <input type="email" id="email" name="email" value="{{ old('email', 'admin@restaurant.com') }}" placeholder=" " autocomplete="email" required/>
          <label for="email">Email Address <span class="required-star">*</span></label>
        </div>

        <!-- Password -->
        <div class="input-group-float">
          <i class="bi bi-lock-fill input-icon-left"></i>
          <input type="password" id="password" name="password" value="password" placeholder=" " autocomplete="current-password" required/>
          <label for="password">Password <span class="required-star">*</span></label>
          <button class="input-icon-right" type="button" onclick="togglePassword()" id="eyeBtn" title="Toggle password">
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>

        <!-- Remember me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <label class="d-flex align-items-center text-xs cursor-pointer" style="color: rgba(255,255,255,0.6); font-size: 0.8rem; user-select: none;">
            <input type="checkbox" name="remember" class="me-2 rounded border-gray-300 text-amber-500 focus:ring-amber-500" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);">
            Remember me
          </label>
          
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-link m-0">
              Forgot Password?
            </a>
          @endif
        </div>

        <!-- Login Button -->
        <button type="submit" class="btn-login" id="loginBtn">
          <i class="bi bi-box-arrow-in-right" id="loginIcon"></i>
          <span id="loginText">Sign In</span>
        </button>
    </form>

    <!-- Divider -->
    <div class="divider">
      <hr/><span>Don't have an account?</span><hr/>
    </div>

    <div class="register-row">
      @if (Route::has('register'))
        <a href="{{ route('register') }}">
          Register here
        </a>
      @else
        <a href="#" onclick="showToast('Contact your system administrator to register.','success'); return false;">
          Register here
        </a>
      @endif
    </div>

</div>

<!-- Toast -->
<div id="toast"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ── Role Dropdown & Auto-fill ── */
    const iconColors = {
      'shield-fill': 'linear-gradient(135deg,#e63911,#f5a623)',
      'fire': 'linear-gradient(135deg,#f59e0b,#d97706)',
    };

    const roleCredentials = {
      'Super Admin': { email: 'admin@restaurant.com', password: 'password' },
      'Kitchen Staff': { email: 'kitchen@restaurant.com', password: 'password' }
    };

    function toggleDropdown() {
      const dd = document.getElementById('roleDropdown');
      const caret = document.getElementById('roleCaret');
      const open = dd.classList.toggle('open');
      caret.style.transform = open ? 'rotate(180deg)' : '';
      caret.style.transition = 'transform 0.25s';
    }

    function selectRole(el, name, desc, icon) {
      document.getElementById('roleName').textContent = name;
      document.getElementById('roleDesc').textContent = desc;
      const ico = document.getElementById('roleIcon');
      ico.className = 'bi bi-' + icon;
      ico.closest('.role-avatar').style.background = iconColors[icon] || iconColors['shield-fill'];
      document.querySelectorAll('.role-option').forEach(o => o.classList.remove('active'));
      el.classList.add('active');
      toggleDropdown();

      // Pre-fill credentials dynamically
      if (roleCredentials[name]) {
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        emailInput.value = roleCredentials[name].email;
        passInput.value = roleCredentials[name].password;
        
        // Trigger inputs check for float label animations
        emailInput.placeholder = "filled";
        passInput.placeholder = "filled";
      }
    }

    document.addEventListener('click', e => {
      if (!document.getElementById('roleSelector').contains(e.target) &&
          !document.getElementById('roleDropdown').contains(e.target)) {
        document.getElementById('roleDropdown').classList.remove('open');
        const caret = document.getElementById('roleCaret');
        if (caret) caret.style.transform = '';
      }
    });

    /* ── Password Toggle ── */
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
          <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
          <line x1="1" y1="1" x2="23" y2="23"/>`;
      } else {
        input.type = 'password';
        icon.innerHTML = `
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>`;
      }
    }

    /* ── Form Interception for Animations ── */
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const email = document.getElementById('email').value.trim();
      const pass  = document.getElementById('password').value;
      const btn   = document.getElementById('loginBtn');
      const ico   = document.getElementById('loginIcon');
      const txt   = document.getElementById('loginText');

      if (!email) { showToast('Please enter your email address.', 'error'); shakeFocus('email'); return; }
      if (!pass)  { showToast('Please enter your password.', 'error'); shakeFocus('password'); return; }

      // Loading state
      btn.disabled = true;
      ico.className = 'bi bi-arrow-repeat';
      txt.textContent = 'Signing in…';
      btn.classList.add('loading');

      // Submit form after animation delay
      setTimeout(() => {
        this.submit();
      }, 600);
    });

    /* ── Shake helper ── */
    function shakeFocus(id) {
      const el = document.getElementById(id);
      el.focus();
      el.closest('.input-group-float').animate([
        {transform:'translateX(-6px)'},{transform:'translateX(6px)'},
        {transform:'translateX(-4px)'},{transform:'translateX(4px)'},
        {transform:'translateX(0)'}
      ], { duration: 350, easing: 'ease-in-out' });
    }

    /* ── Toast ── */
    let toastTimer;
    function showToast(msg, type='success') {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.className = type;
      t.style.display = 'block';
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => { t.style.display = 'none'; }, 3500);
    }

    /* ── Check Inputs on Load & Input for Float Labels ── */
    document.addEventListener('DOMContentLoaded', () => {
      const inputs = document.querySelectorAll('.input-group-float input');
      inputs.forEach(input => {
        if (input.value.trim() !== "") {
          input.placeholder = "filled";
        } else {
          input.placeholder = " ";
        }
        
        input.addEventListener('input', () => {
          if (input.value.trim() !== "") {
            input.placeholder = "filled";
          } else {
            input.placeholder = " ";
          }
        });
      });

      // Show Laravel Validation Errors (if any) via Toast
      @if ($errors->any())
        showToast("{{ $errors->first() }}", 'error');
      @endif

      @if (session('status'))
        showToast("{{ session('status') }}", 'success');
      @endif
    });


</script>
@endsection
