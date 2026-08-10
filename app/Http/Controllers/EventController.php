<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function create()
    {
        return view('dashboard.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEventDates($request, requireLocationAndStatus: false, requireFutureDates: true);
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_format' => $validated['event_format'],
            'event_date' => $validated['event_date'],
            'event_end_date' => $validated['event_end_date'],
            'location' => '',
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        $event->ensureFormToken();

        return redirect()->route('public.permitted-events')->with('success', 'Event created successfully!');
    }

    public function show(Request $request, $id)
    {
        $search = trim((string) $request->input('search', ''));

        $event = Event::withCount('mediaReleases')->findOrFail($id);
        $user = Auth::user();

        if ($user->isUser() && $event->created_by != $user->id) {
            abort(403);
        }

        $releasesQuery = $event->mediaReleases()->orderByDesc('submitted_at');

        if ($search !== '') {
            $releasesQuery->where(function ($builder) use ($search) {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $mediaReleases = $releasesQuery->get();

        return view('dashboard.show', compact('event', 'mediaReleases', 'search'));
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();
        if ($user->isUser() && $event->created_by != $user->id) {
            abort(403);
        }

        return view('dashboard.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();
        if ($user->isUser() && $event->created_by != $user->id) {
            abort(403);
        }
        $validated = $this->validateEventDates($request);
        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'event_format' => $validated['event_format'],
            'event_date' => $validated['event_date'],
            'event_end_date' => $validated['event_end_date'],
            'location' => $request->location,
            'status' => $request->status,
        ]);

        $event->ensureFormToken();

        return redirect()->route('public.permitted-events')->with('success', 'Event updated successfully!');
    }

    private function validateEventDates(Request $request, bool $requireLocationAndStatus = true, bool $requireFutureDates = false): array
    {
        $minDate = Carbon::now('America/Los_Angeles')->toDateString();

        $eventDateRules = ['required', 'date'];
        $eventEndDateRules = ['nullable', 'required_if:date_type,range', 'date', 'after_or_equal:event_date'];

        if ($requireFutureDates) {
            $eventDateRules[] = "after_or_equal:{$minDate}";
            $eventEndDateRules[] = "after_or_equal:{$minDate}";
        }

        $rules = [
            'title' => 'required',
            'description' => 'required',
            'event_format' => 'required|in:in_person,print',
            'date_type' => 'required|in:single,range',
            'event_date' => $eventDateRules,
            'event_end_date' => $eventEndDateRules,
        ];

        if ($requireLocationAndStatus) {
            $rules['location'] = 'required';
            $rules['status'] = 'required';
        }

        $request->validate($rules);

        return [
            'event_format' => $request->event_format,
            'event_date' => $request->event_date,
            'event_end_date' => $request->date_type === 'range' ? $request->event_end_date : null,
        ];
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();
        if ($user->isUser() && $event->created_by != $user->id) {
            abort(403);
        }

        if ($event->status !== 'active' || $event->display_status !== 'active') {
            return redirect()
                ->route('public.permitted-events')
                ->withErrors(['event' => 'Only active events can be deleted.']);
        }

        $event->delete();

        return redirect()->route('public.permitted-events')->with('success', 'Event deleted successfully!');
    }
}
