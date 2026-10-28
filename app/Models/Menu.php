<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_key', 'name', 'section', 'route_name', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function designationPermissions()
    {
        return $this->hasMany(DesignationMenuPermission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
