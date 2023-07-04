<?php

namespace App\Helpdesk;

use App\Helpers\ConfigHelper;
use App\Models\TicketFields;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
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

    /**
     * @param $path
     * @param $mime
     * @param int $quality
     * @return mixed
     */
    public static function imageResponse($path, $mime = null, int $quality = 90)
    {
        return Image::make($path)->response($mime, $quality);
    }

    public static function downloadAllFiles(int $ticketId)
    {
        $path = self::FOLDER . DIRECTORY_SEPARATOR . $ticketId;
        $file_names = Storage::files($path);
        $zip = new \ZipArchive();
        if (!Storage::directoryExists('private/zip')) {
            Storage::createDirectory('private/zip');
        }
        $zipArchivePath = '/private/zip/Ticket-' . $ticketId . '-files.zip';
        $zipPath = Storage::path($zipArchivePath);
        if ($zip->open($zipPath, \ZipArchive::CREATE)) {
            foreach ($file_names as $file_name) {
                //$file_content = Storage::get($file_name);
                $zip->addFile(Storage::path($file_name), File::basename($file_name));
            }
            $zip->close();
            \Illuminate\Support\Facades\Response::download(Storage::path($zipArchivePath))
                ->deleteFileAfterSend(true)
                ->send();
        }
    }


    /**
     * @param TicketFields $ticketField
     * @return string|void
     */
    public static function getFile(TicketFields $ticketField)
    {
        $path = self::FOLDER . DIRECTORY_SEPARATOR . $ticketField->ticket_id . $ticketField->content;
        $mime = Storage::mimeType($path);
        if (!Storage::exists($path)) {
            $data = View::make('pages.404', [
                'name' => ConfigHelper::getValue('app.name'),
                'bg' => ConfigHelper::getValue('app.bg'),
                'data' => $ticketField->content
            ])->render();
            return \Illuminate\Support\Facades\Response::make($data)->send();
        }
        if (Str::startsWith($mime, 'image/')) {
            $response = self::imageResponse(Storage::path($path));
            if ($response instanceof Response) {
                $response->send();
            }

        } else {
            \Illuminate\Support\Facades\Response::download(Storage::path($path))->send();
        }
    }
}
