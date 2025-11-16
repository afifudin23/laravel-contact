<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddressController extends Controller
{
    private function getContact(string $userId, string $id): Contact
    {
        $contact = Contact::where('id', $id)->where('user_id', $userId)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => ["message" => "Contact not found"]
            ], 404));
        }
        return $contact;
    }
    private function getAddress(string $contactId, string $addressId): Address
    {
        $address = Address::where("id", $addressId)->where("contact_id", $contactId)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => ["message" => "Address not found"]
            ], 404));
        }
        return $address;
    }

    public function create(string $contactId, AddressRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);

        $data = $request->validated();
        $data["contact_id"] = $contact->id;
        $address = Address::create($data);
        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(string $contactId, string $addressId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $address = $this->getAddress($contact->id, $addressId);

        return response()->json($address)->setStatusCode(200);
    }

    public function update(string $contactId, string $addressId, AddressRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $address = $this->getAddress($contact->id, $addressId);

        $data = $request->validated();
        $address->update($data);

        return (new AddressResource($address))->response();
    }

    public function delete(string $contactId, string $addressId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $address = $this->getAddress($contact->id, $addressId);

        $address->delete();

        return response()->json(['data' => ["deleted" => true]]);
    }

    public function list(string $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $addresses = Address::where("contact_id", $contact->id)->get();

        return response()->json($addresses);
    }
}
