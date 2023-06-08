<?php

namespace App\Helpdesk;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TicketsStorage
{
    const FOLDER = 'private/tickets';

    public static function createFile(int $ticketId, UploadedFile $file): ?string
    {
        $path = self::FOLDER . DIRECTORY_SEPARATOR . $ticketId . self::prepareFileName($file);
        if (Storage::put($path, $file->getContent())) {
            return self::prepareFileName($file);
        }
        return null;
    }

    public static function prepareFileName(UploadedFile $file): string
    {
        return DIRECTORY_SEPARATOR . $file->getClientOriginalName();
    }
}
