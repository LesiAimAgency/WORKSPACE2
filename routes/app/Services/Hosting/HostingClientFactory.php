<?php

namespace App\Services\Hosting;

use App\Models\HostingProfile;
use App\Services\Hosting\Contracts\HostingClientInterface;

class HostingClientFactory
{
    public static function make(HostingProfile $profile): HostingClientInterface
    {
        $client = match ($profile->panel_type) {
            'cpanel' => new CpanelHostingClient,
            'directadmin' => new DirectAdminHostingClient,
            default => throw new \InvalidArgumentException("Unsupported panel type: {$profile->panel_type}"),
        };

        return $client->setProfile($profile);
    }
}
