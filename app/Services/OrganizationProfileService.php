<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Submission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * An NPO managing its own account: profile, logo, email, password, deletion.
 */
class OrganizationProfileService
{
    private const PROFILE_FIELDS = ['org_name', 'org_phone', 'state', 'org_hq_location', 'website', 'description'];

    private const LOGO_FOLDER = 'logos';

    /** Sample photos shared by many initiatives — never delete these. */
    private const SHARED_IMAGE_FOLDER = 'events/sample/';

    public function updateProfile(Organization $organization, array $data, ?UploadedFile $logo, bool $removeLogo): void
    {
        $organization->fill(Arr::only($data, self::PROFILE_FIELDS));

        if ($logo || $removeLogo) {
            $this->deletePublicFile($organization->logo_path);
            $organization->logo_path = $logo?->store(self::LOGO_FOLDER, 'public');
        }

        $organization->save();
    }

    public function updateEmail(Organization $organization, string $email): void
    {
        $organization->forceFill(['org_email' => $email])->save();
    }

    /**
     * New password (hashed by the model). A new remember-token signs out
     * "Keep me signed in" sessions on other devices.
     */
    public function updatePassword(Organization $organization, string $password): void
    {
        $organization->forceFill([
            'password'       => $password,
            'remember_token' => Str::random(60),
        ])->save();
    }

    /**
     * Deletes the organisation, its initiatives and applications (database cascade),
     * then removes its files: logo, initiative photos and volunteers' résumés.
     */
    public function deleteAccount(Organization $organization): void
    {
        $eventImages = $organization->events()
            ->whereNotNull('image_path')
            ->pluck('image_path')
            ->reject(fn (string $path) => str_starts_with($path, self::SHARED_IMAGE_FOLDER));

        $resumes = Submission::whereIn('event_id', $organization->events()->select('event_id'))
            ->whereNotNull('resume_path')
            ->pluck('resume_path');

        $logo = $organization->logo_path;

        DB::transaction(fn () => $organization->delete());

        // Files are removed only after the database delete succeeded.
        Storage::disk('public')->delete(array_filter([$logo, ...$eventImages->all()]));
        Storage::disk('local')->delete($resumes->all());
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}