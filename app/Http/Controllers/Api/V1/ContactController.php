<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ContactRequest;
use App\Http\Resources\Api\V1\ContactRsource;
use App\Models\Contact;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    public function store(ContactRequest $request)
    {
        // Retrieve validated data
        $validated = $request->validated();

        // Add the authenticated user ID
        $validated['user_id'] = auth()->user()->id;

        // Create contact using mass assignment
        $contact = Contact::create($validated);

        return $this->successResponse($contact, 'Contact message saved successfully.');
    }
}