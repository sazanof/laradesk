<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RoomsController extends Controller
{
    public function onUploadCsv(Request $request)
    {
        $file = $request->file('file');
        $ar = [];
        if ($file instanceof UploadedFile) {
            $contents = $file->getContent();
            $contents = explode("\n", $contents);
            if (!empty($contents)
                && count($contents) > 1
                && Str::contains($contents[0], [
                    'group',
                    'level',
                    'title',
                    'description'
                ])) {
                $indexes = explode(',', $contents[0]);
                $nameIndex = array_search('title', $indexes);
                $levelIndex = array_search('level', $indexes);
                $groupIndex = array_search('group', $indexes);
                $descriptionIndex = array_search('description', $indexes);
                $i = 0;
                foreach ($contents as $line) {
                    if ($i === 0) {
                        $i++;
                        continue;
                    }
                    $data = explode(',', $line);
                    if (isset($data[$groupIndex]) && isset($data[$nameIndex]) && isset($data[$levelIndex]))
                        $ar[] = [
                            'group' => trim($data[$groupIndex]),
                            'title' => trim($data[$nameIndex]),
                            'level' => trim($data[$levelIndex]),
                            'description' => isset($data[$descriptionIndex]) ? trim($data[$descriptionIndex]) : ""
                        ];
                    $i++;
                }
            }
        }
        return $ar;
    }

    public function uploadCsvRoomData(Request $request)
    {
        $office_id = $request->get('officeId');
        $clear_previous = $request->get('clearPrevious');
        $data = $request->get('data');
        if (!empty($office_id) && is_array($data)) {
            if ($clear_previous) {
                Room::where('office_id', $office_id)->delete();
                foreach ($data as $r) {
                    Room::updateOrInsert([
                        'office_id' => $office_id,
                        'name' => $r['title'],
                        'level' => $r['level']
                    ], [
                        'description' => $r['description']
                    ]);
                }
            }
        }
    }
}
