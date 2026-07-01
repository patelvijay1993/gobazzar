<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'icon', 'type', 'is_active', 'sort_order'];

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    // ── Sub-category support ──────────────────────────────────────
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CategoryField::class)->orderBy('sort_order');
    }

    /**
     * All custom fields that apply when posting under this category —
     * the category's own fields PLUS its parent's fields (inherited).
     */
    public function applicableFields()
    {
        $fields = $this->fields()->get();
        if ($this->parent_id) {
            $parentFields = CategoryField::where('category_id', $this->parent_id)
                ->orderBy('sort_order')->get();
            // Parent fields first, then sub-category-specific
            $fields = $parentFields->concat($fields);
        }
        return $fields->unique('key')->values();
    }

    /** Top-level categories (no parent) */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /** Sub-categories only */
    public function scopeSubs($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }
}
