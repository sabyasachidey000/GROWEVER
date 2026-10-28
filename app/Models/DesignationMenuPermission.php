<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationMenuPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id', 'menu_id', 'can_view',
    ];

    protected $casts = [
        'can_view' => 'boolean',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
