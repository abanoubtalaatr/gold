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
        $vendorId = auth()->user()->vendor_id??auth()->user()->id;
        
        $contacts = Contact::with(['user', 'rentalOrder', 'saleOrder'])
            ->latest()
            ->filter($request->only('search', 'type', 'status'))
            ->where(function ($query) use ($vendorId) {
                $query->whereHas('saleOrder.branch', function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                })->orWhereHas('rentalOrder.branch', function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                });
            })
            ->paginate(10)
            ->withQueryString();


        $vendorMessages = Contact::where('is_to_admin', '1')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10, ['*'], 'vendor_messages_page');

        return inertia('Vendor/Compalins/index', [
            'contacts' => $contacts,
            'vendorMessages' => $vendorMessages,
            'filters' => $request->all('search', 'type', 'status'),
        ]);
    }


    public function adminComplaints(Request $request)
    {
        $vendorId = auth()->user()->vendor_id??auth()->user()->id;
        
        $contacts = Contact::with(['user', 'rentalOrder', 'saleOrder'])
            ->latest()
            ->filter($request->only('search', 'type', 'status'))
            ->where(function ($query) use ($vendorId) {
                $query->whereHas('saleOrder.branch', function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                })->orWhereHas('rentalOrder.branch', function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                });
            })
            ->paginate(10)
            ->withQueryString();


        $vendorMessages = Contact::where('is_to_admin', '1')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10, ['*'], 'vendor_messages_page');

        return inertia('Vendor/Compalins/Admin', [
            'contacts' => $contacts,
            'vendorMessages' => $vendorMessages,
            'filters' => $request->all('search', 'type', 'status'),
        ]);
    }


    public function create()
    {
        return inertia('Vendor/Compalins/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        Contact::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'user_id' => auth()->user()->id,
            'is_to_admin' => '1'
        ]);

        return redirect()->route('vendor.contacts.index')->with('success', 'Your message has been sent to admin.');
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

    public function vendorMessages()
    {
        $messages = Contact::
            where('is_to_admin', '1')
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->paginate(10);
        return inertia('Vendor/Compalins/index', [
            'vendorMessages' => $messages,
            'filters' => request()->all(['search', 'type', 'status']),
        ]);
    }
}