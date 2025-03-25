<?php
declare(strict_types=1);

namespace App\DTO;

final class MediaProvider
{
    public string $locale;
    public string $link;
    public string $logo_path;
    public int $provider_id;
    public string $provider_name;
    public int $display_priority;
}
