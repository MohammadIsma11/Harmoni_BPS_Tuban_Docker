<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeApproval extends Model
{
    use HasFactory;

    protected $fillable = ['article_id', 'status', 'reviewer_id', 'notes'];

    public function article()
    {
        return $this->belongsTo(KnowledgeArticle::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
