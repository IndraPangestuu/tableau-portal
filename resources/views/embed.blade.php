<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeMenu->name ?? 'Dashboard' }} - Portal Korlantas</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a1628;
            min-height: 100vh;
        }

        .embed-container {
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        #tableauViz {
            display: block;
            width: 100%;
            height: 100vh;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            max-width: 500px;
            margin: 50px auto;
        }

        .error-box h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .error-box p {
            font-size: 14px;
            margin-top: 10px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #0a1628;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            z-index: 100;
            color: #fff;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(30, 136, 229, 0.2);
            border-top-color: #1e88e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .back-link {
            position: fixed;
            top: 15px;
            left: 15px;
            background: rgba(30, 136, 229, 0.9);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            z-index: 200;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .back-link:hover {
            background: rgba(30, 136, 229, 1);
        }
    </style>
</head>
<body>
    <a href="/dashboard" class="back-link">
        ← Kembali ke Dashboard
    </a>

    <div class="embed-container">
        @if($failed)
            <div class="error-box">
                <h3>🚨 Gagal Mendapatkan Trusted Ticket</h3>
                <p>{{ $error_message }}</p>
                <p>Pastikan IP web server sudah menjadi Trusted Host di Tableau Server.</p>
            </div>
        @else
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
                <p>Memuat dashboard...</p>
            </div>

            <tableau-viz
                id="tableauViz"
                src="{{ $embed_url }}"
                toolbar="bottom"
                hide-tabs
            ></tableau-viz>
        @endif
    </div>

    @if(!$failed)
    <script type="module" src="{{ $server }}/javascripts/api/tableau.embedding.3.latest.min.js"></script>
    <script type="module">
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        const viz = document.getElementById('tableauViz');
        if (viz) {
            viz.addEventListener('firstinteractive', hideLoading);
            viz.addEventListener('firstvizsizeknown', hideLoading);
        }

        // Fallback timeout
        setTimeout(hideLoading, 8000);
    </script>
    @endif
</body>
</html>
