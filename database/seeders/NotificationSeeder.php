<?php

namespace Database\Seeders;

use App\Models\Notifications;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 1',
                'message' => 'Ini Notification 1 ya',
                'type' => 'passed',
                'path' => '/bo-info',
                'is_read' => 0,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 2',
                'message' => 'Ini Notification 2 ya',
                'type' => 'failed',
                'path' => '/legal-document',
                'is_read' => 1,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 3',
                'message' => 'Ini Notification 3 ya',
                'type' => 'info',
                'path' => '/daftar-produk',
                'is_read' => 0,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 4',
                'message' => 'Ini Notification 4 ya',
                'type' => 'passed',
                'path' => '/legal-document',
                'is_read' => 1,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 5',
                'message' => 'Ini Notification 5 ya',
                'type' => 'info',
                'path' => '/bo-info',
                'is_read' => 0,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 6',
                'message' => 'Ini Notification 6 ya',
                'type' => 'failed',
                'path' => '/daftar-produk',
                'is_read' => 1,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 7',
                'message' => 'Ini Notification 7 ya',
                'type' => 'info',
                'path' => '/bo-info',
                'is_read' => 0,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 8',
                'message' => 'Ini Notification 8 ya',
                'type' => 'passed',
                'path' => '/legal-document',
                'is_read' => 1,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 9',
                'message' => 'Ini Notification 9 ya',
                'type' => 'failed',
                'path' => '/daftar-produk',
                'is_read' => 0,
            ],
            [
                'bisnis_owner_id' => 1,
                'title' => 'Notification 10',
                'message' => 'Ini Notification 10 ya',
                'type' => 'info',
                'path' => '/legal-document',
                'is_read' => 1,
            ],
        ];

        foreach ($notifications as $notification) {
            Notifications::create($notification);
        }
    }
}
