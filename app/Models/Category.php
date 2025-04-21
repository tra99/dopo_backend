<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'parent_id', 'type', 'school_type_id', 'status'];

    /**
     * Get the parent category (if any).
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')
            ->select(['id', 'title', 'parent_id', 'type']);
    }

    /**
     * Get all child categories recursively.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->select(['id', 'title', 'parent_id'])
            ->with('children:id,title,parent_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function schoolType()
    {
        return $this->belongsTo(CategoryGroups::class, 'school_type_id')->select(['id', 'school_type_en', 'school_type_kh']);
    }

    public function achieves()
    {
        return $this->belongsToMany(
            Achievement::class,
            'achieve_category_or_support_phrase', // pivot table name
            'category_id',                        // foreign key on the pivot table referencing Category
            'achieve_id'                         // foreign key on the pivot table referencing Achieve
        );
    }
}
