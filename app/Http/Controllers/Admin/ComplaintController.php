<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintReplyMail;
use App\Mail\ContactMail;
use App\Mail\ContactReplyMail;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Contact::with([
            'user',
            'rentalOrder' => fn($query) => $query->with(['branch.vendor']),
            'saleOrder' => fn($query) => $query->with(['branch.vendor'])
        ])
            ->where('is_to_admin', 1)
            ->orWhere(function ($query) {
                $query->whereNull('sale_order_id')
                    ->whereNull('rental_order_id');
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Admin/Complaints/Index', [
            'complaints' => $complaints,
            'filters' => $request->all('search', 'status'),
        ]);
    }

    public function reply(Request $request, Contact $complaint)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);

        // Send email reply if email exists
        $email = $complaint->user?->email ?? $complaint->email;
        if ($email) {
            Mail::to($email)->send(new ContactReplyMail(
                $complaint->subject,
                $request->message
            ));
        }

        $complaint->update([
            'reply' => $request->message,
            'read' => true,
            'read_at' => now(),
            'status' => 'replied',
        ]);

        return back()->with('success', __('Reply sent successfully.'));
    }

    public function updateStatus(Request $request, Contact $complaint)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved',
        ]);

        $complaint->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }
}
