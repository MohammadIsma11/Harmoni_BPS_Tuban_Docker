<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'author_id', 'title', 'slug', 'content', 'view_count', 'is_published'];

    public function category()
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function approval()
    {
        return $this->hasOne(KnowledgeApproval::class, 'article_id');
    }
}
