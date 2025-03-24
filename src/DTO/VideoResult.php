<?php
declare(strict_types=1);

namespace App\DTO;

final class VideoResult
{
    public string $iso_639_1;
    public string $iso_3166_1;
    public string $name;
    public string $key;
    public string $published_at;
    public string $site;
    public int $size;
    public string $type;
    public bool $official;
    public string $id;
}
