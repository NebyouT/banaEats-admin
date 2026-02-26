<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $game->name }}</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, {{ $game->display_settings['primary_color'] ?? '#8DC63F' }} 0%, {{ $game->display_settings['secondary_color'] ?? '#F5D800' }} 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        
        .game-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
        
        .game-header {
            text-align: center;
            margin-bottom: 20px;
            color: {{ $game->display_settings['text_color'] ?? '#FFFFFF' }};
        }
        
        .game-header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .game-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .game-body {
            background: white;
            border-radius: 20px;
            padding: 30px 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .spinner-circle {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid {{ $game->display_settings['primary_color'] ?? '#8DC63F' }};
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }
        
        .spinner-text {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .game-instructions {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }
        
        .prize-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .prize-modal.active {
            display: flex;
        }
        
        .prize-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .prize-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .prize-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .prize-description {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .prize-code {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        
        .btn-close-modal {
            background: {{ $game->display_settings['primary_color'] ?? '#8DC63F' }};
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        
        canvas {
            max-width: 100%;
            height: auto;
        }
        
        .scratch-container {
            position: relative;
            width: 100%;
            max-width: 350px;
        }
        
        .slot-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        
        .slot-reel {
            width: 80px;
            height: 100px;
            background: #f8f9fa;
            border: 3px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .slot-symbol {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
        }
        
        .mystery-boxes {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 350px;
            width: 100%;
        }
        
        .mystery-box {
            aspect-ratio: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .mystery-box:active {
            transform: scale(0.95);
        }
        
        .btn-spin, .btn-scratch, .btn-slot-spin, .btn-roulette-spin {
            background: {{ $game->display_settings['secondary_color'] ?? '#F5D800' }};
            color: {{ $game->display_settings['text_color'] ?? '#1A1A1A' }};
            border: none;
            padding: 16px 40px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        
        .btn-spin:active, .btn-scratch:active, .btn-slot-spin:active, .btn-roulette-spin:active {
            transform: scale(0.95);
        }
        
        .roulette-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            max-width: 350px;
            width: 100%;
            margin: 20px 0;
        }
        
        .roulette-option {
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .roulette-option:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <h1>{{ $game->name }}</h1>
            @if($game->description)
            <p>{{ $game->description }}</p>
            @endif
        </div>
        
        <div class="game-body" id="gameBody">
            <div class="loading-spinner" id="gameLoading">
                <div class="spinner-circle"></div>
                <div class="spinner-text">Loading game...</div>
            </div>
        </div>
        
        @if($game->instructions)
        <div class="game-instructions">
            {{ $game->instructions }}
        </div>
        @endif
    </div>
    
    <div class="prize-modal" id="prizeModal">
        <div class="prize-content">
            <div class="prize-icon">🎉</div>
            <div class="prize-title" id="prizeTitle"></div>
            <div class="prize-description" id="prizeDescription"></div>
            <div class="prize-code" id="prizeCode"></div>
            <button class="btn-close-modal" onclick="closePrizeModal()">Close</button>
        </div>
    </div>
    
    <!-- Load jQuery FIRST to prevent $ is not defined error -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        const PRIMARY = '{{ $game->display_settings["primary_color"] ?? "#8DC63F" }}';
        const SECONDARY = '{{ $game->display_settings["secondary_color"] ?? "#F5D800" }}';
        const TEXT_COLOR = '{{ $game->display_settings["text_color"] ?? "#1A1A1A" }}';
        const GAME_TYPE = '{{ $game->type }}';
        const GAME_ID = {{ $game->id }};
        const prizes = @json($prizesData);
        
        function closePrizeModal() {
            $('#prizeModal').removeClass('active');
        }
        
        function showPrizeResult(prize, prizeCode) {
            $('#prizeTitle').text('Congratulations! 🎊');
            $('#prizeDescription').text('You won: ' + prize.name);
            $('#prizeCode').text('Code: ' + prizeCode);
            $('#prizeModal').addClass('active');
        }
        
        function selectRandomPrize() {
            if (prizes.length === 0) return null;
            
            const totalProb = prizes.reduce((sum, p) => sum + parseFloat(p.probability), 0);
            let rand = Math.random() * totalProb;
            
            for (let prize of prizes) {
                rand -= parseFloat(prize.probability);
                if (rand <= 0) return prize;
            }
            
            return prizes[0];
        }
        
        function generatePrizeCode() {
            return 'PRIZE-' + Math.random().toString(36).substr(2, 9).toUpperCase();
        }
        
        $(document).ready(function() {
            const body = $('#gameBody');
            
            if (GAME_TYPE === 'spin_wheel') {
                renderSpinWheel();
            } else if (GAME_TYPE === 'scratch_card') {
                renderScratchCard();
            } else if (GAME_TYPE === 'slot_machine') {
                renderSlotMachine();
            } else if (GAME_TYPE === 'mystery_box') {
                renderMysteryBox();
            } else {
                renderDecisionRoulette();
            }
        });
        
        function renderSpinWheel() {
            const body = $('#gameBody');
            body.html(`
                <canvas id="wheelCanvas" width="350" height="350"></canvas>
                <button class="btn-spin" onclick="spinWheel()">SPIN</button>
            `);
            
            $('#gameLoading').remove();
            drawWheel();
        }
        
        function drawWheel() {
            const canvas = document.getElementById('wheelCanvas');
            const ctx = canvas.getContext('2d');
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const radius = 150;
            
            const anglePerSegment = (2 * Math.PI) / prizes.length;
            
            prizes.forEach((prize, i) => {
                const startAngle = i * anglePerSegment;
                const endAngle = startAngle + anglePerSegment;
                
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.closePath();
                ctx.fillStyle = prize.color || PRIMARY;
                ctx.fill();
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 2;
                ctx.stroke();
                
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + anglePerSegment / 2);
                ctx.textAlign = 'center';
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 12px Arial';
                ctx.fillText(prize.name, radius * 0.7, 5);
                ctx.restore();
            });
            
            ctx.beginPath();
            ctx.arc(centerX, centerY, 15, 0, 2 * Math.PI);
            ctx.fillStyle = '#fff';
            ctx.fill();
        }
        
        let spinning = false;
        function spinWheel() {
            if (spinning) return;
            spinning = true;
            
            const canvas = document.getElementById('wheelCanvas');
            const prize = selectRandomPrize();
            const prizeIndex = prizes.findIndex(p => p.id === prize.id);
            const anglePerSegment = (2 * Math.PI) / prizes.length;
            const targetAngle = prizeIndex * anglePerSegment + anglePerSegment / 2;
            const spins = 5;
            const totalRotation = spins * 2 * Math.PI + targetAngle;
            
            let currentRotation = 0;
            const duration = 3000;
            const startTime = Date.now();
            
            function animate() {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeOut = 1 - Math.pow(1 - progress, 3);
                
                currentRotation = totalRotation * easeOut;
                
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(currentRotation);
                ctx.translate(-canvas.width / 2, -canvas.height / 2);
                drawWheel();
                ctx.restore();
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    spinning = false;
                    setTimeout(() => showPrizeResult(prize, generatePrizeCode()), 500);
                }
            }
            
            animate();
        }
        
        function renderScratchCard() {
            const body = $('#gameBody');
            body.html(`
                <div class="scratch-container">
                    <canvas id="scratchCanvas" width="350" height="200"></canvas>
                </div>
                <p style="font-size:13px;color:#999;margin-top:15px;">Scratch to reveal your prize!</p>
            `);
            
            $('#gameLoading').remove();
            initScratchCard();
        }
        
        function initScratchCard() {
            const canvas = document.getElementById('scratchCanvas');
            const ctx = canvas.getContext('2d');
            const prize = selectRandomPrize();
            
            ctx.fillStyle = '#f0f0f0';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = SECONDARY;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 20px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Scratch Here!', canvas.width / 2, canvas.height / 2);
            
            let isScratching = false;
            let scratchedPercent = 0;
            
            function scratch(x, y) {
                ctx.globalCompositeOperation = 'destination-out';
                ctx.beginPath();
                ctx.arc(x, y, 20, 0, 2 * Math.PI);
                ctx.fill();
            }
            
            canvas.addEventListener('mousedown', () => isScratching = true);
            canvas.addEventListener('mouseup', () => isScratching = false);
            canvas.addEventListener('mousemove', (e) => {
                if (isScratching) {
                    const rect = canvas.getBoundingClientRect();
                    scratch(e.clientX - rect.left, e.clientY - rect.top);
                    checkScratchProgress();
                }
            });
            
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                isScratching = true;
            });
            canvas.addEventListener('touchend', () => isScratching = false);
            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                if (isScratching) {
                    const rect = canvas.getBoundingClientRect();
                    const touch = e.touches[0];
                    scratch(touch.clientX - rect.left, touch.clientY - rect.top);
                    checkScratchProgress();
                }
            });
            
            function checkScratchProgress() {
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                let transparent = 0;
                for (let i = 3; i < imageData.data.length; i += 4) {
                    if (imageData.data[i] === 0) transparent++;
                }
                scratchedPercent = (transparent / (imageData.data.length / 4)) * 100;
                
                if (scratchedPercent > 50 && !canvas.dataset.revealed) {
                    canvas.dataset.revealed = 'true';
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.globalCompositeOperation = 'source-over';
                    ctx.fillStyle = '#4CAF50';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#fff';
                    ctx.font = 'bold 24px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText(prize.name, canvas.width / 2, canvas.height / 2);
                    setTimeout(() => showPrizeResult(prize, generatePrizeCode()), 1000);
                }
            }
        }
        
        function renderSlotMachine() {
            const body = $('#gameBody');
            body.html(`
                <div class="slot-container">
                    <div class="slot-reel"><div class="slot-symbol" id="slot1">🍒</div></div>
                    <div class="slot-reel"><div class="slot-symbol" id="slot2">🍋</div></div>
                    <div class="slot-reel"><div class="slot-symbol" id="slot3">🍊</div></div>
                </div>
                <button class="btn-slot-spin" onclick="spinSlots()">SPIN</button>
            `);
            
            $('#gameLoading').remove();
        }
        
        let slotsSpinning = false;
        function spinSlots() {
            if (slotsSpinning) return;
            slotsSpinning = true;
            
            const symbols = ['🍒', '🍋', '🍊', '🍇', '🍉', '⭐'];
            const prize = selectRandomPrize();
            
            for (let i = 1; i <= 3; i++) {
                let count = 0;
                const interval = setInterval(() => {
                    $(`#slot${i}`).text(symbols[Math.floor(Math.random() * symbols.length)]);
                    count++;
                    if (count > 20 + i * 5) {
                        clearInterval(interval);
                        $(`#slot${i}`).text('⭐');
                        if (i === 3) {
                            slotsSpinning = false;
                            setTimeout(() => showPrizeResult(prize, generatePrizeCode()), 500);
                        }
                    }
                }, 100);
            }
        }
        
        function renderMysteryBox() {
            const body = $('#gameBody');
            let html = '<div class="mystery-boxes">';
            for (let i = 0; i < 6; i++) {
                html += `<div class="mystery-box" onclick="openBox(${i})">🎁</div>`;
            }
            html += '</div><p style="font-size:12px;color:#aaa;margin-top:12px;text-align:center">Tap a box to reveal your prize!</p>';
            body.html(html);
            
            $('#gameLoading').remove();
        }
        
        let boxOpened = false;
        function openBox(index) {
            if (boxOpened) return;
            boxOpened = true;
            
            const prize = selectRandomPrize();
            const boxes = $('.mystery-box');
            boxes.eq(index).html('🎉').css('background', 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)');
            
            setTimeout(() => showPrizeResult(prize, generatePrizeCode()), 800);
        }
        
        function renderDecisionRoulette() {
            const body = $('#gameBody');
            const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A'];
            let html = '<div class="roulette-options">';
            prizes.slice(0, 4).forEach((prize, i) => {
                html += `<div class="roulette-option" style="background:${colors[i]};color:#fff" onclick="selectOption(${i})">${prize.name}</div>`;
            });
            html += '</div>';
            body.html(html);
            
            $('#gameLoading').remove();
        }
        
        let optionSelected = false;
        function selectOption(index) {
            if (optionSelected) return;
            optionSelected = true;
            
            const prize = selectRandomPrize();
            setTimeout(() => showPrizeResult(prize, generatePrizeCode()), 500);
        }
    </script>
</body>
</html>
