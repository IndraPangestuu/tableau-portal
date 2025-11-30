<?php
// =================================================================
// KONFIGURASI TABLEAU SERVER (!!! GANTI SEMUA NILAI INI !!!)
// =================================================================
$tableauServer = "https://dakgarlantas.info"; 
$trustedUser = "korlantas_viewer_2"; // Username Tableau yang valid
//$tableauSite = "Home"; // Ganti jika Anda menggunakan site selain 'default'
$viewPath = "/views/home/01_SummaryDAKGARLANTAS3"; // Path ke View (Dashboard/Worksheet) Anda
// =================================================================

// 1. Tentukan endpoint Tableau Server untuk Trusted Auth
$url = $tableauServer . '/trusted';

// 2. Data yang dikirimkan dalam permintaan POST
$data = array(
    'username' => $trustedUser,
    //'target_site' => $tableauSite
);

// 3. Konfigurasi cURL untuk permintaan POST
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

// 4. Eksekusi dan tangkap respons
$ticket = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// 5. Cek Kegagalan Tiket (-1)
$isTicketFailed = false;
$errorMessage = "";

if ($httpCode !== 200 || $curlError) {
    $isTicketFailed = true;
    $ticket = '-1';
    $errorMessage = "Error Koneksi/HTTP. Kode: " . $httpCode . ". Pesan: " . $curlError;
} elseif ($ticket === '-1') {
    $isTicketFailed = true;
    $errorMessage = "AUTHENTICATION FAILED. Pastikan IP Server Web Anda ditambahkan sebagai 'Trusted Host' di Tableau Server.";
}

$trustedTicket = $ticket; 

// 6. Susun URL akhir yang akan digunakan oleh Web Component
// Format: [Server]/trusted/[Ticket]/[Path View]
$urlWithTicket = $tableauServer . "/trusted/" . $trustedTicket . $viewPath;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embed Tableau dengan Trusted Auth (Web Component)</title>
    
    <script type="module" src="<?= $tableauServer ?>/javascripts/api/tableau.embedding.3.latest.min.js"></script>
    
    <style>
        body { font-family: Arial, sans-serif; }
        /* Atur tinggi dan lebar untuk Web Component */
        #tableau-viz-container {
            display: block; /* Pastikan berfungsi seperti block element */
            height: 2000px; 
            width: 100%;
            border: 0px solid #ccc;
            margin-top: 20px;
        }
        .error-message {
            color: white;
            background-color: #f44336;
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>


    <?php if ($isTicketFailed): ?>
        <div class="error-message">
            <h2>?? GAGAL MENDAPATKAN TIKET (Kode: <?= htmlspecialchars($trustedTicket) ?>)</h2>
            <p><?= htmlspecialchars($errorMessage) ?></p>
            <p><strong>Tindakan Koreksi:</strong> Periksa IP Server Web di konfigurasi Tableau Server.</p>
        </div>
    <?php endif; ?>

    <tableau-viz
        id="tableau-viz-container"
        src="<?= $urlWithTicket ?>"  
        toolbar="hidden"
        hide-tabs
    >
  
    
        

        <?php if (!$isTicketFailed): ?>
             <p style="color: green;">Visualisasi sedang dimuat...</p>
        <?php endif; ?>
    </tableau-viz>
    
    <hr>
    <tableau-viz 
        id="tableau-viz" 
        src="<?= $tableauServer ?>/views/etnas/Dashboard1" 
        width=100% 
        height="1280" 
        hide-tabs 
        toolbar="hidden" 
    >
    </tableau-viz>
</body>
</html>