<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Locked — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            animation: fadeUp .4s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b22, #ef444422);
            border: 2px solid #f59e0b44;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
            color: #f59e0b;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: .5rem;
        }

        .badge {
            display: inline-block;
            background: #f59e0b22;
            color: #fbbf24;
            border: 1px solid #f59e0b44;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            padding: .25rem .75rem;
            margin-bottom: 1.25rem;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        p {
            color: #94a3b8;
            font-size: .95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        p strong {
            color: #f1f5f9;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: .75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .625rem 1.25rem;
            border-radius: .75rem;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff;
            box-shadow: 0 4px 15px rgba(99,102,241,.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99,102,241,.4);
        }

        .btn-ghost {
            background: #ffffff12;
            color: #94a3b8;
            border: 1px solid #334155;
        }
        .btn-ghost:hover {
            background: #ffffff1e;
            color: #e2e8f0;
        }

        .divider {
            border: none;
            border-top: 1px solid #1e3a5f;
            margin: 2rem 0 1.5rem;
        }

        .contact-note {
            font-size: .8rem;
            color: #475569;
        }
        .contact-note a {
            color: #60a5fa;
            text-decoration: none;
        }
        .contact-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <div class="badge">Module Locked</div>

        <h1>This Module Is Not Enabled</h1>

        <p>
            The <strong>{{ ucwords(str_replace(['_', '-'], ' ', $feature)) }}</strong> module
            has not been activated for your restaurant account.<br><br>
            Please contact your <strong>Super Administrator</strong> to enable this feature on your plan.
        </p>

        <div class="actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}"
               class="btn btn-ghost">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Go to Dashboard
            </a>
        </div>

        <hr class="divider">
        <p class="contact-note">
            Feature code: <code>{{ $feature }}</code> &nbsp;·&nbsp;
            Need help? Contact your system administrator.
        </p>
    </div>
</body>
</html>
