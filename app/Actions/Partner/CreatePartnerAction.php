<?php

declare(strict_types=1);

namespace App\Actions\Partner;

use App\Enums\UserRole;
use App\Mail\PartnerWelcomeMail;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Creates a new partner with an associated user account.
 */
final class CreatePartnerAction
{
    /**
     * Execute the partner creation.
     *
     * @param array<string, mixed> $data Validated partner data
     * @return Partner The created partner
     */
    public function execute(array $data): Partner
    {
        return DB::transaction(function () use ($data) {
            // Create partner record
            $partner = Partner::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'vat_number' => $data['vat_number'] ?? null,
                'sdi_pec' => $data['sdi_pec'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create associated user account for partner
            $contactName = $data['contact_name'] ?? $data['name'] . ' Staff';
            $temporaryPassword = Str::random(12);

            User::create([
                'name' => $contactName,
                'email' => $data['email'],
                'password' => $temporaryPassword,
                'role' => UserRole::PARTNER,
                'partner_id' => $partner->id,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Send welcome email with temporary password
            Mail::to($partner->email)->send(
                new PartnerWelcomeMail($partner, $temporaryPassword, $contactName)
            );

            return $partner;
        });
    }
}
