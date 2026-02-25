@extends('layouts.admin.app')

@section('title', translate('Game Preview'))

@push('css_or_js')
<style>
/* Phone Frame Simulator */
.preview-toolbar{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.device-btn{padding:6px 14px;border:1.5px solid #ddd;border-radius:8px;background:#fff;font-size:12px;font-weight:600;cursor:pointer;transition:.2s}
.device-btn.active,.device-btn:hover{border-color:#8DC63F;background:#f0fbe0;color:#5a8a1a}
.phone-frame-wrap{display:flex;justify-content:center;padding:20px;background:#f0f0f0;border-radius:12px;min-height:700px;align-items:flex-start;overflow:auto;resize:both}
.phone-frame{background:#1a1a1a;border-radius:40px;padding:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);transition:width .3s,height .3s;position:relative;flex-shrink:0}
.phone-notch{width:120px;height:28px;background:#1a1a1a;border-radius:0 0 16px 16px;position:absolute;top:0;left:50%;transform:translateX(-50%);z-index:10;display:flex;align-items:center;justify-content:center;gap:6px}
.phone-notch::before{content:'';width:8px;height:8px;background:#333;border-radius:50%}
.phone-notch::after{content:'';width:40px;height:4px;background:#333;border-radius:2px}
.phone-screen{width:100%;height:100%;background:#fff;border-radius:28px;overflow:hidden;position:relative}
.phone-status-bar{height:44px;background:linear-gradient(135deg,#1a1a1a,#2a2a2a);display:flex;align-items:center;justify-content:space-between;padding:0 20px;color:#fff;font-size:11px;font-weight:600}
.phone-status-bar .time{font-size:13px;font-weight:700}
.phone-status-icons{display:flex;gap:4px;align-items:center}
.phone-home-bar{height:34px;display:flex;align-items:center;justify-content:center;background:#fff}
.phone-home-bar::after{content:'';width:120px;height:4px;background:#ddd;border-radius:2px}

/* Game Screen */
.game-screen{flex:1;overflow-y:auto;position:relative}
.game-header{padding:16px 20px;display:flex;align-items:center;gap:12px}
.game-header .back-btn{width:32px;height:32px;border-radius:50%;background:rgba(0,0,0,.06);display:flex;align-items:center;justify-content:center;font-size:14px;color:#333}
.game-header .title{font-size:16px;font-weight:700;color:#1a1a1a}
.game-body{display:flex;flex-direction:column;align-items:center;padding:0 16px 20px}

/* Spin Wheel */
.wheel-wrapper{position:relative;margin:10px auto}
.wheel-canvas-wrap{position:relative}
.wheel-pointer-arrow{position:absolute;top:-18px;left:50%;transform:translateX(-50%);z-index:5}
.wheel-pointer-arrow::after{content:'';display:block;width:0;height:0;border-left:14px solid transparent;border-right:14px solid transparent;border-top:24px solid #FF4444;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3))}
.wheel-center-btn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:60px;height:60px;border-radius:50%;border:4px solid #fff;font-weight:800;font-size:11px;cursor:pointer;box-shadow:0 4px 15px rgba(0,0,0,.3);z-index:5;transition:transform .1s}
.wheel-center-btn:hover{transform:translate(-50%,-50%) scale(1.08)}
.wheel-center-btn:active{transform:translate(-50%,-50%) scale(.95)}

/* Scratch Card */
.scratch-card-container{position:relative;width:100%;max-width:280px;aspect-ratio:1.5/1;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.15);cursor:pointer}
.scratch-prize-layer{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;text-align:center}
.scratch-prize-layer .prize-icon{font-size:48px;margin-bottom:8px}
.scratch-prize-layer .prize-text{font-size:20px;font-weight:800}
.scratch-overlay-layer{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:opacity .5s}
.scratch-overlay-layer .scratch-hint{font-size:14px;font-weight:700;color:#666;margin-top:8px}
.scratch-particles{position:absolute;inset:0;pointer-events:none;overflow:hidden}

/* Slot Machine */
.slot-machine-body{width:100%;max-width:280px;border-radius:20px;padding:20px;position:relative;overflow:hidden}
.slot-machine-body::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.1),transparent);pointer-events:none}
.slot-reels{display:flex;gap:8px;justify-content:center;margin-bottom:16px}
.slot-reel{width:70px;height:90px;background:#fff;border-radius:12px;overflow:hidden;position:relative;box-shadow:inset 0 2px 8px rgba(0,0,0,.15)}
.slot-reel-inner{position:absolute;width:100%;transition:transform .3s;display:flex;flex-direction:column}
.slot-symbol{height:90px;display:flex;align-items:center;justify-content:center;font-size:40px}
.slot-lever{width:100%;padding:12px;border-radius:12px;border:none;font-weight:800;font-size:14px;cursor:pointer;transition:.2s}
.slot-lever:hover{filter:brightness(1.1);transform:translateY(-1px)}
.slot-lever:active{transform:translateY(1px)}

/* Mystery Box */
.mystery-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;width:100%;max-width:260px}
.mystery-box-item{aspect-ratio:1;border-radius:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:transform .3s,box-shadow .3s;position:relative;overflow:hidden}
.mystery-box-item:hover{transform:translateY(-4px);box-shadow:0 8px 25px rgba(0,0,0,.2)}
.mystery-box-item .box-icon{font-size:40px;transition:transform .3s}
.mystery-box-item:hover .box-icon{transform:scale(1.15) rotate(-5deg)}
.mystery-box-item .box-label{font-size:11px;font-weight:700;margin-top:4px;opacity:.7}
.mystery-box-item.revealed{pointer-events:none}
.mystery-box-item.revealed .box-icon{animation:revealBounce .5s}
.mystery-box-item::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.2),transparent);pointer-events:none}

/* Prize Result Modal */
.prize-result-overlay{position:absolute;inset:0;background:rgba(0,0,0,.7);z-index:20;display:none;align-items:center;justify-content:center;backdrop-filter:blur(5px)}
.prize-result-overlay.show{display:flex}
.prize-result-card{background:#fff;border-radius:20px;padding:30px 24px;text-align:center;width:85%;max-width:280px;animation:resultPop .4s ease-out}
.prize-result-card .result-icon{font-size:56px;margin-bottom:10px}
.prize-result-card .result-title{font-size:20px;font-weight:800;margin-bottom:4px}
.prize-result-card .result-desc{font-size:13px;color:#888;margin-bottom:16px}
.prize-result-card .result-btn{width:100%;padding:12px;border:none;border-radius:12px;font-weight:700;font-size:14px;cursor:pointer;color:#fff}

/* Confetti */
.confetti-container{position:absolute;inset:0;pointer-events:none;overflow:hidden;z-index:25}
.confetti-piece{position:absolute;width:8px;height:8px;top:-10px;animation:confettiFall 3s ease-in forwards}

/* Animations */
@keyframes resultPop{from{transform:scale(.5);opacity:0}to{transform:scale(1);opacity:1}}
@keyframes revealBounce{0%{transform:scale(0) rotate(0)}50%{transform:scale(1.3) rotate(10deg)}100%{transform:scale(1) rotate(0)}}
@keyframes confettiFall{0%{transform:translateY(0) rotate(0);opacity:1}100%{transform:translateY(600px) rotate(720deg);opacity:0}}
@keyframes wheelGlow{0%,100%{box-shadow:0 0 20px rgba(255,215,0,.3)}50%{box-shadow:0 0 40px rgba(255,215,0,.6)}}
@keyframes pulse{0%,100%{transform:translate(-50%,-50%) scale(1)}50%{transform:translate(-50%,-50%) scale(1.05)}}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
.shimmer-effect{background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.3) 50%,transparent 100%);background-size:200% 100%;animation:shimmer 2s infinite}

/* Loading Spinner */
.loading-spinner{display:flex;flex-direction:column;align-items:center;justify-content:center;height:200px;padding:20px}
.spinner-circle{width:40px;height:40px;border:3px solid #f3f3f3;border-top:3px solid var(--primary-color,#8DC63F);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:12px}
.spinner-text{font-size:13px;color:#666;font-weight:500}
@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{dynamicAsset('public/assets/admin/img/game.png')}}" class="w--20" alt="">
                </span>
                <span>{{ translate('Preview') }}: {{ $game->name }}</span>
            </h1>
            <a href="{{ route('admin.gamification.edit', $game->id) }}" class="btn btn-secondary">
                <i class="tio-back-ui"></i> {{ translate('Back to Edit') }}
            </a>
        </div>
    </div>

    <!-- Device Selector Toolbar -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="preview-toolbar">
                <strong class="mr-2">{{ translate('Device') }}:</strong>
                <button class="device-btn active" data-w="375" data-h="812">iPhone SE</button>
                <button class="device-btn" data-w="390" data-h="844">iPhone 14</button>
                <button class="device-btn" data-w="430" data-h="932">iPhone 15 Pro Max</button>
                <button class="device-btn" data-w="360" data-h="780">Android Small</button>
                <button class="device-btn" data-w="412" data-h="915">Android Large</button>
                <button class="device-btn" data-w="768" data-h="1024">Tablet</button>
                <span class="ml-auto text-muted" id="sizeLabel" style="font-size:12px;font-weight:600">375 x 812</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Phone Frame -->
            <div class="phone-frame-wrap" id="phoneWrap">
                <div class="phone-frame" id="phoneFrame" style="width:399px;height:836px">
                    <div class="phone-notch"></div>
                    <div class="phone-screen" style="display:flex;flex-direction:column">
                        <!-- Status Bar -->
                        <div class="phone-status-bar">
                            <span class="time" id="statusTime">9:41</span>
                            <div class="phone-status-icons">
                                <svg width="16" height="12" viewBox="0 0 16 12" fill="#fff"><rect x="0" y="6" width="3" height="6" rx="1"/><rect x="4.5" y="4" width="3" height="8" rx="1"/><rect x="9" y="2" width="3" height="10" rx="1"/><rect x="13.5" y="0" width="3" height="12" rx="1" opacity=".3"/></svg>
                                <svg width="16" height="12" viewBox="0 0 16 12" fill="#fff"><path d="M8 2C5 2 2.5 3.5 1 5.5L8 12l7-6.5C13.5 3.5 11 2 8 2z" opacity=".9"/></svg>
                                <svg width="22" height="12" viewBox="0 0 22 12" fill="#fff"><rect x="0" y="1" width="18" height="10" rx="2" fill="none" stroke="#fff" stroke-width="1.5"/><rect x="2" y="3" width="12" height="6" rx="1" fill="#8DC63F"/><rect x="19" y="4" width="2" height="4" rx=".5"/></svg>
                            </div>
                        </div>

                        <!-- Game Screen Content -->
                        <div class="game-screen" id="gameScreen" style="background:linear-gradient(180deg,{{ $game->display_settings['primary_color'] ?? '#8DC63F' }}22,#fff 40%)">
                            <!-- Game Header -->
                            <div class="game-header">
                                <div class="back-btn"><i class="tio-chevron-left"></i></div>
                                <div class="title">{{ $game->name }}</div>
                            </div>

                            <!-- Instructions -->
                            <div style="padding:0 20px 12px;text-align:center">
                                <p style="font-size:12px;color:#888;margin:0">{{ $game->instructions ?? $game->description }}</p>
                            </div>

                            <!-- Game Body -->
                            <div class="game-body" id="gameBody">
                                <!-- Loading Spinner -->
                                <div class="loading-spinner" id="gameLoading">
                                    <div class="spinner-circle"></div>
                                    <div class="spinner-text">{{ translate('Loading game...') }}</div>
                                </div>
                            </div>

                            <!-- Plays Remaining -->
                            <div style="text-align:center;padding:12px 20px">
                                <span style="font-size:11px;color:#aaa;background:#f5f5f5;padding:6px 14px;border-radius:20px">
                                    {{ $game->plays_per_day }} {{ translate('plays remaining today') }}
                                </span>
                            </div>
                        </div>

                        <!-- Prize Result Overlay -->
                        <div class="prize-result-overlay" id="prizeResultOverlay">
                            <div class="confetti-container" id="confettiContainer"></div>
                            <div class="prize-result-card">
                                <div class="result-icon" id="resultIcon">🎉</div>
                                <div class="result-title" id="resultTitle">Congratulations!</div>
                                <div class="result-desc" id="resultDesc">You won a prize!</div>
                                <button class="result-btn" id="resultClaimBtn" style="background:{{ $game->display_settings['primary_color'] ?? '#8DC63F' }}">Claim Prize</button>
                            </div>
                        </div>

                        <!-- Home Bar -->
                        <div class="phone-home-bar"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Game Info -->
            <div class="card">
                <div class="card-header"><h5 class="card-title">{{ translate('Game Info') }}</h5></div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted">{{ translate('Type') }}</td><td class="font-weight-bold">{{ ucwords(str_replace('_',' ',$game->type)) }}</td></tr>
                        <tr><td class="text-muted">{{ translate('Status') }}</td><td><span class="badge badge-{{ $game->status ? 'success' : 'danger' }}">{{ $game->status ? 'Active' : 'Inactive' }}</span></td></tr>
                        <tr><td class="text-muted">{{ translate('Plays/Day') }}</td><td class="font-weight-bold">{{ $game->plays_per_day }}</td></tr>
                        <tr><td class="text-muted">{{ translate('Cooldown') }}</td><td class="font-weight-bold">{{ $game->cooldown_minutes ?: 'None' }}</td></tr>
                        <tr><td class="text-muted">{{ translate('Prizes') }}</td><td class="font-weight-bold">{{ $game->prizes->count() }}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Prize List -->
            <div class="card mt-3">
                <div class="card-header"><h5 class="card-title">{{ translate('Prize Segments') }}</h5></div>
                <div class="card-body p-0">
                    @forelse($game->prizes as $prize)
                    <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div style="width:12px;height:12px;border-radius:3px;background:{{ $prize->color }};margin-right:10px;flex-shrink:0"></div>
                        <div class="flex-grow-1">
                            <div style="font-size:13px;font-weight:600">{{ $prize->name }}</div>
                            <div style="font-size:11px;color:#aaa">{{ $prize->probability }}% &bull; {{ ucwords(str_replace('_',' ',$prize->type)) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">{{ translate('No prizes') }}</div>
                    @endforelse
                </div>
            </div>

            <!-- Colors -->
            <div class="card mt-3">
                <div class="card-header"><h5 class="card-title">{{ translate('Theme') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        @php $ds = $game->display_settings ?? []; @endphp
                        <div class="text-center"><div style="width:48px;height:48px;background:{{ $ds['primary_color'] ?? '#8DC63F' }};border-radius:8px"></div><small class="d-block mt-1">Primary</small></div>
                        <div class="text-center"><div style="width:48px;height:48px;background:{{ $ds['secondary_color'] ?? '#F5D800' }};border-radius:8px"></div><small class="d-block mt-1">Secondary</small></div>
                        <div class="text-center"><div style="width:48px;height:48px;background:{{ $ds['text_color'] ?? '#1A1A1A' }};border-radius:8px"></div><small class="d-block mt-1">Text</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
$(document).ready(function() {
    const PRIMARY = '{{ $game->display_settings["primary_color"] ?? "#8DC63F" }}';
    const SECONDARY = '{{ $game->display_settings["secondary_color"] ?? "#F5D800" }}';
    const TEXT_COLOR = '{{ $game->display_settings["text_color"] ?? "#1A1A1A" }}';
    const GAME_TYPE = '{{ $game->type }}';
    const prizes = @json($prizesData);

    // Update status bar time
    function updateTime(){
        const now = new Date();
        document.getElementById('statusTime').textContent = now.getHours()+':'+String(now.getMinutes()).padStart(2,'0');
    }
    updateTime(); setInterval(updateTime, 60000);

    // Device switcher
    $('.device-btn').on('click', function(){
        $('.device-btn').removeClass('active');
        $(this).addClass('active');
        const w = $(this).data('w'), h = $(this).data('h');
        $('#phoneFrame').css({width:(w+24)+'px',height:(h+24)+'px'});
        $('#sizeLabel').text(w+' x '+h);
        if(GAME_TYPE==='spin_wheel') setTimeout(drawWheel, 350);
    });

    // Confetti
    function showConfetti(){
        const c=$('#confettiContainer').empty();
        const colors=['#FF6B6B','#4ECDC4','#FFE66D','#8DC63F','#FF8A5C','#A78BFA'];
        for(let i=0;i<60;i++){
            $('<div class="confetti-piece">').css({
                left:Math.random()*100+'%',
                background:colors[Math.floor(Math.random()*colors.length)],
                animationDelay:Math.random()*2+'s',
                animationDuration:(2+Math.random()*2)+'s',
                borderRadius:Math.random()>.5?'50%':'0',
                width:(4+Math.random()*8)+'px',
                height:(4+Math.random()*8)+'px'
            }).appendTo(c);
        }
    }

    // Show result
    function showResult(prize){
        const icons={'discount_percentage':'🏷️','discount_fixed':'💰','free_delivery':'🚚','loyalty_points':'⭐','wallet_credit':'💳','free_item':'🍔','mystery':'🎁'};
        $('#resultIcon').text(prize?icons[prize.type]||'🎉':'😢');
        $('#resultTitle').text(prize?'You Won!':'Better Luck Next Time');
        $('#resultDesc').text(prize?prize.name:'Try again later!');
        $('#resultClaimBtn').text(prize?'Claim Prize':'Play Again').off('click').on('click',function(){
            $('#prizeResultOverlay').removeClass('show');
            $('#confettiContainer').empty();
        });
        if(prize) showConfetti();
        $('#prizeResultOverlay').addClass('show');
    }

    // Pick weighted prize
    function pickPrize(){
        if(!prizes.length) return null;
        const total=prizes.reduce((s,p)=>s+p.probability,0);
        let r=Math.random()*total, cum=0;
        for(const p of prizes){cum+=p.probability;if(r<=cum) return p;}
        return null;
    }

    const body=$('#gameBody');

    // =================== SPIN WHEEL ===================
    if(GAME_TYPE==='spin_wheel'){
        let spinning=false, currentAngle=0;
        body.html(`
            <div class="wheel-wrapper">
                <div class="wheel-canvas-wrap">
                    <div class="wheel-pointer-arrow"></div>
                    <canvas id="wheelCanvas" width="280" height="280" style="display:block"></canvas>
                    <button class="wheel-center-btn" id="spinBtn" style="background:${PRIMARY};color:#fff">SPIN</button>
                </div>
            </div>
        `);
        
        // Hide loading spinner
        $('#gameLoading').remove();

        function drawWheel(){
            const canvas=document.getElementById('wheelCanvas');
            if(!canvas) return;
            const rect=canvas.parentElement.getBoundingClientRect();
            const size=Math.min(rect.width, 280);
            canvas.width=size; canvas.height=size;
            const ctx=canvas.getContext('2d'), cx=size/2, cy=size/2, r=size/2-10;
            if(!prizes.length) return;
            const slice=2*Math.PI/prizes.length;
            ctx.clearRect(0,0,size,size);
            ctx.save(); ctx.translate(cx,cy); ctx.rotate(currentAngle*(Math.PI/180));
            prizes.forEach((p,i)=>{
                const start=i*slice, end=start+slice;
                ctx.beginPath(); ctx.moveTo(0,0); ctx.arc(0,0,r,start,end); ctx.closePath();
                ctx.fillStyle=p.color; ctx.fill();
                ctx.strokeStyle='rgba(255,255,255,0.8)'; ctx.lineWidth=2; ctx.stroke();
                ctx.save(); ctx.rotate(start+slice/2);
                ctx.fillStyle='#fff'; ctx.font='bold '+Math.max(9,Math.min(13,r/8))+'px Arial';
                ctx.textAlign='center'; ctx.textBaseline='middle';
                const txt=p.name.length>10?p.name.substring(0,9)+'..':p.name;
                ctx.fillText(txt,r*0.6,0);
                ctx.restore();
            });
            ctx.restore();
            // center
            ctx.beginPath(); ctx.arc(cx,cy,24,0,2*Math.PI);
            ctx.fillStyle='#fff'; ctx.fill();
            ctx.strokeStyle=PRIMARY; ctx.lineWidth=3; ctx.stroke();
        }
        drawWheel();

        $(document).on('click','#spinBtn',function(){
            if(spinning) return;
            spinning=true;
            const prize=pickPrize();
            const prizeIdx=prize?prizes.indexOf(prize):0;
            const sliceDeg=360/prizes.length;
            const targetAngle=360*5+(360-prizeIdx*sliceDeg-sliceDeg/2);
            const startAngle=currentAngle;
            const duration=4000;
            const startTime=Date.now();
            function animate(){
                const elapsed=Date.now()-startTime;
                const progress=Math.min(elapsed/duration,1);
                const ease=1-Math.pow(1-progress,4);
                currentAngle=startAngle+ease*(targetAngle-startAngle);
                drawWheel();
                if(progress<1) requestAnimationFrame(animate);
                else{spinning=false;setTimeout(()=>showResult(prize),400);}
            }
            animate();
        });
    }

    // =================== SCRATCH CARD ===================
    else if(GAME_TYPE==='scratch_card'){
        const prize=pickPrize();
        body.html(`
            <div class="scratch-card-container" id="scratchCard">
                <div class="scratch-prize-layer" style="background:linear-gradient(135deg,${PRIMARY}33,${SECONDARY}33)">
                    <div class="prize-icon">${prize?'🎉':'😢'}</div>
                    <div class="prize-text" style="color:${TEXT_COLOR}">${prize?prize.name:'No Prize'}</div>
                    <div style="font-size:12px;color:#888;margin-top:4px">${prize?'Tap claim to redeem!':'Better luck next time'}</div>
                </div>
                <canvas id="scratchCanvas" style="position:absolute;inset:0;cursor:pointer"></canvas>
            </div>
            <p style="font-size:12px;color:#aaa;margin-top:12px">Scratch the card to reveal your prize!</p>
        `);
        
        // Hide loading spinner
        $('#gameLoading').remove();
        
        setTimeout(()=>{
            const card=document.getElementById('scratchCard');
            const canvas=document.getElementById('scratchCanvas');
            if(!canvas||!card) return;
            canvas.width=card.offsetWidth; canvas.height=card.offsetHeight;
            const ctx=canvas.getContext('2d');
            // Draw scratch overlay
            const grd=ctx.createLinearGradient(0,0,canvas.width,canvas.height);
            grd.addColorStop(0,'#C0C0C0'); grd.addColorStop(0.5,'#D8D8D8'); grd.addColorStop(1,'#B0B0B0');
            ctx.fillStyle=grd; ctx.fillRect(0,0,canvas.width,canvas.height);
            ctx.fillStyle='#999'; ctx.font='bold 16px Arial'; ctx.textAlign='center';
            ctx.fillText('SCRATCH HERE',canvas.width/2,canvas.height/2-8);
            ctx.font='24px Arial'; ctx.fillText('🪙',canvas.width/2,canvas.height/2+24);
            let isDown=false, scratched=0, total=canvas.width*canvas.height;
            function scratch(x,y){
                ctx.globalCompositeOperation='destination-out';
                ctx.beginPath(); ctx.arc(x,y,20,0,2*Math.PI); ctx.fill();
                const d=ctx.getImageData(0,0,canvas.width,canvas.height).data;
                let clear=0;
                for(let i=3;i<d.length;i+=4) if(d[i]===0) clear++;
                if(clear/(d.length/4)>0.5){canvas.style.opacity='0';setTimeout(()=>{canvas.remove();showResult(prize);},500);}
            }
            canvas.addEventListener('mousedown',e=>{isDown=true;const r=canvas.getBoundingClientRect();scratch(e.clientX-r.left,e.clientY-r.top);});
            canvas.addEventListener('mousemove',e=>{if(!isDown)return;const r=canvas.getBoundingClientRect();scratch(e.clientX-r.left,e.clientY-r.top);});
            canvas.addEventListener('mouseup',()=>isDown=false);
            canvas.addEventListener('mouseleave',()=>isDown=false);
            canvas.addEventListener('touchstart',e=>{e.preventDefault();isDown=true;const r=canvas.getBoundingClientRect();const t=e.touches[0];scratch(t.clientX-r.left,t.clientY-r.top);},{passive:false});
            canvas.addEventListener('touchmove',e=>{e.preventDefault();if(!isDown)return;const r=canvas.getBoundingClientRect();const t=e.touches[0];scratch(t.clientX-r.left,t.clientY-r.top);},{passive:false});
            canvas.addEventListener('touchend',()=>isDown=false);
        },100);
    }

    // =================== SLOT MACHINE ===================
    else if(GAME_TYPE==='slot_machine'){
        const emojis=['🍒','🍋','🍊','🎁','⭐','💎','🔔','🍀'];
        body.html(`
            <div class="slot-machine-body" style="background:linear-gradient(135deg,${PRIMARY},${SECONDARY})">
                <div style="text-align:center;margin-bottom:12px;font-size:18px;font-weight:800;color:#fff;text-shadow:0 2px 4px rgba(0,0,0,.2)">LUCKY SLOTS</div>
                <div class="slot-reels">
                    <div class="slot-reel"><div class="slot-reel-inner" id="reel0"></div></div>
                    <div class="slot-reel"><div class="slot-reel-inner" id="reel1"></div></div>
                    <div class="slot-reel"><div class="slot-reel-inner" id="reel2"></div></div>
                </div>
                <button class="slot-lever" id="slotPull" style="background:#fff;color:${TEXT_COLOR}">🎰 PULL LEVER</button>
            </div>
        `);
        
        // Hide loading spinner
        $('#gameLoading').remove();
        
        // Fill reels
        for(let r=0;r<3;r++){
            let html='';
            for(let i=0;i<20;i++) html+=`<div class="slot-symbol">${emojis[Math.floor(Math.random()*emojis.length)]}</div>`;
            $(`#reel${r}`).html(html);
        }
        let slotSpinning=false;
        $(document).on('click','#slotPull',function(){
            if(slotSpinning) return;
            slotSpinning=true;
            const prize=pickPrize();
            for(let r=0;r<3;r++){
                const reel=$(`#reel${r}`);
                const offset=-(Math.floor(Math.random()*10+5))*90;
                reel.css({transition:'none',transform:'translateY(0)'});
                setTimeout(()=>{
                    reel.css({transition:`transform ${1.5+r*0.5}s cubic-bezier(.2,.8,.3,1)`,transform:`translateY(${offset}px)`});
                },50);
            }
            setTimeout(()=>{slotSpinning=false;showResult(prize);},3500);
        });
    }

    // =================== MYSTERY BOX ===================
    else if(GAME_TYPE==='mystery_box'){
        const boxEmojis=['🎁','🎀','📦','🎊'];
        let html='<div class="mystery-grid">';
        for(let i=0;i<4;i++){
            html+=`<div class="mystery-box-item" data-idx="${i}" style="background:linear-gradient(135deg,${PRIMARY},${SECONDARY})">
                <div class="box-icon">${boxEmojis[i]}</div>
                <div class="box-label" style="color:#fff">Box ${i+1}</div>
            </div>`;
        }
        html+='</div><p style="font-size:12px;color:#aaa;margin-top:12px;text-align:center">Tap a box to reveal your prize!</p>';
        body.html(html);
        
        // Hide loading spinner
        $('#gameLoading').remove();
        
        let boxOpened=false;
        $(document).on('click','.mystery-box-item',function(){
            if(boxOpened) return;
            boxOpened=true;
            const box=$(this);
            const prize=pickPrize();
            // Shake animation
            box.css({animation:'none'});
            setTimeout(()=>box.css({animation:'revealBounce .6s'}),10);
            box.find('.box-icon').text(prize?'🎉':'😢');
            box.find('.box-label').text(prize?prize.name:'Empty');
            box.addClass('revealed');
            // Dim others
            $('.mystery-box-item').not(box).css({opacity:.4,filter:'grayscale(1)'});
            setTimeout(()=>showResult(prize),800);
        });
    }

    // =================== DECISION ROULETTE ===================
    else{
        body.html(`
            <div class="wheel-wrapper">
                <div class="wheel-canvas-wrap">
                    <div class="wheel-pointer-arrow"></div>
                    <canvas id="wheelCanvas" width="280" height="280" style="display:block"></canvas>
                    <button class="wheel-center-btn" id="spinBtn" style="background:${PRIMARY};color:#fff">GO</button>
                </div>
            </div>
        `);
        
        // Hide loading spinner
        $('#gameLoading').remove();
        
        let spinning=false, currentAngle=0;
        function drawWheel(){
            const canvas=document.getElementById('wheelCanvas');
            if(!canvas) return;
            const size=280; canvas.width=size; canvas.height=size;
            const ctx=canvas.getContext('2d'), cx=size/2, cy=size/2, r=size/2-10;
            const segments=prizes.length||3;
            const slice=2*Math.PI/segments;
            const defaultColors=[PRIMARY,SECONDARY,'#FF6B6B','#4ECDC4','#A78BFA','#FFE66D'];
            ctx.clearRect(0,0,size,size);
            ctx.save(); ctx.translate(cx,cy); ctx.rotate(currentAngle*(Math.PI/180));
            for(let i=0;i<segments;i++){
                const start=i*slice, end=start+slice;
                ctx.beginPath(); ctx.moveTo(0,0); ctx.arc(0,0,r,start,end); ctx.closePath();
                ctx.fillStyle=prizes[i]?prizes[i].color:defaultColors[i%defaultColors.length]; ctx.fill();
                ctx.strokeStyle='rgba(255,255,255,.8)'; ctx.lineWidth=2; ctx.stroke();
                ctx.save(); ctx.rotate(start+slice/2);
                ctx.fillStyle='#fff'; ctx.font='bold 11px Arial'; ctx.textAlign='center';
                const n=prizes[i]?prizes[i].name:'Prize '+(i+1);
                ctx.fillText(n.length>10?n.substring(0,9)+'..':n,r*0.6,0);
                ctx.restore();
            }
            ctx.restore();
            ctx.beginPath(); ctx.arc(cx,cy,20,0,2*Math.PI);
            ctx.fillStyle='#fff'; ctx.fill(); ctx.strokeStyle=PRIMARY; ctx.lineWidth=3; ctx.stroke();
        }
        drawWheel();
        $(document).on('click','#spinBtn',function(){
            if(spinning) return; spinning=true;
            const prize=pickPrize();
            const idx=prize?prizes.indexOf(prize):0;
            const sliceDeg=360/(prizes.length||3);
            const target=360*5+(360-idx*sliceDeg-sliceDeg/2);
            const start=currentAngle, dur=4000, st=Date.now();
            function anim(){
                const p=Math.min((Date.now()-st)/dur,1);
                currentAngle=start+(1-Math.pow(1-p,4))*(target-start);
                drawWheel();
                if(p<1) requestAnimationFrame(anim);
                else{spinning=false;setTimeout(()=>showResult(prize),400);}
            }
            anim();
        });
    }
});
</script>
@endpush
