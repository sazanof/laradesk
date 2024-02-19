<?php

namespace App\Helpers;

use App\Models\TicketThread;
use App\Models\TicketThreadCommentFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadHelper
{
    const DIR_THREAD = 'tread';

    /**
     * @return Filesystem|FilesystemAdapter
     */
    public static function threadStorage(): Filesystem|FilesystemAdapter
    {
        return Storage::disk('threads');
    }

    /**
     * @throws FilesystemException
     */
    public static function uploadTicketThreadFile(TicketThread $thread, UploadedFile $file)
    {
        $path = $thread->id . DIRECTORY_SEPARATOR . $file->getClientOriginalName();
        if (self::threadStorage()->put($path, $file->getContent())) {
            TicketThreadCommentFile::create([
                'user_id' => $thread->user_id,
                'ticket_id' => $thread->ticket_id,
                'thread_id' => $thread->id,
                'name' => $file->getClientOriginalName(),
                'file' => $path
            ]);
        }
    }

    /**
     * @param TicketThreadCommentFile $file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function downloadThreadFile(TicketThreadCommentFile $file)
    {
        return self::threadStorage()->download($file->file);
    }

    /**
     * @param int $commentId
     * @return BinaryFileResponse|void
     */
    public static function downloadAllThreadFiles(int $commentId)
    {
        $storage = self::threadStorage();
        $file_names = $storage->files($commentId);
        if (empty($file_names)) return null;
        $zip = new \ZipArchive();
        if (!Storage::directoryExists('/private/zip')) {
            Storage::createDirectory('/private/zip');
        }
        $zipArchivePath = '/private/zip/Thread-files-' . uniqid('', true) . '.zip';
        $zipPath = Storage::path($zipArchivePath);
        if ($zip->open($zipPath, \ZipArchive::CREATE)) {
            foreach ($file_names as $file_name) {
                $zip->addFile($storage->path($file_name), File::basename($file_name));
            }
            $zip->close();
            return Response::download(Storage::path($zipArchivePath))
                ->deleteFileAfterSend()
                ->send();
        }
    }
}
