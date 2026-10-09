@extends('layouts.petugas')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 25px; }

    .card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
    .card-header { padding: 18px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
    .card-title { font-size: 15px; font-weight: 700; margin: 0; color: #222; }

    .notif-list { list-style: none; margin: 0; padding: 0; }
    .notif-item { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; display: flex; gap: 15px; align-items: flex-start; transition: background 0.2s; }
    .notif-item.unread { background: #f0f7ff; }
    .notif-item:last-child { border-bottom: none; }

    .notif-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .icon-reservasi { background: #e3f2fd; color: #1565c0; }
    .icon-laporan   { background: #fff3e0; color: #e65100; }
    .icon-sistem    { background: #f3e5f5; color: #7b1fa2; }

    .notif-content { flex: 1; }
    .notif-title { font-size: 14px; font-weight: 600; color: #111; margin-bottom: 4px; display: flex; justify-content: space-between; align-items: center; }
    .notif-message { font-size: 13px; color: #444; line-height: 1.5; margin-bottom: 6px; }
    .notif-time { font-size: 11px; color: #888; }

    .btn-read-all { background: #fff; border: 1px solid #d0d0d0; padding: 6px 14px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #444; cursor: pointer; }
    .btn-read-all:hover { background: #f5f5f5; color: #111; }
</style>

<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
            <h1 class="page-title">Pemberitahuan System</h1>
            <p class="page-subtitle">Daftar notifikasi terbaru terkait aktivitas reservasi dan laporan kerusakan Anda.</p>
        </div>
        <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" class="btn-read-all">✓ Tandai Semua Dibaca</button>
        </form>
    </div>

    @if(session('success'))
        <div style="background:#e8f5e9; color:#2e7d32; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #c8e6c9;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Notifikasi</h3>
        </div>
        <ul class="notif-list">
            @forelse($notifications as $notif)
                <li class="notif-item {{ !$notif->is_read ? 'unread' : '' }}">
                    <div class="notif-icon {{ $notif->type === 'reservasi' ? 'icon-reservasi' : ($notif->type === 'laporan' ? 'icon-laporan' : 'icon-sistem') }}">
                        @if($notif->type === 'reservasi') 📅 @elseif($notif->type === 'laporan') 🔧 @else 🔔 @endif
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">
                            <span>{{ $notif->title }}</span>
                            <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="notif-message">{{ $notif->message }}</div>
                        <div style="display:flex; gap:12px; align-items:center;">
                            @if($notif->link)
                                <a href="{{ route('notifications.markAsRead', $notif->id) }}" style="font-size:12px; font-weight:600; color:#1565c0; text-decoration:none;">
                                    Lihat Detail ↗
                                </a>
                            @endif
                            @if(!$notif->is_read)
                                <a href="{{ route('notifications.markAsRead', $notif->id) }}" style="color:#666; font-size:12px; text-decoration:underline;">
                                    Tandai dibaca
                                </a>
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li style="text-align:center; padding:40px; color:#888; font-size:14px;">
                    Belum ada notifikasi untuk Anda.
                </li>
            @endforelse
        </ul>

        @if($notifications->hasPages())
            <div style="padding:15px 20px; border-top:1px solid #f0f0f0;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
