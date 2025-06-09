<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;


use App\Models\Contact;
use App\Repositories\ContactUsRepository;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private $repository;

    public function __construct(ContactUsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendorId = $request->user()->vendor_id??$request->user()->id;
        
        $filters = [
            'name' => $request->name,
            'email' => $request->email,
            'read' => $request->read,
        ];

        $items = $this->repository->getFilteredContacts($filters);

        return Inertia('ContactUS/Index', [
            'items' => $items,
            'filters' => $filters,

        ]);
    }



    /**
     * Display the specified resource.
     */
      public function show($id)
    {
        $contact = $this->repository->markAsRead($id);
        return inertia('ContactUS/Show', ['contact' => $contact]);
    }





    public function read(Contact $contact)
    {

        $contact->update(
            [
                'read' => ($contact->read) ? 0 : 1
            ]
        );

        return redirect()->route('contacts.index')
            ->with('success', __('messages.status_updated'));
    }


        public function destroy(Contact $contact)
    {
        try {
            $this->repository->delete($contact);

            return redirect()
                ->route('contacts.index')
                ->with('success', 'deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


}