<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::with(['user', 'rentalOrder', 'saleOrder'])
            ->latest()
            ->filter($request->only('search', 'type', 'status'))
            ->paginate(10)
            ->withQueryString();

        return inertia('Vendor/Compalins/index', [
            'contacts' => $contacts,
            'filters' => $request->all('search', 'type', 'status'),
        ]);
    }

    public function markAsRead(Contact $contact)
    {
        $contact->update([
            'read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Contact marked as read.');
    }

    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);
        if (!empty($contact->email)) {
            // Send email reply
            Mail::to($contact->email)->send(new ContactReplyMail(
                $contact->subject,
                $request->message
            ));
        }


        $contact->update([
            'reply' => $request->message,
            'read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }
}