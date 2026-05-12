<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketReply;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * ADMIN: List all tickets
     */
    public function publicIndex(Request $request)
    {
        // Expecting an array of objects: [['id' => '...', 'token' => '...'], ...]
        $items = $request->get('items', []);
        $tickets = [];
        
        if (!empty($items) && is_array($items)) {
            $query = Ticket::query()->with('category');
            
            $query->where(function($q) use ($items) {
                foreach ($items as $item) {
                    if (isset($item['id']) && isset($item['token'])) {
                        $q->orWhere(function($sub) use ($item) {
                            $sub->where('tracking_id', $item['id'])
                                ->where('view_token', $item['token']);
                        });
                    }
                }
            });

            $tickets = $query->latest()->get();
        }
        
        if ($request->ajax()) {
            return response()->json($tickets);
        }

        return view('ticket.public.index', compact('tickets'));
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with('category', 'user', 'assignee');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'ilike', "%{$search}%")
                  ->orWhere('tracking_id', 'ilike', "%{$search}%")
                  ->orWhere('reporter_name', 'ilike', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();
        $categories = TicketCategory::all();

        return view('ticket.admin.index', compact('tickets', 'categories'));
    }

    /**
     * GUEST: Show creation form
     */
    public function create()
    {
        $categories = TicketCategory::all();
        return view('ticket.public.create', compact('categories'));
    }

    /**
     * GUEST: Store new ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reporter_name' => 'required|string|max:255',
            'reporter_phone' => 'required|string|max:20',
            'reporter_email' => 'nullable|email|max:255',
            'reporter_organization' => 'required|string|max:255',
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'notification_method' => 'required|in:whatsapp,email',
            'priority' => 'required|in:rendah,sedang,tinggi',
            'attachment' => 'nullable|file|max:10240',
        ]);

        // Use Model method for tracking ID
        $ticketModel = new Ticket();
        $validated['tracking_id'] = $ticketModel->generateTrackingId($validated['category_id']);
        $validated['view_token'] = Str::random(32);
        
        $validated['status'] = 'open';
        $validated['user_id'] = Auth::id(); // If logged in, associate

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('tickets/attachments', 'public');
        }

        $ticket = Ticket::create($validated);

        return redirect()->route('ticket.public.success', [
            'tracking_id' => $ticket->tracking_id,
            'token' => $ticket->view_token
        ])->with([
            'tracking_id' => $ticket->tracking_id,
            'token' => $ticket->view_token
        ]);
    }

    public function success(Request $request)
    {
        $tracking_id = $request->get('tracking_id');
        $token = $request->get('token');
        return view('ticket.public.success', compact('tracking_id', 'token'));
    }

    public function trackForm()
    {
        return view('ticket.public.track');
    }

    public function track(Request $request)
    {
        $request->validate(['tracking_id' => 'required|string']);
        $ticket = Ticket::where('tracking_id', $request->tracking_id)
            ->with(['replies.user', 'activities.user', 'category'])
            ->first();

        if ($request->ajax()) {
            if (!$ticket) return response()->json(['success' => false]);
            return response()->json([
                'success' => true,
                'id' => $ticket->tracking_id,
                'token' => $ticket->view_token
            ]);
        }

        if (!$ticket) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        }

        return view('ticket.public.show', compact('ticket'));
    }

    /**
     * ADMIN: Show ticket detail
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['replies.user', 'activities.user', 'category']);
        
        // PJs belonging to this category
        $pj_ids = $ticket->category->pj_ids ?? [];
        $admins = User::whereIn('id', $pj_ids)->get();
        
        return view('ticket.admin.show', compact('ticket', 'admins'));
    }

    /**
     * ADMIN: Store reply
     */
    public function storeReply(Request $request, Ticket $ticket)
    {
        // Hanya Admin, Katim, atau PJ yang bisa membalas
        Gate::authorize('can-manage-ticket', $ticket);

        $validated = $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $data = [
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_admin' => true,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('tickets/replies', 'public');
        }

        TicketReply::create($data);

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }

    /**
     * ADMIN: Update ticket status/assignment
     */
    public function update(Request $request, Ticket $ticket)
    {
        // Hanya Admin, Katim, atau PJ yang bisa update status/assignee
        Gate::authorize('can-manage-ticket', $ticket);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:ticket_categories,id', // Admin can change category
            'status' => 'required|in:open,assigned,onprogress,check wa,closed',
            'assigned_to_ids' => 'nullable|array',
            'assigned_to_ids.*' => 'exists:users,id',
            'unit_kerja' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
            'solution' => 'nullable|string',
            'wa_status' => 'nullable|in:Pending,Sent,None',
        ]);

        $oldStatus = $ticket->status;
        $oldAssignees = $ticket->assigned_to_ids ?? [];

        if ($validated['status'] === 'closed' && !$ticket->finished_at) {
            $validated['finished_at'] = now();
        }

        $ticket->update($validated);

        // Activity Logging
        if ($oldStatus !== $validated['status']) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'type' => 'status_change',
                'old_value' => $oldStatus,
                'new_value' => $validated['status'],
                'message' => Auth::user()->nama_lengkap . ' mengubah status menjadi ' . strtoupper($validated['status']),
            ]);

        }

        if (json_encode($oldAssignees) != json_encode($request->assigned_to_ids)) {
            $newNames = User::whereIn('id', $request->assigned_to_ids ?? [])->pluck('nama_lengkap')->toArray();
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'type' => 'assigned_to',
                'new_value' => json_encode($request->assigned_to_ids),
                'message' => Auth::user()->nama_lengkap . ' menugaskan tiket ke: ' . (empty($newNames) ? 'Tanpa Petugas' : implode(', ', $newNames)),
            ]);
        }

        return redirect()->back()->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Trigger WA Confirmation
     */
    public function triggerWa(Ticket $ticket)
    {
        $ticket->update([
            'status' => 'check wa',
            'wa_status' => 'Pending'
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'type' => 'wa_confirmation',
            'message' => Auth::user()->nama_lengkap . ' memicu konfirmasi WhatsApp kepada PJ.',
        ]);

        return redirect()->back()->with('success', 'Konfirmasi WA telah dikirim ke PJ.');
    }

    /**
     * ADMIN: Push to KMS
     */
    public function pushToKms(Ticket $ticket)
    {
        // Seluruh pegawai bisa push ke KMS asalkan status sudah Closed
        if ($ticket->status !== 'closed') {
            return redirect()->back()->with('error', 'Tiket harus dalam status CLOSED untuk dipush ke KMS.');
        }

        if ($ticket->pushed_to_kms) {
            return;
        }

        // Map TicketCategory to KnowledgeCategory
        $knowledgeCategory = \App\Models\KnowledgeCategory::firstOrCreate(
            ['slug' => $ticket->category->slug],
            ['name' => $ticket->category->name]
        );

        // Internal push to Knowledge module
        \App\Models\KnowledgeArticle::create([
            'category_id' => $knowledgeCategory->id,
            'author_id' => $ticket->assigned_to ?: Auth::id(),
            'title' => $ticket->subject,
            'slug' => Str::slug($ticket->subject) . '-' . time(),
            'content' => $ticket->solution ?: $ticket->description,
            'is_published' => false,
        ]);

        $ticket->update(['pushed_to_kms' => true]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'type' => 'pushed_to_kms',
            'message' => Auth::user()->nama_lengkap . ' mengirim tiket ini ke sistem KMS untuk dipublikasi.',
        ]);
    }
}
