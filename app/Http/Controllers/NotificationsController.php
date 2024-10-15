<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $latestNotif = Notifications::where('bisnis_owner_id', $bo->id)
            ->limit(5)
            ->get();

        $arrayLatestNotif = [];
        foreach ($latestNotif as $latest) {
            $arrayLatestNotif[] = [
                'id' => $latest->id,
                'title' => $latest->title,
                'message' => $latest->message,
                'type' => $latest->type,
                'path' => $latest->path,
                'is_read' => $latest->is_read,
                'created_at' => Carbon::parse($latest->created_at)->translatedFormat('d M - H:i'),
            ];
        }

        $count_unread = Notifications::where('bisnis_owner_id', $bo->id)
            ->where('is_read', 0)
            ->count();

        $allNotif = Notifications::where('bisnis_owner_id', $bo->id)->get();

        $arrayAllNotif = [];
        foreach ($allNotif as $all) {
            $arrayAllNotif[] = [
                'id' => $all->id,
                'title' => $all->title,
                'message' => $all->message,
                'type' => $all->type,
                'path' => $all->path,
                'is_read' => $all->is_read,
                'created_at' => Carbon::parse($all->created_at)->translatedFormat('d M - H:i'),
            ];
        }

        return response()->json([
            'status' => true,
            'unread_notif' => $count_unread,
            'latest_notif' => $arrayLatestNotif,
            'all_notif' => $arrayAllNotif
        ], 200);
    }

    public function read_notif(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $notif = Notifications::find($request->id);
        if (!$notif) {
            return response()->json([
                'status' => false,
                'message' => 'Notifikasi tidak ditemukan.'
            ], 404);
        }

        $notif->is_read = 1;
        $notif->save();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil membaca notifikasi.'
        ], 200);
    }
}
