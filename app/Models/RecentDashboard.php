<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentDashboard extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'menu_id', 'accessed_at'];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Record dashboard access
     */
    public static function recordAccess(int $userId, int $menuId): void
    {
        self::updateOrCreate(
            ['user_id' => $userId, 'menu_id' => $menuId],
            ['accessed_at' => now()]
        );

        // Keep only last 10 recent dashboards per user
        $oldRecords = self::where('user_id', $userId)
            ->orderBy('accessed_at', 'desc')
            ->skip(10)
            ->take(100)
            ->pluck('id');

        if ($oldRecords->isNotEmpty()) {
            self::whereIn('id', $oldRecords)->delete();
        }
    }

    /**
     * Get recent dashboards for user
     */
    public static function getRecent(int $userId, int $limit = 5)
    {
        return self::where('user_id', $userId)
            ->with('menu')
            ->orderBy('accessed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
