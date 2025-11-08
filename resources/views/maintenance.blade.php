<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance - Mode NasDem Futuristik</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background: linear-gradient(135deg, #0066cc, #004499, #002266);
            color: #fff;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        #particle-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .maintenance-container {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 0.5rem;
        }

        .logo {
            margin-bottom: 2rem;
        }

        .logo-icon {
            font-size: 4rem;
            color: #ffffff;
            text-shadow: 0 0 20px #0066cc, 0 0 40px #0066cc;
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }

        .nasdem-logo {
            width: 120px;
            height: 120px;
            filter: drop-shadow(0 0 20px rgba(0, 102, 204, 0.8));
            animation: logo-float 6s ease-in-out infinite;
        }

        .title {
            font-family: 'Orbitron', monospace;
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #ffffff, #66aaff, #0066cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(0, 102, 204, 0.5);
            animation: text-shimmer 3s ease-in-out infinite;
        }

        .subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            color: #aaddff;
            font-weight: 300;
        }

        .motto {
            font-size: 1.1rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(0, 102, 204, 0.8);
        }

        .progress-container {
            width: 100%;
            max-width: 500px;
            margin: 2rem 0;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #0066cc, #66aaff);
            border-radius: 10px;
            width: 75%;
            animation: progress-animation 2s ease-in-out infinite;
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shine 2s ease-in-out infinite;
        }

        .status-text {
            font-size: 1.1rem;
            color: #66aaff;
            margin-top: 1rem;
            font-weight: 500;
        }

        .countdown {
            margin: 0rem 0;
        }

        .countdown-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #aaddff;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            min-width: 80px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .countdown-number {
            font-family: 'Orbitron', monospace;
            font-size: 2rem;
            font-weight: 700;
            color: #66aaff;
            text-shadow: 0 0 10px #66aaff;
        }

        .countdown-label {
            font-size: 0.8rem;
            color: #aaddff;
            margin-top: 0.5rem;
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-logo {
            position: absolute;
            width: 60px;
            height: 60px;
            opacity: 0.7;
            animation: flying-logo 15s linear infinite;
            filter: drop-shadow(0 0 10px rgba(0, 102, 204, 0.8));
        }

        .floating-element {
            position: absolute;
            font-size: 2rem;
            opacity: 0.6;
            animation: float 6s ease-in-out infinite;
            text-shadow: 0 0 10px rgba(102, 170, 255, 0.5);
        }

        .tech-icons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .tech-icon {
            font-size: 2.5rem;
            animation: icon-rotate 4s linear infinite;
            color: #66aaff;
            text-shadow: 0 0 15px #66aaff;
        }

        .message {
            max-width: 600px;
            margin: 1rem auto;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes pulse-glow {
            0% {
                text-shadow: 0 0 20px #0066cc, 0 0 40px #0066cc;
            }
            100% {
                text-shadow: 0 0 30px #0066cc, 0 0 60px #0066cc, 0 0 80px #0066cc;
            }
        }

        @keyframes logo-float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes flying-logo {
            0% {
                transform: translateX(-100px) translateY(100vh) rotate(0deg) scale(0.5);
                opacity: 0;
            }
            10% {
                opacity: 0.7;
            }
            90% {
                opacity: 0.7;
            }
            100% {
                transform: translateX(calc(100vw + 100px)) translateY(-100px) rotate(360deg) scale(1.2);
                opacity: 0;
            }
        }

        @keyframes text-shimmer {
            0%, 100% {
                background-position: -200% center;
            }
            50% {
                background-position: 200% center;
            }
        }

        @keyframes progress-animation {
            0%, 100% {
                transform: scaleX(1);
            }
            50% {
                transform: scaleX(1.02);
            }
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        @keyframes icon-rotate {
            0% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(180deg) scale(1.1);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .title {
                font-size: 2rem;
            }
            .subtitle {
                font-size: 1.1rem;
            }
            .countdown-timer {
                gap: 0.5rem;
            }
            .countdown-item {
                min-width: 60px;
                padding: 0.8rem;
            }
            .countdown-number {
                font-size: 1.5rem;
            }
            .nasdem-logo {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <!-- Particle Background -->
    <div id="particle-canvas"></div>

    <!-- Flying Logos -->
    <div class="floating-elements">
        <!-- Multiple flying logos with different delays -->
        <img src="/images/nasdem.png" class="floating-logo" style="animation-delay: 0s; left: 10%;">
        <img src="/images/nasdem.png" class="floating-logo" style="animation-delay: 3s; left: 20%;">
        <img src="/images/nasdem.png" class="floating-logo" style="animation-delay: 6s; left: 30%;">
        <img src="/images/nasdem.png" class="floating-logo" style="animation-delay: 9s; left: 40%;">
        <img src="/images/nasdem.png" class="floating-logo" style="animation-delay: 12s; left: 50%;">
        
        <!-- Floating elements -->
        <div class="floating-element" style="top: 10%; left: 5%;">🌱</div>
        <div class="floating-element" style="top: 20%; right: 10%;">🚜</div>
        <div class="floating-element" style="bottom: 30%; left: 15%;">🌾</div>
        <div class="floating-element" style="bottom: 20%; right: 5%;">💧</div>
        <div class="floating-element" style="top: 40%; left: 20%;">🌻</div>
        <div class="floating-element" style="top: 60%; right: 20%;">🐄</div>
        <div class="floating-element" style="top: 15%; left: 50%;">🌽</div>
        <div class="floating-element" style="bottom: 40%; right: 15%;">🍅</div>
    </div>

    <!-- Main Content -->
    <div class="maintenance-container">
        <div class="logo">
            <img src="/images/nasdem.png" class="nasdem-logo" alt="NasDem">
        </div>

        <div class="motto">GERAKAN PERUBAHAN</div>

        <h1 class="title">SISTEM RPR SEDANG UPGRADE</h1>
        <p class="subtitle">Kami sedang meningkatkan sistem untuk pendataan yang lebih baik</p>

        <div class="tech-icons">
            <div class="tech-icon">🌱</div>
            <div class="tech-icon">🚜</div>
            <div class="tech-icon">🌾</div>
            <div class="tech-icon">💧</div>
            <div class="tech-icon">🌻</div>
        </div>

        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="status-text" id="statusText">Memperbarui database... 75%</div>
        </div>

        <div class="countdown">
            <div class="countdown-title">Perkiraan Selesai:</div>
            <div class="countdown-timer">
                <div class="countdown-item">
                    <div class="countdown-number" id="hours">02</div>
                    <div class="countdown-label">JAM</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="minutes">30</div>
                    <div class="countdown-label">MENIT</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="seconds">45</div>
                    <div class="countdown-label">DETIK</div>
                </div>
            </div>
        </div>

        <div class="message">
            <p>🚀 <strong>Fitur Baru yang Akan Datang:</strong></p>
            <p>• Sistem Real-time Monitoring</p>
            <p>• AI-Powered Analytics</p>
            <p>• Mobile App Integration</p>
            <p>• Enhanced Security Features</p>
            <p>• Cloud Data Synchronization</p>
        </div>
    </div>

    <script>
        // Particle Background dengan warna biru NasDem
        function initParticles() {
            const canvas = document.getElementById('particle-canvas');
            if (!canvas) return;
            
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ canvas, alpha: true });
            
            renderer.setSize(window.innerWidth, window.innerHeight);
            
            const particlesGeometry = new THREE.BufferGeometry();
            const particlesCount = 1500;
            
            const posArray = new Float32Array(particlesCount * 3);
            
            for(let i = 0; i < particlesCount * 3; i++) {
                posArray[i] = (Math.random() - 0.5) * 5;
            }
            
            particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
            
            const particlesMaterial = new THREE.PointsMaterial({
                size: 0.005,
                color: 0x0066cc, // Warna biru NasDem
                transparent: true,
                opacity: 0.6
            });
            
            const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
            scene.add(particlesMesh);
            
            camera.position.z = 2;
            
            function animate() {
                requestAnimationFrame(animate);
                
                particlesMesh.rotation.x += 0.0005;
                particlesMesh.rotation.y += 0.001;
                
                renderer.render(scene, camera);
            }
            
            animate();
            
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
        }

        // Status Messages Rotation
        const statusMessages = [
            "Memperbarui database sistem... 75%",
            "Optimasi server... 80%", 
            "Instalasi security patch... 85%",
            "Update fitur AI... 90%",
            "Final testing... 95%",
            "Hampir selesai... 99%"
        ];

        let currentStatus = 0;
        function rotateStatus() {
            const statusElement = document.getElementById('statusText');
            if (statusElement) {
                statusElement.textContent = statusMessages[currentStatus];
                currentStatus = (currentStatus + 1) % statusMessages.length;
            }
        }

        // Countdown Timer
        function updateCountdown() {
            const now = new Date();
            const target = new Date(now);
            target.setHours(now.getHours() + 2, now.getMinutes() + 30, now.getSeconds() + 45);
            
            const diff = target - now;
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            const hoursElement = document.getElementById('hours');
            const minutesElement = document.getElementById('minutes');
            const secondsElement = document.getElementById('seconds');
            
            if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
            if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
            if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            try {
                initParticles();
                updateCountdown();
                setInterval(updateCountdown, 1000);
                setInterval(rotateStatus, 3000);
            } catch (error) {
                console.log('Particles initialization skipped:', error);
            }
        });
    </script>
</body>
</html>