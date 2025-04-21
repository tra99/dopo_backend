<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportPhase extends Model
{
    use HasFactory;

    protected $guarded  = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['title', 'parent_id', 'status', 'school_type_id'];

    /**
     * Define the parent relationship.
     */
    public function parent()
    {
        return $this->belongsTo(SupportPhase::class, 'parent_id')->select(['id', 'title']);
    }

    /**
     * Define the children relationship.
     */
    public function children()
    {
        return $this->hasMany(SupportPhase::class, 'parent_id')
            ->select(['id', 'title', 'parent_id'])
            ->with('children:id,title,parent_id');
    }
}
