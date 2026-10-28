<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'employee_id', 'name', 'email', 'password',
        'status', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->hasMany(\App\Models\Notification::class)->whereNull('read_at')->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Determines both sidebar visibility and route access for a registered menu.
     */
    public function hasMenuPermission(string $menuKey): bool
    {
        static $registeredMenus = [];

        if (! array_key_exists($menuKey, $registeredMenus)) {
            $registeredMenus[$menuKey] = Menu::active()
                ->where('menu_key', $menuKey)
                ->value('id');
        }

        $menuId = $registeredMenus[$menuKey];

        if (! $menuId) {
            Log::warning('Unregistered menu permission key requested.', [
                'menu_key' => $menuKey,
                'user_id' => $this->id,
            ]);

            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $designationId = $this->employee?->designation_id;

        if (! $designationId) {
            return false;
        }

        return DesignationMenuPermission::query()
            ->where('designation_id', $designationId)
            ->where('menu_id', $menuId)
            ->where('can_view', true)
            ->exists();
    }

    public function markLogin($ip = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }
}
