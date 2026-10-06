<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ChecklistTick;
use App\Models\ContentItem;
use App\Models\ContentSection;
use App\Models\Trip;
use App\Models\User;
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
     * Lista de bagaj a unei croaziere, pe secțiuni, cu bifele membrului.
     * Bifele sunt legate și de croazieră, deci aceeași listă folosită la
     * două ieșiri are bife separate.
     */
    private function checklistFor(Trip $trip, User $user)
    {
        $lista = $trip->packingList;

        if (! $lista) {
            return collect();
        }

        $bifate = ChecklistTick::query()
            ->where('user_id', $user->id)
            ->where('trip_id', $trip->id)
            ->whereNotNull('checked_at')
            ->pluck('content_item_id')
            ->all();

        $sectiuni = $lista->sections()->with(['items', 'children.items'])->get();

        return $sectiuni->map(fn (ContentSection $sectiune) => [
            'titlu'     => $sectiune->title,
            'nota'      => $sectiune->note,
            'elemente'  => $this->elementeCuBife($sectiune->items, $bifate),
            'subsectiuni' => $sectiune->children->map(fn (ContentSection $sub) => [
                'titlu'    => $sub->title,
                'elemente' => $this->elementeCuBife($sub->items, $bifate),
            ]),
        ]);
    }

    private function elementeCuBife($elemente, array $bifate)
    {
        return $elemente->map(fn (ContentItem $element) => [
            'id'        => $element->id,
            'titlu'     => $element->title,
            'detaliu'   => $element->body,
            'imagine'   => $element->imageUrl(),
            'link'      => $element->link_url,
            'linkText'  => $element->link_label,
            'bifat'     => in_array($element->id, $bifate, true),
        ]);
    }

    /** Bifeaza / debifeaza un element, pentru croaziera data. */
    public function toggleChecklist(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item'    => ['required', 'integer', Rule::exists('content_items', 'id')],
            'trip'    => ['required', 'integer', Rule::exists('trips', 'id')],
            'checked' => ['required', 'boolean'],
        ]);

        $user = $request->user();

        // Membrul poate bifa doar la croazierele la care participă
        abort_unless($user->trips()->whereKey($data['trip'])->exists(), 403);

        ChecklistTick::updateOrCreate(
            [
                'user_id'         => $user->id,
                'trip_id'         => $data['trip'],
                'content_item_id' => $data['item'],
            ],
            ['checked_at' => $data['checked'] ? now() : null]
        );

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
