<?php

namespace App\Repositories;
use App\Http\Requests\ContactUsRequest;
use App\Models\Contact;

class ContactUsRepository
 {

    protected $model;

    /**
     * ContactUsRepository constructor.
     *
     * @param Contact $ContactUs
     */

    public function __construct( Contact $ContactUs )
    {
        $this->model = $ContactUs;
    }

    public function createContactUsRequest( array $data ): Contact
    {
        return $this->model->create( $data );
    }

    public function getAll()
    {
        return $this->model->orderBy('created_at','desc')->get();
    }

    public function findById( $id )
    {
        return $this->model->findOrFail( $id );
    }


    public function getFilteredContacts( $filters )
    {
         $query = $this->model->query();

        if ($filters['name']) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }
        if ($filters['email']) {
            $query->where('email', 'LIKE', "%{$filters['email']}%");
        }


        if (isset($filters['read'])) {
            $query->where('read', $filters['read']);
        }

        return $query->paginate();
    }

       public function delete($model)
    {
        $model->delete();
    }

       public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);

        if (!$contact->read) {
            $contact->update([
                'read' => true
            ]);
        }

        return $contact;
    }


}