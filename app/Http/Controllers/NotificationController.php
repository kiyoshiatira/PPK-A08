<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * FR-12: Menampilkan seluruh notifikasi milik pengguna/petugas
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * FR-12: Tandai 1 notifikasi sebagai sudah dibaca
     */
    public function markAsRead(Notification $notification)
    {
        // Pastikan notifikasi milik user yang sedang login
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * FR-12: Tandai SEMUA notifikasi milik user sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }
}
