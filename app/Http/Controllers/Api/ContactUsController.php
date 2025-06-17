<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\AppBaseController;
use App\Http\Requests\Api\ContactEmailRequest;
use App\Http\Requests\ContactUsRequest;
use App\Http\Resources\Api\ContactResource;
use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\Admin\NewContactMessageAdmin;
use App\Notifications\ContactNotification;
use App\Notifications\Vendor\NewContactMessage;
use App\Repositories\ContactUsRepository;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class ContactUsController extends AppBaseController
{
    use ApiResponseTrait;

    private $contactUsRepository;

    public function index()
    {
        $contacts = Contact::where('user_id', auth()->user()->id)->get();

        return $this->successResponse(ContactResource::collection($contacts));
    }
    public function __construct(ContactUsRepository $contactUsRepository)
    {
        $this->contactUsRepository = $contactUsRepository;
    }

    public function complainOrSuggestion(ContactUsRequest $request)
    {

        try {
            $input = $request->validated();

            $input['user_id'] = auth()->user()->id;
            $input['is_to_admin'] = 1;

            $contact = Contact::create($input);

            // $admin = User::find(1);

            // Sent an Email to User
            // Mail::to($request->get('email'))->send(new ContactMail());

            // Sent an Email to Administrator
            // if ($admin)
            // $admin->notify(new ContactNotification($input, $contactUs->id));

            // Send notification if vendor exists
            $this->notifyVendor($contact);

            return $this->successResponse([], __('mobile.message sent successfully.'));
        } catch (TransportException $e) {
            // return $this->sendError(__('Email not valid'),['email' => __('Email not valid')]);
        }
    }

    public function store(ContactEmailRequest $request)
    {
        $input = $request->validated();

        $contactUs = Contact::create($input);

        return $this->successResponse(new ContactResource($contactUs), __('mobile.Contact us created successfully'));
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
        // if (!$vendor) {
        //     // Example: Notify all active vendors
        //     $vendors = User::where('role', 'vendor')->get();
        //     foreach ($vendors as $vendor) {
        //         $vendor->notify(new NewContactMessage($contact));
        //     }
        //     return;
        // }
        // $vendor->notify(new NewContactMessage($contact));


        // Always notify admins
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin')
                ->orWhere('name', 'superadmin')
                ->whereNull('vendor_id');
        })->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewContactMessageAdmin($contact));
        }
    }

}
