<?php  
session_start();  
  
// AYARLAR  
$system_pass = "@ngbwayfite"; 
$telegram_bot_token = "8716804002:AAEAJ6c0FVCR19rk5-TeIZtMOVDLllfOzgs"; // Telegram bot token'ı ekleyin
$telegram_chat_id = "8258235296"; // Log gönderilecek chat ID
  
// GİRİŞ KONTROLÜ  
if (isset($_POST['login']) || isset($_POST['fast_login'])) {  
    if (isset($_POST['fast_login'])) {  
        $_SESSION['access'] = true;  
        logGiris("Hızlı giriş yapıldı - IP: " . $_SERVER['REMOTE_ADDR']);
    } else {  
        $input = trim(strtolower($_POST['passInput']));  
        if ($input === $system_pass) {  
            $_SESSION['access'] = true;  
            logGiris("Başarılı giriş - IP: " . $_SERVER['REMOTE_ADDR']);
        } else {  
            $_SESSION['tries'] = isset($_SESSION['tries']) ? $_SESSION['tries'] + 1 : 1;  
            if ($_SESSION['tries'] >= 3) {  
                logGiris("BAŞARISIZ GİRİŞ DENEMESİ - IP: " . $_SERVER['REMOTE_ADDR']);
                header("Location: https://www.usom.gov.tr");  
                exit();  
            }  
            $error = "HATALI ANAHTAR!";  
        }  
    }  
}  

function logGiris($mesaj) {
    $dosya = fopen("giris_log.txt", "a");
    fwrite($dosya, "[" . date("Y-m-d H:i:s") . "] " . $mesaj . "\n");
    fclose($dosya);
}
  
if (isset($_GET['logout'])) {  
    session_destroy();  
    header("Location: index.php");  
    exit();  
}  
?>  
<!DOCTYPE html>  
<html lang="tr">  
<head>  
    <meta charset="UTF-8">  
    <title>NGB - VERİ TERMİNALİ</title>  
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;900&family=Source+Code+Pro:wght@400;700&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>  
        :root { 
            --neon-red: #ff0000; 
            --neon-gold: #ffcc00; 
            --dark-bg: #050505;
            --glow-intensity: 0 0 20px;
        }  
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body, html { 
            margin: 0; 
            padding: 0; 
            width: 100%; 
            height: 100%; 
            background: #000; 
            font-family: 'Source Code Pro', monospace; 
            color: #fff; 
            overflow-x: hidden; 
        }  

        /* Matrix efekti */
        #matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.15;
            pointer-events: none;
        }

        .usom-bg { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            border: none; 
            z-index: 1; 
            filter: brightness(0.15) blur(3px) grayscale(100%); 
            pointer-events: none; 
            animation: slowPulse 8s infinite;
        }  

        @keyframes slowPulse {
            0%, 100% { opacity: 0.15; filter: brightness(0.15) blur(3px) grayscale(100%); }
            50% { opacity: 0.25; filter: brightness(0.2) blur(2px) grayscale(80%); }
        }

        /* Giriş ekranı */
        .login-overlay { 
            position: relative; 
            z-index: 10; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: rgba(0,0,0,0.85); 
            backdrop-filter: blur(5px);
        }  

        .terminal-box { 
            background: rgba(5,5,5,0.95); 
            padding: 50px; 
            border: 2px solid var(--neon-red); 
            text-align: center; 
            width: 420px; 
            box-shadow: 0 0 50px rgba(255,0,0,0.3);
            position: relative;
            overflow: hidden;
            animation: terminalGlow 3s infinite;
        }  

        @keyframes terminalGlow {
            0%, 100% { box-shadow: 0 0 30px rgba(255,0,0,0.3); border-color: #ff0000; }
            50% { box-shadow: 0 0 70px rgba(255,0,0,0.6); border-color: #ff4444; }
        }

        .terminal-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,0,0,0.1) 50%, transparent 70%);
            animation: scan 8s linear infinite;
            pointer-events: none;
        }

        @keyframes scan {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        h1 { 
            font-family: 'Orbitron', sans-serif; 
            color: var(--neon-red); 
            letter-spacing: 8px; 
            text-shadow: 0 0 10px var(--neon-red), 0 0 20px var(--neon-red); 
            margin-bottom: 30px;
            font-size: 3rem;
            position: relative;
            animation: textFlicker 3s infinite;
        }  

        @keyframes textFlicker {
            0%, 100% { opacity: 1; text-shadow: 0 0 10px red, 0 0 20px red; }
            92% { opacity: 1; text-shadow: 0 0 10px red, 0 0 20px red; }
            93% { opacity: 0.5; text-shadow: 0 0 5px red; }
            94% { opacity: 1; text-shadow: 0 0 15px red, 0 0 30px red; }
        }

        .loader-circle {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255,0,0,0.2);
            border-top: 4px solid var(--neon-red);
            border-right: 4px solid var(--neon-gold);
            border-radius: 50%;
            margin: 20px auto;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 20px rgba(255,0,0,0.5);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        input[type="password"] { 
            width: 100%; 
            padding: 15px; 
            margin: 20px 0; 
            background: #111; 
            border: 1px solid #333; 
            color: var(--neon-gold); 
            text-align: center; 
            font-size: 1.2rem; 
            box-sizing: border-box; 
            outline: none;
            transition: all 0.3s;
            font-family: 'Source Code Pro', monospace;
            letter-spacing: 4px;
        }  

        input[type="password"]:focus {
            border-color: var(--neon-red);
            box-shadow: 0 0 20px rgba(255,0,0,0.3);
            background: #1a1a1a;
        }

        input[type="password"]::placeholder {
            letter-spacing: 0;
            color: #333;
        }
        
        .btn { 
            background: linear-gradient(45deg, #8b0000, #ff0000); 
            color: #000; 
            border: none; 
            padding: 15px; 
            font-weight: 900; 
            cursor: pointer; 
            font-family: 'Orbitron', sans-serif; 
            width: 100%; 
            text-transform: uppercase; 
            transition: all 0.3s; 
            margin-bottom: 10px;
            position: relative;
            overflow: hidden;
            letter-spacing: 2px;
        }  

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-fast { 
            background: linear-gradient(45deg, #b8860b, #ffcc00); 
        }  

        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 30px rgba(255,0,0,0.5); 
        }  

        /* Ana panel */
        #panel { 
            position: relative; 
            z-index: 10; 
            padding: 30px; 
            background: rgba(5,5,5,0.97);
            min-height: 100vh;
            backdrop-filter: blur(5px);
        }  

        /* Hoşgeldin mesajı */
        .welcome-message {
            background: linear-gradient(45deg, #1a0000, #330000);
            border: 1px solid var(--neon-red);
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
            animation: welcomeGlow 2s;
        }

        @keyframes welcomeGlow {
            0% { box-shadow: 0 0 0 rgba(255,0,0,0); }
            50% { box-shadow: 0 0 100px rgba(255,0,0,0.5); }
            100% { box-shadow: 0 0 20px rgba(255,0,0,0.3); }
        }

        .welcome-message::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--neon-red), transparent);
            animation: scanline 3s linear infinite;
        }

        @keyframes scanline {
            0% { left: -100%; }
            100% { left: 200%; }
        }

        .welcome-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: var(--neon-gold);
            text-shadow: 0 0 10px var(--neon-gold);
            margin-bottom: 10px;
        }

        .welcome-sub {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .telegram-support {
            background: #0088cc;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .telegram-support:hover {
            background: #00a0e6;
            transform: scale(1.05);
            box-shadow: 0 0 20px #0088cc;
        }

        .section-title { 
            font-family: 'Orbitron', sans-serif; 
            color: var(--neon-gold); 
            border-bottom: 2px solid var(--neon-red); 
            padding: 10px; 
            margin: 30px 0 15px 0; 
            font-size: 1.2rem; 
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }  

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-red), var(--neon-gold), var(--neon-red));
            animation: slideBorder 2s linear infinite;
        }

        @keyframes slideBorder {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
            gap: 15px; 
        }  

        .card { 
            background: #0d0d0d; 
            border: 1px solid #1a1a1a; 
            padding: 20px; 
            border-left: 4px solid var(--neon-red); 
            cursor: pointer; 
            font-size: 0.9rem; 
            transition: all 0.3s; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis;
            position: relative;
            animation: cardAppear 0.5s ease-out;
            animation-fill-mode: both;
        }  

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 65%, rgba(255,0,0,0.1) 80%, transparent 95%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }

        .card:hover { 
            background: #151515; 
            border-color: var(--neon-red); 
            transform: translateX(5px) translateY(-2px);
            color: var(--neon-gold);
            box-shadow: 0 5px 20px rgba(255,0,0,0.3);
        }  

        .overlay { 
            display: none; 
            position: fixed; 
            top:0; 
            left:0; 
            width:100%; 
            height:100%; 
            background:rgba(0,0,0,0.95); 
            z-index:1000; 
            align-items:center; 
            justify-content:center;
            backdrop-filter: blur(10px);
            animation: fadeIn 0.3s;
        }  

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal { 
            background:#000; 
            border: 2px solid var(--neon-red); 
            padding: 40px; 
            text-align:center; 
            width: 380px; 
            box-shadow: 0 0 60px var(--neon-red);
            animation: modalPop 0.4s;
        }  

        @keyframes modalPop {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .pass-code { 
            color: var(--neon-gold); 
            font-size: 2.2rem; 
            border: 2px dashed var(--neon-gold); 
            padding: 20px; 
            margin: 20px 0; 
            font-weight: bold;
            letter-spacing: 5px;
            animation: blink 1s infinite;
        }  

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Yükleniyor çemberleri */
        .loading-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
        }

        .loading-circle {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,0,0,0.1);
            border-top: 3px solid var(--neon-red);
            border-right: 3px solid var(--neon-gold);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 20px rgba(255,0,0,0.3);
        }

        /* İstatistik kartı */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #0a0a0a;
            border: 1px solid #1a1a1a;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--neon-red);
            box-shadow: 0 5px 30px rgba(255,0,0,0.2);
        }

        .stat-value {
            font-size: 2rem;
            color: var(--neon-gold);
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
    </style>
</head>  
<body>  
    <!-- Matrix efekti canvas'ı -->
    <canvas id="matrix-bg"></canvas>

<?php if (!isset($_SESSION['access'])): ?>  
    <iframe class="usom-bg" src="https://www.usom.gov.tr"></iframe>  
    <div class="login-overlay">  
        <form class="terminal-box" method="POST">  
            <h1>NGB</h1>  
            <div class="loader-circle"></div>
            <?php if(isset($error)): ?>
                <p style="color:yellow; font-size:0.9rem; margin:10px 0;">⚠️ <?php echo $error; ?></p>  
            <?php endif; ?>  
            <input type="password" name="passInput" placeholder="SİSTEM ANAHTARI" autofocus>  
            <button type="submit" name="login" class="btn"> 
                <i class="fas fa-key" style="margin-right: 8px;"></i>SİSTEME GİRİŞ YAP
            </button>  
            <button type="submit" name="fast_login" class="btn btn-fast">  
                <i class="fas fa-bolt" style="margin-right: 8px;"></i>HIZLI GİRİŞ
            </button>  
            <p style="color:#444; font-size:0.7rem; margin-top:20px;">🔒 AUTHORIZED PERSONNEL ONLY</p>  
            <p style="color:#222; font-size:0.6rem;">© 2024 NGB SECURITY SYSTEMS</p>  
        </form>  
    </div>

<?php else: ?>  
    <div id="panel">  
        <!-- Hoşgeldin mesajı -->
        <div class="welcome-message">
            <div class="welcome-text">
                <i class="fas fa-user-shield" style="margin-right: 10px;"></i>
                HOŞGELDİNİZ, YETKİLİ KULLANICI
            </div>
            <div class="welcome-sub">
                <span><i class="fas fa-clock"></i> <?php echo date('d.m.Y H:i:s'); ?></span>
                <span><i class="fas fa-globe"></i> <?php echo $_SERVER['REMOTE_ADDR']; ?></span>
                <a href="https://t.me/ngbwayfite" target="_blank" class="telegram-support">
                    <i class="fab fa-telegram"></i> @ngbwayfite
                </a>
                <a href="https://t.me/ngbsorgu" target="_blank" class="telegram-support" style="background: #2a2a2a;">
                    <i class="fab fa-telegram"></i> @ngbsorgu
                </a>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-database" style="color: var(--neon-red); font-size: 2rem;"></i>
                <div class="stat-value" id="total-data">0</div>
                <div class="stat-label">Toplam Veri Seti</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users" style="color: var(--neon-gold); font-size: 2rem;"></i>
                <div class="stat-value" id="total-records">0M+</div>
                <div class="stat-label">Toplam Kayıt</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line" style="color: #00ff00; font-size: 2rem;"></i>
                <div class="stat-value" id="new-today">19</div>
                <div class="stat-label">Bugün Eklenen</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shield-alt" style="color: #ff00ff; font-size: 2rem;"></i>
                <div class="stat-value" id="active-users">1</div>
                <div class="stat-label">Aktif Oturum</div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">  
            <h1 style="font-size: 2rem; letter-spacing: 4px;">NGB // VERİ TERMİNALİ</h1>  
            <a href="?logout=1" style="color:var(--neon-red); text-decoration:none; font-size:0.9rem; border:1px solid var(--neon-red); padding:8px 15px;" 
               onmouseover="this.style.background='var(--neon-red)'; this.style.color='#000';" 
               onmouseout="this.style.background='transparent'; this.style.color='var(--neon-red)';">
                <i class="fas fa-sign-out-alt"></i> SİSTEMDEN AYRIL
            </a>  
        </div>  

        <div class="section-title">
            <i class="fas fa-crown"></i> NGB ADMIN PANELİ
        </div>  
        <div class="grid" id="admin-new-data"></div>  

        <div class="section-title">
            <i class="fas fa-star"></i> YENİ EKLENEN VERİLER & ÖZEL LİNKLER
        </div>  
        <div class="grid" id="new-added"></div>  

        <div class="section-title">
            <i class="fas fa-robot"></i> BOT & API ALTYAPILARI
        </div>  
        <div class="grid" id="bots"></div>  

        <div class="section-title">
            <i class="fas fa-chart-bar"></i> GENEL VERİ SETLERİ (MERNİS/GSM)
        </div>  
        <div class="grid" id="general-data"></div>  

        <div class="section-title">
            <i class="fas fa-id-card"></i> KİMLİK / VESİKA / MERNİS
        </div>  
        <div class="grid" id="id-data"></div>  

        <div class="section-title">
            <i class="fas fa-map-marked-alt"></i> TAPU & ADRES VERİLERİ
        </div>  
        <div class="grid" id="land-data"></div>  

        <div class="section-title">
            <i class="fas fa-university"></i> BANKA & BAHİS & ÖZEL DATALAR
        </div>  
        <div class="grid" id="special-data"></div>  

        <div class="section-title">
            <i class="fas fa-graduation-cap"></i> EĞİTİM & ÖĞRENCİ VERİLERİ
        </div>  
        <div class="grid" id="edu-data"></div>  
    </div>  

    <!-- Yükleniyor çemberi -->
    <div class="loading-container">
        <div class="loading-circle"></div>
    </div>

    <div class="overlay" id="overlay">  
        <div class="modal">  
            <i class="fas fa-lock" style="color: var(--neon-red); font-size: 3rem; margin-bottom: 15px;"></i>
            <div style="color:var(--neon-red); font-size:1rem; margin-bottom:10px;">DOSYA ŞİFRESİ:</div>  
            <div class="pass-code">@devrelax</div>  
            <button class="btn" id="goBtn">
                <i class="fas fa-external-link-alt"></i> DOSYAYI AÇ
            </button>  
            <button onclick="closeModal()" style="background:none; border:none; color:#555; cursor:pointer; margin-top:15px; font-size:0.9rem;">  
                <i class="fas fa-times"></i> İPTAL
            </button>  
        </div>  
    </div>  

<script>  
    // Matrix efekti
    const canvas = document.getElementById('matrix-bg');
    const ctx = canvas.getContext('2d');

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%+-/~{[|`]}";
    const matrixArray = matrix.split("");

    const fontSize = 10;
    const columns = canvas.width / fontSize;

    const drops = [];
    for(let x = 0; x < columns; x++) {
        drops[x] = 1;
    }

    function drawMatrix() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = '#0F0';
        ctx.font = fontSize + 'px monospace';

        for(let i = 0; i < drops.length; i++) {
            const text = matrixArray[Math.floor(Math.random() * matrixArray.length)];
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);

            if(drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                drops[i] = 0;
            }
            drops[i]++;
        }
    }

    setInterval(drawMatrix, 35);

    // Veri setleri
    const db = [  
        {n: "8 Adet Discord & Telegram Bot", u: "1H7FqzdRRygg983YcHzXRMDkzOV-TJhvq", c: "bots", size: "2.3GB"},  
        {n: "Api Sunucusu Oluşturucu", u: "1ZilMYtojtnEIoMoAzf8foBaLkzx6DC0C", c: "bots", size: "156MB"},  
        {n: "109m Tc Pro", u: "15QnumnhgZsiAy9vaILMFpxMpGymWvk57", c: "general-data", size: "14.5GB"},  
        {n: "195m Gsm", u: "16UUUBaqFqRD1guzNEk8hjvKZ3cHfZNUX", c: "general-data", size: "21.2GB"},  
        {n: "135m Gsm", u: "19rWIa_iJz3IWFfRBog61ZgvoeQ68o7-9", c: "general-data", size: "15.8GB"},  
        {n: "116m Gsm", u: "19wAhByABb-KssZUWiFilIk0ANekiIef0", c: "general-data", size: "12.4GB"},  
        {n: "145m Gsm", u: "1UiAJHwe6VDHlrKd7GuqM8j-lfi2lmsgM", c: "general-data", size: "16.2GB"},  
        {n: "120m Gsm", u: "19vEG1Bag-TeB0G6zH_qGS5MiCGBotcMg", c: "general-data", size: "13.1GB"},  
        {n: "200m Gsm", u: "1oo6_RJcd9qWywx6l0yvol9cRXbOyQdow", c: "general-data", size: "22.8GB"},  
        {n: "50m Vatandaş", u: "10cBYrxgOHfuJ_HB63pjq9nQ987NyWSFE", c: "general-data", size: "6.7GB"},  
        {n: "101m Data", u: "1Ut7EPR7ZzmKf-do2GaHE1YkYitcStFOC", c: "general-data", size: "11.3GB"},  
        {n: "400k Vesika", u: "1bmgiE1ZQ4aMQEzDRSSAs9WVfX2Kls4QV", c: "id-data", size: "2.1GB"},  
        {n: "400k Vesika (Yedek)", u: "1brbf0YrYoiYMMCqR8HT3VGKN5EhURg84", c: "id-data", size: "2.1GB"},  
        {n: "60k Vesika", u: "1gisHgOssuvrtCy7mONEv3JmDiR3N6id9", c: "id-data", size: "856MB"},  
        {n: "3k Yök Vesika", u: "1o_tlvW3Ub7_gy1kC7E869EjaFb75N3ME", c: "id-data", size: "234MB"},  
        {n: "6m Aşı Datası", u: "13WB2iD0_-2wTXKQzuH5VfJBNTtkOeK-0", c: "id-data", size: "3.2GB"},  
        {n: "240k Ölüm Verisi", u: "1YQTRbCiT7DRz8ocSsWRmT8b-akB2it7P", c: "id-data", size: "156MB"},  
        {n: "Mobil Mernis", u: "1cB64DQiXol4DUQwcFJPgEVN2JqKbSN7i", c: "id-data", size: "4.5GB"},  
        {n: "54m Seçmen 2015", u: "167XC60hDxvvX5NgNYyIPXIYhUvwV-sCS", c: "id-data", size: "7.8GB"},  
        {n: "3m Yabancı", u: "1hgmF5s6OO-hW6H0pKFSng3L5DpIVAF-x", c: "id-data", size: "1.2GB"},  
        {n: "83m Adres", u: "1w3dxU6Dr9AtCN9LxaGIyTcxyShQcEylQ", c: "land-data", size: "9.4GB"},  
        {n: "81m Adres (09-24)", u: "1BRTriBMqp4ZSvlNMsUGfnW_L2v-fEiGV", c: "land-data", size: "9.1GB"},  
        {n: "8m İstanbul Tapu", u: "1juEwo-4jQoGKwbcH8Stc9sqZ5yPlkRpE", c: "land-data", size: "2.8GB"},  
        {n: "97m 33 İl Tapu", u: "1uBsIGe5mFe_8tiqFewywDextSPF6Rabv", c: "land-data", size: "15.6GB"},  
        {n: "2m Kocaeli Tapu", u: "1NqCCiXTWN7y0zJs_cjKV4UtMrMOrZ8s_", c: "land-data", size: "892MB"},  
        {n: "20m İş Yeri", u: "1dJ6uMyRqZIxNZ9ozF6dDESuaoxYXgi-U", c: "land-data", size: "4.2GB"},  
        {n: "9m Ak Parti", u: "1JEv5p_bMUNPGPMKiiAfXM-6iHwGahvUm", c: "special-data", size: "1.8GB"},  
        {n: "20m Facebook TR", u: "1x8OlCwowDwrRATJJs8lQii96Zy8xGRac", c: "special-data", size: "3.4GB"},  
        {n: "3m Craftrise", u: "1FCFS21r81XVUM15mfRiYG9kC5QdtMwJ7", c: "special-data", size: "678MB"},  
        {n: "5m Hgs Verisi", u: "1GzJ4us2Kc76QRpfEhI40pBWRmFBNueeK", c: "special-data", size: "1.1GB"},  
        {n: "2m Sgk Verisi", u: "1JCr3AROaOt3Nxfy7_oYyeapUNFS5zoYB", c: "special-data", size: "456MB"},  
        {n: "70k Papara", u: "1xjZhJhnMcNuYVxoN5dLSAksJI0krZtF5", c: "special-data", size: "89MB"},  
        {n: "500k Tuttur", u: "1ezuSXr5uEkrva0bxvIY4KNxvpnS50cXx", c: "special-data", size: "234MB"},  
        {n: "360k 1xbet", u: "1Vn5zTdv6S8Ir2SC6Ct19cuR4VSfO4yW0", c: "special-data", size: "178MB"},  
        {n: "600k Instagram", u: "1oxxqUuCQQNGF51pAydLftcPeW-A1V4F-", c: "special-data", size: "267MB"},  
        {n: "25m Okul No", u: "1QK6b2J2mVEYyHCXFNvcyVTHJPviK2DKW", c: "edu-data", size: "3.8GB"},  
        {n: "23m e-Okul", u: "18GQ4culyLH0X7nxcv4R_aYtir_4SO_4I", c: "edu-data", size: "3.5GB"},  
        {n: "800k İşler Öğrenci", u: "1z4og5-Ip0TQutEXH0lZ1cX0EzugZ6xdb", c: "edu-data", size: "345MB"},  
        {n: "178k Öğretmen", u: "1r3ak2bzZ9aVodFTuRBbjmAJuF6A61s6X", c: "edu-data", size: "123MB"}  
    ];  

    // Yeni eklenen veriler
    const newData = [
        {n: "4.6M SNAPCHAT DATASI", u: "https://dosya.co/0l197l8m59c6/SnapChat_(UserList)_DB.zip.html", c: "admin-new-data", size: "892MB"},
        {n: "Getir Datası", u: "https://www.mediafire.com/file/o78sff7ltkx00si/getir.rar/file", c: "admin-new-data", size: "2.1GB"},
        {n: "101 M ALL TR DATASI", u: "https://drive.google.com/file/d/1s3foUv8y89QyLXPOxZaP3vAXWxmN_fbp/view", c: "admin-new-data", size: "15.6GB"},
        {n: "101 M ALL TR DATASI (Yedek)", u: "https://drive.google.com/file/d/1ITlNgsyNEm2F2C3hgfW9doRgBDtyRM8f/view", c: "admin-new-data", size: "15.6GB"},
        {n: "116M Gsm Data", u: "https://drive.google.com/file/d/1yoJ6F89fiEC7x-GAhJQZQkXIuV4-SwEK/view", c: "new-added", size: "12.4GB"},
        {n: "135M Gsm Data", u: "https://dosya.co/wvi4hvp2g107/kayitlar.json.html", c: "new-added", size: "15.8GB"},
        {n: "E - OKUL DATA 16 M", u: "#", note: "Eski veriler korundu", c: "new-added", size: "2.8GB"},
        {n: "VODAFONE SQL DATA", u: "https://www.mediafire.com/file/eqqt95hpxkrn0az/Vodaphone.sql/file", c: "new-added", size: "3.4GB"},
        {n: "80 K OKUL NO DATA", u: "https://dosya.co/3xvtttdny5x7/okulno.sql.html", c: "new-added", size: "234MB"},
        {n: "Türkmenistan Datası", u: "https://dosya.co/zlc612hyw226/Turkmenistan_Citizenship_Data.rar.html", c: "new-added", size: "1.8GB"},
        {n: "120 M GSM DATA", u: "https://www.mediafire.com/file/q3rxydgltknr8qi/120m.rar/file", c: "new-added", size: "13.1GB"},
        {n: "İZMİR TAPU DATASI", u: "https://s6.dosya.tc/server18/r7nzut/Newton_Izmir_Tapu_Datasi.zip.html", c: "new-added", size: "2.2GB"},
        {n: "2022 KFC.com Database", u: "https://drive.google.com/file/d/17ZmjzRURaVm2IA02GCM465lTY0EqclcP/view", c: "new-added", size: "567MB"},
        {n: "13 M TC PRO DATA", u: "https://dosya.co/94t9dysjf1c5/13m_Tc_Pro_Newton.zip.html", c: "new-added", size: "2.4GB"},
        {n: "Truecaller Database", u: "https://dosya.co/6batvmwzi5jm/TrueCaller_99314_veri.rar.html", c: "new-added", size: "789MB"},
        {n: "25 M E - OKUL DATA", u: "https://www.mediafire.com/file/px3ie2cqmth1ald/eokulmars.zip/file", c: "new-added", size: "4.1GB"}
    ];

    // İstatistikleri güncelle
    document.getElementById('total-data').textContent = db.length + newData.length;
    
    let totalRecords = 0;
    db.forEach(item => {
        if(item.n.includes('m')) {
            const match = item.n.match(/(\d+)m/i);
            if(match) totalRecords += parseInt(match[1]);
        }
    });
    document.getElementById('total-records').textContent = totalRecords + 'M+';

    // Kartları oluştur (gecikmeli animasyon için)
    db.forEach((item, index) => {  
        setTimeout(() => {
            const card = document.createElement('div');  
            card.className = 'card';  
            card.innerHTML = `> ${item.n} <span style="color: #666; font-size: 0.7rem; margin-left: 8px;">[${item.size || '??'}]</span>`;  
            card.onclick = () => {  
                document.getElementById('overlay').style.display = 'flex';  
                document.getElementById('goBtn').onclick = () => {  
                    window.open('https://drive.google.com/file/d/' + item.u + '/view', '_blank');  
                    closeModal();  
                };  
            };  
            const container = document.getElementById(item.c);  
            if(container) container.appendChild(card);  
        }, index * 50);
    });  

    // Yeni eklenenleri işle
    newData.forEach((item, index) => {  
        setTimeout(() => {
            const card = document.createElement('div');  
            card.className = 'card';  
            card.innerHTML = `> ${item.n} ${item.note ? '(' + item.note + ')' : ''} <span style="color: #666; font-size: 0.7rem; margin-left: 8px;">[${item.size || '??'}]</span>`;  
            card.onclick = () => {  
                if(item.u !== '#') {
                    window.open(item.u, '_blank');  
                }
            };  
            const container = document.getElementById(item.c);  
            if(container) container.appendChild(card);  
        }, (db.length + index) * 50);
    });  

    function closeModal() { 
        document.getElementById('overlay').style.display = 'none'; 
    }  

    // Pencere yeniden boyutlandığında matrix'i güncelle
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
</script>

<?php endif; ?>  
</body>  
</html>
