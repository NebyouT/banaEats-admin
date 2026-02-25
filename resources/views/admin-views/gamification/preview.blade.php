@extends('layouts.admin.app')

@section('title', translate('Game Preview'))

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

    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="background: url('{{ $game->background_image_full_url }}') center/cover; min-height: 600px; position: relative;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="background: rgba(0,0,0,0.3); backdrop-filter: blur(5px);">
                    
                    @if($game->type == 'spin_wheel')
                    <!-- Spin Wheel Preview -->
                    <div class="wheel-container" style="position: relative; width: 400px; height: 400px;">
                        <canvas id="wheelCanvas" width="400" height="400"></canvas>
                        <div class="wheel-pointer" style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-top: 30px solid {{ $game->display_settings['primary_color'] ?? '#8DC63F' }};"></div>
                        <button class="spin-button btn btn-lg" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80px; height: 80px; border-radius: 50%; background: {{ $game->display_settings['primary_color'] ?? '#8DC63F' }}; color: {{ $game->display_settings['text_color'] ?? '#1A1A1A' }}; border: 5px solid white; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                            SPIN
                        </button>
                    </div>
                    @elseif($game->type == 'scratch_card')
                    <!-- Scratch Card Preview -->
                    <div class="scratch-card" style="width: 400px; height: 300px; background: linear-gradient(135deg, {{ $game->display_settings['primary_color'] ?? '#8DC63F' }}, {{ $game->display_settings['secondary_color'] ?? '#F5D800' }}); border-radius: 15px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <div class="scratch-overlay" style="position: absolute; inset: 0; background: silver; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <p style="color: #666; font-size: 24px; font-weight: bold;">SCRATCH TO REVEAL</p>
                        </div>
                        <div class="scratch-content" style="padding: 40px; text-align: center; color: {{ $game->display_settings['text_color'] ?? '#1A1A1A' }};">
                            <h2>Your Prize!</h2>
                            <p class="mt-3">Prize details will appear here</p>
                        </div>
                    </div>
                    @elseif($game->type == 'slot_machine')
                    <!-- Slot Machine Preview -->
                    <div class="slot-machine" style="background: linear-gradient(135deg, {{ $game->display_settings['primary_color'] ?? '#8DC63F' }}, {{ $game->display_settings['secondary_color'] ?? '#F5D800' }}); padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <div class="slots-container d-flex gap-3 mb-4">
                            @for($i = 0; $i < 3; $i++)
                            <div class="slot" style="width: 100px; height: 120px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 48px; box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);">
                                🎁
                            </div>
                            @endfor
                        </div>
                        <button class="btn btn-lg btn-light" style="width: 100%; font-weight: bold;">PULL LEVER</button>
                    </div>
                    @elseif($game->type == 'mystery_box')
                    <!-- Mystery Box Preview -->
                    <div class="mystery-boxes d-flex gap-4">
                        @for($i = 0; $i < 3; $i++)
                        <div class="mystery-box" style="width: 150px; height: 150px; background: linear-gradient(135deg, {{ $game->display_settings['primary_color'] ?? '#8DC63F' }}, {{ $game->display_settings['secondary_color'] ?? '#F5D800' }}); border-radius: 15px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.3s; box-shadow: 0 5px 20px rgba(0,0,0,0.3);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                            <span style="font-size: 64px;">🎁</span>
                        </div>
                        @endfor
                    </div>
                    @else
                    <!-- Decision Roulette Preview -->
                    <div class="roulette-container text-center">
                        <div class="roulette-wheel" style="width: 300px; height: 300px; background: conic-gradient(from 0deg, {{ $game->display_settings['primary_color'] ?? '#8DC63F' }} 0deg 120deg, {{ $game->display_settings['secondary_color'] ?? '#F5D800' }} 120deg 240deg, #FF6B6B 240deg 360deg); border-radius: 50%; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);"></div>
                        <button class="btn btn-lg mt-4" style="background: {{ $game->display_settings['primary_color'] ?? '#8DC63F' }}; color: {{ $game->display_settings['text_color'] ?? '#1A1A1A' }}; font-weight: bold; padding: 15px 40px;">SPIN</button>
                    </div>
                    @endif

                    <div class="game-info mt-5 text-center" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 10px; max-width: 500px;">
                        <h3 style="color: {{ $game->display_settings['text_color'] ?? '#1A1A1A' }};">{{ $game->name }}</h3>
                        <p class="text-muted">{{ $game->description }}</p>
                        @if($game->instructions)
                        <small class="text-muted d-block mt-2">{{ $game->instructions }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Game Settings') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>{{ translate('Type') }}:</strong> {{ $game->type_name }}</li>
                        <li class="mb-2"><strong>{{ translate('Status') }}:</strong> 
                            <span class="badge badge-{{ $game->status ? 'success' : 'danger' }}">
                                {{ $game->status ? translate('Active') : translate('Inactive') }}
                            </span>
                        </li>
                        <li class="mb-2"><strong>{{ translate('Plays Per Day') }}:</strong> {{ $game->plays_per_day }}</li>
                        @if($game->cooldown_minutes)
                        <li class="mb-2"><strong>{{ translate('Cooldown') }}:</strong> {{ $game->cooldown_minutes }} {{ translate('minutes') }}</li>
                        @endif
                        <li class="mb-2"><strong>{{ translate('Total Prizes') }}:</strong> {{ $game->prizes->count() }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Prizes') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($game->prizes as $prize)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs mr-2" style="background-color: {{ $prize->color }}"></div>
                                            <div>
                                                <strong>{{ $prize->name }}</strong>
                                                <div class="small text-muted">{{ $prize->probability }}% chance</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center py-3 text-muted">
                                        {{ translate('No prizes added yet') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Color Scheme') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <div class="text-center">
                            <div style="width: 60px; height: 60px; background: {{ $game->display_settings['primary_color'] ?? '#8DC63F' }}; border-radius: 10px; margin-bottom: 5px;"></div>
                            <small>{{ translate('Primary') }}</small>
                        </div>
                        <div class="text-center">
                            <div style="width: 60px; height: 60px; background: {{ $game->display_settings['secondary_color'] ?? '#F5D800' }}; border-radius: 10px; margin-bottom: 5px;"></div>
                            <small>{{ translate('Secondary') }}</small>
                        </div>
                        <div class="text-center">
                            <div style="width: 60px; height: 60px; background: {{ $game->display_settings['text_color'] ?? '#1A1A1A' }}; border-radius: 10px; margin-bottom: 5px;"></div>
                            <small>{{ translate('Text') }}</small>
                        </div>
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
    @if($game->type == 'spin_wheel' && $game->prizes->count() > 0)
    // Draw spin wheel
    const canvas = document.getElementById('wheelCanvas');
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 180;
    
    const prizes = @json($game->prizes->map(function($p) {
        return ['name' => $p->name, 'color' => $p->color];
    }));
    
    const sliceAngle = (2 * Math.PI) / prizes.length;
    
    prizes.forEach((prize, index) => {
        const startAngle = index * sliceAngle;
        const endAngle = startAngle + sliceAngle;
        
        // Draw slice
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.closePath();
        ctx.fillStyle = prize.color;
        ctx.fill();
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = 3;
        ctx.stroke();
        
        // Draw text
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate(startAngle + sliceAngle / 2);
        ctx.textAlign = 'center';
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 14px Arial';
        ctx.fillText(prize.name, radius / 1.5, 0);
        ctx.restore();
    });
    
    // Draw center circle
    ctx.beginPath();
    ctx.arc(centerX, centerY, 40, 0, 2 * Math.PI);
    ctx.fillStyle = '#fff';
    ctx.fill();
    ctx.strokeStyle = '{{ $game->display_settings["primary_color"] ?? "#8DC63F" }}';
    ctx.lineWidth = 5;
    ctx.stroke();
    @endif
});
</script>
@endpush
