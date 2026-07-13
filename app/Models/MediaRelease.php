<?php

namespace App\Models;

use App\Models\Concerns\FormatsPacificTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaRelease extends Model
{
    use FormatsPacificTime;
    use HasFactory;

    public const AFFILIATIONS = [
        'Prospective Student',
        'Current Student',
        'Faculty/Staff',
        'Alumni',
        'Parent/Family Member',
        'Community Member',
        'Other',
    ];

    public const AFFILIATIONS_WITH_DETAILS = [
        'Current Student',
        'Faculty/Staff',
        'Alumni',
        'Parent/Family Member',
        'Other',
    ];

    public const STUDENT_PROGRAMS = [
        'Art & Design',
        'Business',
        'Communication',
        'Computer Science',
        'Design',
        'Economics',
        'Education',
        'Engineering',
        'Film',
        'Finance/Accounting',
        'Government/Political Science',
        'Health Science',
        'Liberal Arts',
        'Music',
        'Performing Arts',
        'Pharmacy',
        'Psychology',
        'Public health',
        'Research',
        'Science',
        'Social Science',
        'Technology',
        'Visual Arts',
    ];

    public const STATE_CODES = [
        'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
        'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD',
        'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ',
        'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC',
        'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY',
        'DC',
    ];

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'affiliation',
        'affiliation_details',
        'date_of_birth',
        'photo_path',
        'photo_encrypted',
        'photo_mime',
        'signature_path',
        'signature_encrypted',
        'signature_mime',
        'consent_agreed',
        'ip_address',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'date_of_birth' => 'date',
        'consent_agreed' => 'boolean',
    ];

    protected $hidden = [
        'photo_encrypted',
        'signature_encrypted',
    ];

    public function hasPhoto(): bool
    {
        return filled($this->photo_encrypted) || filled($this->photo_path);
    }

    public function hasSignature(): bool
    {
        return filled($this->signature_encrypted) || filled($this->signature_path);
    }

    public function photoUrl(): ?string
    {
        if (! $this->hasPhoto()) {
            return null;
        }

        return route('media-releases.photo', $this->id);
    }

    public function signatureUrl(): ?string
    {
        if (! $this->hasSignature()) {
            return null;
        }

        return route('media-releases.signature', $this->id);
    }

    public function getSubmittedAtPacificAttribute(): string
    {
        return $this->formatPacificTime($this->submitted_at);
    }

    public function getSubmittedAtDisplayAttribute(): string
    {
        return $this->formatPacificDate($this->submitted_at);
    }

    public function getFullNameAttribute(): string
    {
        $first = $this->attributes['first_name'] ?? '';
        $last = $this->attributes['last_name'] ?? '';

        if ($first !== '' || $last !== '') {
            return trim($first.' '.$last);
        }

        return $this->attributes['full_name'] ?? '';
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
