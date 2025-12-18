<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    public $timestamps = false;

    protected $rememberTokenName = 'login_session_key';

    public function getRouteKeyName()
    {
        return 'id_user';
    }

    protected $fillable = [
        'username',
        'password',
        'nama',
        'telp',
        'email',
        'foto',
        'account_status',
        'user_role_id',
        'allowed_menus',
    ];

    protected $hidden = [
        'password',
        'login_session_key',
    ];

    protected $casts = [
        'allowed_menus' => 'array',
    ];

    public function getNameAttribute()
    {
        return $this->nama;
    }

    public function getRoleAttribute()
    {
        return $this->user_role_id == 1 ? 'admin' : 'user';
    }

    public function isAdmin()
    {
        return $this->user_role_id == 1;
    }

    /**
     * Get user's favorite menus
     */
    public function favorites()
    {
        return $this->hasMany(UserFavorite::class, 'user_id', 'id_user');
    }

    /**
     * Get user's recent dashboards
     */
    public function recentDashboards()
    {
        return $this->hasMany(RecentDashboard::class, 'user_id', 'id_user');
    }

    /**
     * Check if user can access a menu
     */
    public function canAccessMenu(int $menuId): bool
    {
        // Admin can access all menus
        if ($this->isAdmin()) {
            return true;
        }

        // If no restrictions set, allow all
        if (empty($this->allowed_menus)) {
            return true;
        }

        return in_array($menuId, $this->allowed_menus);
    }

    /**
     * Get accessible menus for user
     */
    public function getAccessibleMenus()
    {
        if ($this->isAdmin() || empty($this->allowed_menus)) {
            return Menu::activeParentMenus()->get();
        }

        return Menu::activeParentMenus()
            ->where(function ($query) {
                $query->whereIn('id', $this->allowed_menus)
                    ->orWhereHas('children', function ($q) {
                        $q->whereIn('id', $this->allowed_menus);
                    });
            })
            ->get();
    }

    /**
     * Check if menu is favorite
     */
    public function hasFavorite(int $menuId): bool
    {
        return $this->favorites()->where('menu_id', $menuId)->exists();
    }

    /**
     * Toggle favorite menu
     */
    public function toggleFavorite(int $menuId): bool
    {
        $favorite = $this->favorites()->where('menu_id', $menuId)->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        }

        $this->favorites()->create(['menu_id' => $menuId]);
        return true;
    }
}
