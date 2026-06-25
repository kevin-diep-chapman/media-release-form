<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function permittedEvents(Request $request)
    {
        $user = Auth::user();
        $search = trim((string) $request->input('search', ''));
        $dateFrom = trim((string) $request->input('date_from', ''));
        $dateTo = trim((string) $request->input('date_to', ''));

        if ($user->isAdmin()) {
            $query = Event::query()->withCount('mediaReleases');
        } elseif ($user->isUser()) {
            $query = Event::where('created_by', $user->id)->withCount('mediaReleases');
        } else {
            $events = collect();

            return view('public.permitted-events', compact('events', 'search', 'dateFrom', 'dateTo'));
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($dateFrom !== '' || $dateTo !== '') {
            $filterStart = $dateFrom !== ''
                ? Carbon::parse($dateFrom, 'America/Los_Angeles')->toDateString()
                : null;
            $filterEnd = $dateTo !== ''
                ? Carbon::parse($dateTo, 'America/Los_Angeles')->toDateString()
                : null;

            if ($filterStart && $filterEnd && $filterStart > $filterEnd) {
                [$filterStart, $filterEnd] = [$filterEnd, $filterStart];
            }

            $query->where(function ($builder) use ($filterStart, $filterEnd) {
                $builder->where(function ($singleDayQuery) use ($filterStart, $filterEnd) {
                    $singleDayQuery->whereNull('event_end_date');

                    if ($filterStart) {
                        $singleDayQuery->where('event_date', '>=', $filterStart);
                    }

                    if ($filterEnd) {
                        $singleDayQuery->where('event_date', '<=', $filterEnd);
                    }
                })->orWhere(function ($rangeQuery) use ($filterStart, $filterEnd) {
                    $rangeQuery->whereNotNull('event_end_date');

                    if ($filterEnd) {
                        $rangeQuery->where('event_date', '<=', $filterEnd);
                    }

                    if ($filterStart) {
                        $rangeQuery->where('event_end_date', '>=', $filterStart);
                    }
                });
            });
        }

        $events = $query->get()->sortBy(function (Event $event) {
            $statusOrder = match ($event->display_status) {
                'active' => 0,
                'upcoming' => 1,
                'inactive' => 2,
                default => 3,
            };

            return sprintf('%d-%s', $statusOrder, $event->event_date->toDateString());
        })->values();

        return view('public.permitted-events', compact('events', 'search', 'dateFrom', 'dateTo'));
    }
}
