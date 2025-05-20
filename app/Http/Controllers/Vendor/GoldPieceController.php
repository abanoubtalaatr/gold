<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreGoldPieceRequest;
use App\Http\Requests\Api\V1\UpdateGoldPieceRequest;
use App\Models\Branch;
use App\Models\GoldPiece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GoldPieceController extends Controller
{
    public function index(Request $request)
    {
        $query = GoldPiece::query()
            ->with(['branch'])
            ->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
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
                'images' => $goldPiece->images,
            ]);

        return Inertia::render('Vendor/GoldPieces/Index', [
            'goldPieces' => $goldPieces,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $branches = Branch::where('vendor_id', auth()->user()->id)->select('id', 'name')->get();

        return Inertia::render('Vendor/GoldPieces/Create', [
            'branches' => $branches,
        ]);
    }

    public function store(StoreGoldPieceRequest $request)
    {
        $goldPiece = GoldPiece::create($request->validated());

        // if ($request->hasFile('images')) {
        //     foreach ($request->file('images') as $image) {
        //         $path = $image->store('gold-pieces', 'public');
        //         $goldPiece->images()->create(['path' => $path]);
        //     }
        // }

        return redirect()->route('vendor.gold-pieces.index')
            ->with('success', 'Gold piece created successfully.');
    }

    public function edit(GoldPiece $goldPiece)
    {
        $branches = Branch::where('vendor_id', auth()->user()->id)->select('id', 'name')->get();

        return Inertia::render('Vendor/GoldPieces/Edit', [
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
                'branch_id' => $goldPiece->branch_id,
                'images' => $goldPiece->images,
            ],
            'branches' => $branches,
        ]);
    }

    public function update(UpdateGoldPieceRequest $request, GoldPiece $goldPiece)
    {
        // return $request;
        $goldPiece->update($request->validated());

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('gold-pieces', 'public');
                $goldPiece->images()->create(['path' => $path]);
            }
        }

        // Handle image deletions
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = $goldPiece->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        return redirect()->route('vendor.gold-pieces.index')
            ->with('success', 'Gold piece updated successfully.');
    }

    public function destroy(GoldPiece $goldPiece)
    {
        // Delete associated images from storage
        foreach ($goldPiece->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $goldPiece->delete();

        return redirect()->route('vendor.gold-pieces.index')
            ->with('success', 'Gold piece deleted successfully.');
    }

    public function toggleStatus(GoldPiece $goldPiece)
    {
        $goldPiece->update([
            'status' => $goldPiece->status === 'available' ? 'unavailable' : 'available'
        ]);

        return back()->with('success', 'Gold piece status updated successfully.');
    }
}