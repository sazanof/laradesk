<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SettingsHelper
{
    public ?UploadedFile $logo = null;
    public ?UploadedFile $bg = null;
    public ?string $name = null;
    public ?int $maxFileSize = null;
    public ?array $allowedMimes = null;

    public static ?SettingsHelper $instance = null;

    /**
     * @return SettingsHelper
     */
    public static function getInstance(): SettingsHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param Request $request
     * @return SettingsHelper|null
     */
    public static function createFromRequest(Request $request): ?SettingsHelper
    {
        $inst = self::getInstance();
        if ($request->files->has('logo')) {
            $logo = $request->files->get('logo');

            $inst->logo = $logo instanceof UploadedFile ? $logo : null;
        }

        if ($request->files->has('bg')) {
            $bg = $request->files->get('bg');
            $inst->bg = $bg instanceof UploadedFile ? $bg : null;
        }

        if ($request->has('name')) {
            $inst->name = $request->get('name');
        }

        if ($request->has('maxFileSize')) {
            $inst->maxFileSize = $request->get('maxFileSize');
        }

        if ($request->has('mimes')) {
            $inst->allowedMimes = $request->get('mimes');
        }

        return self::$instance;
    }


}
