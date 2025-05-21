<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Contact;
use App\Mail\ContactMail;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactUsRequest;
use App\Repositories\ContactUsRepository;
use App\Notifications\ContactNotification;
use App\Http\Resources\Api\ContactResource;
use App\Http\Controllers\Api\AppBaseController;
use App\Http\Requests\Api\ContactEmailRequest;
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
            
            $contactUs = Contact::create($input);

            $admin = User::find(1);

            // Sent an Email to User
            // Mail::to($request->get('email'))->send(new ContactMail());

            // Sent an Email to Administrator
            if ($admin)
                // $admin->notify(new ContactNotification($input, $contactUs->id));

            return $this->successResponse([],__('mobile.message sent successfully.'));
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

    
}
