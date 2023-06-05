<?php

namespace App\Http\Controllers;

use App\Helpdesk\FormFieldsCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TicketsController extends Controller
{
    public function createTicket(Request $request)
    {
        $collection = new FormFieldsCollection($request);
        foreach ($collection->items as $item) {
            if ($item->value instanceof UploadedFile) {
                // work with file
            } else if (is_string($item->value)) {
                // work with string
            }
            //dump($item->options);
        }
        try {
            //TODO Validate other data.....
            $v = $collection->validate();
            if (count($v) === 0) {
                // FORM FIELDS DATA VALID!!!
            }
        } catch (ValidationException $e) {
            return Response::json($e->validator->errors(), 400);
        }
    }
}
