<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        // Ambil 4 artikel terbaru untuk ditampilkan di landing page
        $latestArticles = KnowledgeArticle::where('is_published', true)
            ->with('category')
            ->latest()
            ->take(4)
            ->get();

        $categories = KnowledgeCategory::withCount('articles')->get();

        return view('landing', compact('latestArticles', 'categories'));
    }
}
