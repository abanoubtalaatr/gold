<?php

namespace App\Http\Controllers;

use App\Models\GoldPiece;
use Illuminate\Http\Request;

class GoldPieceDetailsController extends Controller
{
    /**
     * Display the specified gold piece details.
     *
     * @param GoldPiece $goldPiece
     * @return \Illuminate\View\View
     */
    public function show(GoldPiece $goldPiece)
    {
        // Load necessary relationships
        $goldPiece->load(['user', 'branch', 'media']);

        return view('gold-piece.show', compact('goldPiece'));
    }
} 