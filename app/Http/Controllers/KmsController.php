<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KmsController extends Controller
{
    /**
     * PUBLIC: Home & Search
     */
    public function publicIndex(Request $request)
    {
        $query = KnowledgeArticle::where('is_published', true)->with('category');
        
        if ($request->has('q')) {
            $query->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('content', 'like', '%' . $request->q . '%');
        }

        $articles = $query->latest()->paginate(9);
        $categories = KnowledgeCategory::withCount(['articles' => fn($q) => $q->where('is_published', true)])->get();
        
        return view('kms.public.index', compact('articles', 'categories'));
    }

    /**
     * PUBLIC: View Article
     */
    public function show(KnowledgeArticle $article)
    {
        if (!$article->is_published && !Auth::check()) {
            abort(404);
        }

        $article->increment('view_count');
        return view('kms.public.show', compact('article'));
    }

    /**
     * ADMIN: List Articles for Management
     */
    public function adminIndex()
    {
        $articles = KnowledgeArticle::with('category', 'author')->latest()->paginate(10);
        return view('kms.admin.index', compact('articles'));
    }

    /**
     * ADMIN: Edit Article
     */
    public function edit(KnowledgeArticle $article)
    {
        $categories = KnowledgeCategory::all();
        return view('kms.admin.edit', compact('article', 'categories'));
    }

    /**
     * ADMIN: Update/Publish Article
     */
    public function update(Request $request, KnowledgeArticle $article)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:knowledge_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'required|boolean',
        ]);

        $article->update($validated);

        return redirect()->route('kms.admin.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * ADMIN: Delete Article
     */
    public function destroy(KnowledgeArticle $article)
    {
        $article->delete();
        return redirect()->route('kms.admin.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
