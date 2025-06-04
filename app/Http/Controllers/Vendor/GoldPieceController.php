<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GoldPieceController extends Controller
{
    public function index(Request $request)
    {
        $query = GoldPiece::whereNull('branch_id')
            ->latest();
        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Branch filter
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $goldPieces = $query->paginate(10)
            ->withQueryString()
            ->through(fn($goldPiece) => [
                'id' => $goldPiece->id,
                'name' => $goldPiece->name,
                'description' => $goldPiece->description,
                'weight' => $goldPiece->weight,
                'carat' => $goldPiece->carat,
                'type' => $goldPiece->type,
                'rental_price_per_day' => $goldPiece->rental_price_per_day,
                'sale_price' => $goldPiece->sale_price,
                'deposit_amount' => $goldPiece->deposit_amount,
                'status' => $goldPiece->status,
                'branch' => $goldPiece->branch,
                'user' => $goldPiece->user,
                 'images' => $goldPiece->getMedia('images')->map(fn($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ]),
                // 'images' => $goldPiece->images,
                'qr_code' => $goldPiece->qr_code,
            ]);

        $branches = Branch::where('vendor_id', auth()->user()->id)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Vendor/GoldPieces/Index', [
            'goldPieces' => $goldPieces,
            'filters' => $request->only(['search', 'branch_id', 'status']),
            'branches' => $branches
        ]);
    }

    public function show(GoldPiece $goldPiece)
    {
        $goldPiece->load(['branch', 'user']);

        return Inertia::render('Vendor/GoldPieces/Show', [
            'goldPiece' => [
                'id' => $goldPiece->id,
                'name' => $goldPiece->name,
                'description' => $goldPiece->description,
                'weight' => $goldPiece->weight,
                'carat' => $goldPiece->carat,
                'type' => $goldPiece->type,
                'rental_price_per_day' => $goldPiece->rental_price_per_day,
                'sale_price' => $goldPiece->sale_price,
                'deposit_amount' => $goldPiece->deposit_amount,
                'status' => $goldPiece->status,
                'branch' => $goldPiece->branch,
                'user' => $goldPiece->user,
                'qr_code' => $goldPiece->qr_code,
                'images' => $goldPiece->getMedia('images')->map(fn($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ]),
                'created_at' => $goldPiece->created_at->format('M d, Y h:i A'),
                'updated_at' => $goldPiece->updated_at->format('M d, Y h:i A'),
            ]
        ]);
    }

    public function approve(GoldPiece $goldPiece, Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $goldPiece->update([
            'status' => 'available',
            'branch_id' => $request->branch_id
        ]);

        return redirect()->back()->with('success', 'Gold piece approved successfully.');
    }

    public function reject(GoldPiece $goldPiece)
    {
        $goldPiece->update([
            'status' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'Gold piece rejected successfully.');
    }

    public function markSent(GoldPiece $goldPiece)
    {
        $goldPiece->update([
            'status' => 'sent_to_store'
        ]);

        return redirect()->back()->with('success', 'Gold piece marked as sent to store.');
    }

    public function markSold(GoldPiece $goldPiece, Request $request)
    {
        $request->validate([
            'sale_price' => 'required|numeric|min:0'
        ]);

        $goldPiece->update([
            'status' => 'sold',
            'sale_price' => $request->sale_price
        ]);

        return redirect()->back()->with('success', 'Gold piece marked as sold.');
    }

    public function updateStatus(GoldPiece $goldPiece, Request $request)
    {
        $request->validate([
            'status' => 'required|in:available,rented,sold'
        ]);

        $goldPiece->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Gold piece status updated successfully.');
    }
}