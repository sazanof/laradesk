<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OfficesController extends Controller
{
    /**
     * @return Collection
     */
    public function getOffices(): Collection
    {
        return Office::orderBy('name')->get();
    }
}
