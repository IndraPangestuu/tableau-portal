<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'tableau_view_path',
        'tableau_username',
        'order',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get parent menu
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get child menus
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get active child menus
     */
    public function activeChildren()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Check if menu has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if menu is a parent (has no tableau_view_path)
     */
    public function isParent()
    {
        return empty($this->tableau_view_path) || $this->hasChildren();
    }

    /**
     * Scope for active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Scope for parent menus only (no parent_id)
     */
    public function scopeParentMenus($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Scope for active parent menus with active children
     */
    public function scopeActiveParentMenus($query)
    {
        return $query->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['activeChildren'])
            ->orderBy('order');
    }
}
