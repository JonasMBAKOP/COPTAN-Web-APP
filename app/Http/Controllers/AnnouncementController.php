<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $query = Announcement::with('author')
            ->published()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at');

        $announcements = $query->paginate(15)->withQueryString()
            ->filter(fn($a) => $a->isVisibleFor($user));

        $stats = [
            'total'  => Announcement::published()->count(),
            'pinned' => Announcement::published()->pinned()->count(),
        ];

        return view('communication.announcements.index', compact(
            'announcements', 'stats'
        ));
    }

    public function create()
    {
        return view('communication.announcements.create');
    }

    public function store(StoreAnnouncementRequest $request)
    {
        Announcement::create([
            'author_id'    => Auth::id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'target_roles' => $request->target_roles,
            'is_pinned'    => $request->boolean('is_pinned'),
            'published_at' => now(),
        ]);

        return redirect()->route('communication.announcements.index')
            ->with('success', 'Annonce publiée avec succès.');
    }

    public function destroy(Announcement $announcement)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // $this->authorize('delete', $announcement); // ou check manuel
        if (Auth::id() !== $announcement->author_id
            && $user && !$user->hasRole('super-admin')) {
            abort(403);
        }
        $announcement->delete();
        return back()->with('success', 'Annonce supprimée.');
    }
}