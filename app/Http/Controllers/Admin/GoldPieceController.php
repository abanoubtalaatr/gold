<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoldPicesUpdateRequest;
use App\Http\Requests\Api\V1\UpdateGoldPieceRequest;
use App\Models\GoldPiece;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GoldPieceController extends Controller
{
    public function index(Request $request)
    {
        $query = GoldPiece::query()
            ->with(['branch', 'user'])
            ->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
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
                'user' => $goldPiece->user,
                'images' => $goldPiece->getMedia('images')->map(fn($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ]),
                'qr_code' => $goldPiece->qr_code,
            ]);

        return Inertia::render('Admin/GoldPieces/Index', [
            'goldPieces' => $goldPieces,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(GoldPiece $goldPiece)
    {
        $goldPiece->load(['branch', 'user']);

        return Inertia::render('Admin/GoldPieces/Show', [
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
                'user' => [
                    'id' => $goldPiece->user->id,
                    'name' => $goldPiece->user->name,
                    // 'avatar' => $goldPiece->user->getFirstMediaUrl('avatar'),
                ],
                'images' => $goldPiece->getMedia('images')->map(fn($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ]),
                'qr_code' => $goldPiece->qr_code,
                'created_at' => $goldPiece->created_at->format('M d, Y h:i A'),
                'updated_at' => $goldPiece->updated_at->format('M d, Y h:i A'),
            ]
        ]);
    }

    public function edit(GoldPiece $goldPiece)
    {
        $goldPiece->load(['branch', 'user']);

        return Inertia::render('Admin/GoldPieces/Edit', [
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
                'user_id' => $goldPiece->user_id,
                'images' => $goldPiece->getMedia('images')->map(fn($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ]),
                'qr_code' => $goldPiece->qr_code,
            ],
        ]);
    }

    public function update(GoldPicesUpdateRequest $request, GoldPiece $goldPiece)
    {
        $goldPiece->update($request->validated());

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $goldPiece->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('images');
            }
        }

        // Handle image deletions
        if ($request->has('deleted_images')) {
            $goldPiece->media()
                ->whereIn('id', $request->deleted_images)
                ->delete();
        }

        return redirect()->route('admin.gold-pieces.index')
            ->with('success', 'Gold piece updated successfully.');
    }

    public function destroy(GoldPiece $goldPiece)
    {
        $goldPiece->delete();

        return redirect()->route('admin.gold-pieces.index')
            ->with('success', 'Gold piece deleted successfully.');
    }
}
