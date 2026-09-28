<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ChecklistItem;
use App\Models\Trip;
use App\Support\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClubController extends Controller
{
    /** Pagina publica de prezentare + inscriere. */
    public function join()
    {
        if (auth()->check()) {
            return redirect()->route('club.dashboard', app()->getLocale());
        }

        return view('club.join');
    }

    /** Zona de client. */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $current  = $user->currentTrip();
        $upcoming = $user->upcomingTrips();
        $past     = $user->pastTrips();

        // Lista cu necesarul apartine croazierei in curs sau celei urmatoare
        $focus     = $current ?? $upcoming->first();
        $checklist = $focus ? $this->checklistFor($focus, $user) : collect();

        $announcements = Announcement::visibleTo($user)->limit(5)->get();

        return view('club.dashboard', compact(
            'user', 'current', 'upcoming', 'past', 'checklist', 'announcements'
        ));
    }

    /**
     * Lista de bagaj a unei croaziere, cu bifele membrului curent.
     * Daca ieșirea nu are lista proprie, foloseste sablonul global.
     */
    private function checklistFor(Trip $trip, $user)
    {
        $items = $trip->checklistItems;

        if ($items->isEmpty()) {
            $items = ChecklistItem::template()->orderBy('position')->get();
        }

        $checkedIds = $user->checkedItems()
            ->whereNotNull('checklist_user.checked_at')
            ->pluck('checklist_items.id')
            ->all();

        return $items->map(fn (ChecklistItem $item) => [
            'id'      => $item->id,
            'label'   => $item->label,
            'hint'    => $item->hint,
            'checked' => in_array($item->id, $checkedIds, true),
        ]);
    }

    /** Bifeaza / debifeaza un element din lista de bagaj. */
    public function toggleChecklist(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item'    => ['required', 'integer', Rule::exists('checklist_items', 'id')],
            'checked' => ['required', 'boolean'],
        ]);

        $request->user()->checkedItems()->syncWithoutDetaching([
            $data['item'] => ['checked_at' => $data['checked'] ? now() : null],
        ]);

        return response()->json(['ok' => true]);
    }

    /** Profilul membrului. */
    public function profile(Request $request)
    {
        return view('club.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'  => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            ImageProcessor::delete($user->avatar_path);
            $data['avatar_path'] = ImageProcessor::storeAvatar($request->file('avatar'));
        }

        $data['wall_public'] = $request->boolean('wall_public');
        unset($data['avatar']);

        $user->update($data);

        return back()->with('status', __('club.profile.saved'));
    }
}
