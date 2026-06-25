<?php

namespace App\Http\Controllers;

use App\Mail\MediaReleaseConfirmationMail;
use App\Models\MediaRelease;
use App\Services\EncryptedImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MediaReleaseController extends Controller
{
    public function __construct(
        private readonly EncryptedImageService $images,
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sortDirection = strtolower((string) $request->input('sort', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = MediaRelease::with('event')->orderBy('submitted_at', $sortDirection);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('submitted_at', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($eventQuery) use ($search) {
                        $eventQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $releases = $query->get();

        return view('dashboard.media-releases.index', compact('releases', 'search', 'sortDirection'));
    }

    public function show($id)
    {
        $release = MediaRelease::with('event')->findOrFail($id);
        $user = auth()->user();

        if ($user->isUser() && $release->event->created_by != $user->id) {
            abort(403);
        }

        $releaseQuery = MediaRelease::query()->orderByDesc('submitted_at')->orderByDesc('id');

        if ($user->isUser()) {
            $releaseQuery->whereHas('event', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            });
        }

        $releaseIds = $releaseQuery->pluck('id');
        $currentIndex = $releaseIds->search((int) $id);
        $nextReleaseId = ($currentIndex !== false && $currentIndex < $releaseIds->count() - 1)
            ? $releaseIds[$currentIndex + 1]
            : null;

        return view('dashboard.media-release-show', compact('release', 'nextReleaseId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => ['nullable', Rule::in(MediaRelease::STATE_CODES)],
            'zip_code' => 'nullable|digits:5',
            'affiliation' => ['required', Rule::in(MediaRelease::AFFILIATIONS)],
            'affiliation_details' => [
                Rule::requiredIf(in_array($request->affiliation, MediaRelease::AFFILIATIONS_WITH_DETAILS, true)),
                'nullable',
                'string',
                'max:2000',
            ],
            'consent_agreed' => 'nullable|boolean',
            'photo_path' => 'required',
            'signature_path' => 'required',
        ]);

        try {
            $photo = $this->images->fromDataUrl($request->input('photo_path'));
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'photo_path' => 'A valid photo is required.',
            ]);
        }

        try {
            $signature = $this->images->fromDataUrl($request->input('signature_path'));
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'signature_path' => 'A valid signature is required.',
            ]);
        }

        $release = MediaRelease::create([
            'event_id' => $request->event_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => trim($request->first_name.' '.$request->last_name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'affiliation' => $request->affiliation,
            'affiliation_details' => in_array($request->affiliation, MediaRelease::AFFILIATIONS_WITH_DETAILS, true)
                ? $request->affiliation_details
                : null,
            'photo_path' => null,
            'photo_encrypted' => $this->images->encryptBinary($photo['binary']),
            'photo_mime' => $photo['mime'],
            'signature_path' => null,
            'signature_encrypted' => $this->images->encryptBinary($signature['binary']),
            'signature_mime' => $signature['mime'],
            'consent_agreed' => $request->boolean('consent_agreed'),
            'ip_address' => $request->ip(),
            'submitted_at' => now(),
        ]);

        $this->sendConfirmationEmail($release);

        return response()->json(['message' => 'Form submitted.']);
    }

    private function sendConfirmationEmail(MediaRelease $release): void
    {
        try {
            $release->loadMissing('event');

            Mail::to($release->email)->send(new MediaReleaseConfirmationMail($release));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
