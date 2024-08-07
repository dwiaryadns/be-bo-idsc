<?php

use App\Models\ActivityLog;
use Carbon\Carbon;

if (!function_exists('log_activity')) {
    /**
     * Fungsi untuk menyimpan log aktivitas ke database.
     *
     * @param string $activity Aktivitas yang dilakukan
     * @param string $menu Menu atau bagian aplikasi
     * @param string $activityBy Siapa yang melakukan aktivitas
     */
    function log_activity($activity, $menu, $activityBy, $status)
    {
        ActivityLog::create([
            'activity' => $activity,
            'menu' => $menu,
            'activity_by' => $activityBy,
            'status' => $status == 1 ? 'Berhasil' : 'Gagal',
            'activity_at' => Carbon::now(),
        ]);
    }
}
