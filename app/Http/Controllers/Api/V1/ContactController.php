<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Contact;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\Vendor\VendorNotification;
use App\Http\Resources\Api\ContactResource;
use App\Http\Requests\Api\V1\ContactRequest;
use App\Http\Resources\Api\V1\ContactRsource;
use App\Notifications\Vendor\NewContactMessage;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Notifications\Admin\NewContactMessageAdmin;

class ContactController extends Controller
{

    use ApiResponseTrait;
    // List all contacts
    public function index()
    {
        $contacts = Contact::where('user_id', Auth::user()->id)->get();
        // return response()->json(['status' => 'success', 'data' => $contacts]);
        return $this->successResponse(
            ContactRsource::collection($contacts),
            'mobile.complains retrieved successfully'
        );
    }

    public function show($id)
    {
        // Find the contact by ID or throw 404
        $contact = Contact::findOrFail($id);

        // Verify ownership (optional but recommended)
        if ($contact->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Return the contact resource with a success response
        return $this->successResponse(
            new ContactRsource($contact),
            'Contact retrieved successfully'
        );
    }
    public function store(ContactRequest $request)
    {
        // Retrieve validated data
        $validated = $request->validated();

        // Add the authenticated user ID
        $validated['user_id'] = auth()->user()->id;

        // Create contact using mass assignment
        $contact = Contact::create($validated);


        // Send notification if vendor exists
        $this->notifyVendor($contact);

        return $this->successResponse(new ContactResource($contact), 'Contact message saved successfully.');
    }

    public function update(UpdateContactRequest $request, $id)
    {
        // Find the contact or throw 404
        $contact = Contact::findOrFail($id);

        // Validate incoming data
        $validated = $request->validated();

        // Ensure user owns the contact
        if ($contact->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Cast IDs to integers explicitly if needed (optional if using $casts)
        $validated['sale_order_id'] = isset($validated['sale_order_id']) ? (int) $validated['sale_order_id'] : null;
        $validated['rental_order_id'] = isset($validated['rental_order_id']) ? (int) $validated['rental_order_id'] : null;

        // Update the contact
        $contact->update($validated);

        // Return success response with resource
        return $this->successResponse(
            new ContactRsource($contact),
            'Contact updated successfully'
        );
    }

    public function destroy($id)
    {
        // Find the contact or throw 404
        $contact = Contact::findOrFail($id);

        // Verify ownership (optional)
        if ($contact->user_id !== auth()->user()->id) {
            return $this->errorResponse('Unauthorized', null, 403);
        }

        // Delete the contact
        $contact->delete();
        return $this->successResponse(null, 'Contact deleted successfully');

    }

    protected function notifyVendor(Contact $contact)
    {
        $vendor = null;

        if ($contact->rental_order_id) {
            $order = $contact->rentalOrder;
            $vendor = $order->branch->vendor ?? null;
        } elseif ($contact->sale_order_id) {
            $order = $contact->saleOrder;
            $vendor = $order->branch->vendor ?? null;
        }

        // If no specific vendor (general inquiry), you might want to notify all vendors
        // or a specific admin. Adjust this according to your business logic.
        if (!$vendor) {
            // Example: Notify all active vendors
            $vendors = User::where('role', 'vendor')->get();
            foreach ($vendors as $vendor) {
                $vendor->notify(new NewContactMessage($contact));
            }
            return;
        }
        $vendor->notify(new NewContactMessage($contact));


        $user = User::find($vendor->id);
        $title = $user->prefer_language === 'ar' ? 'شكوى أو اقتراح' : __('mobile.Complaint or Suggestion');
        $message = $user->prefer_language === 'ar' ? 'لديك شكوى أو اقتراح جديد' : __('mobile.You have a new complaint or suggestion');
        event(new VendorNotification($title, $message, $vendor->id));

        // // Always notify admins
        // $admins = User::whereHas('roles', function ($query) {
        //     $query->where('name', 'admin')
        //         ->orWhere('name', 'superadmin')
        //         ->whereNull('vendor_id');
        // })->get();
        // foreach ($admins as $admin) {
        //     $admin->notify(new NewContactMessageAdmin($contact));
        // }
    }
}
