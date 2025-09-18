<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;

class SitterController extends Controller
{
    public function show($sitterId)
    {
        // Find sitter or fail
        $sitter = User::with(['services.category', 'locations'])->findOrFail($sitterId);

        // Get active services only
        $services = $sitter->services()
            ->active()
            ->with('category')
            ->get();

        return view('sitters.show', compact('sitter', 'services'));
    }
}
