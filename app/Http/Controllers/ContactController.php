<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $contact = Contact::create($data);
        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function getById(string $id): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            return response()->json([
                'errors' => [
                    'message' => 'Contact not found'
                ]
            ])->setStatusCode(404);
        }
        return response()->json($contact);
    }
    public function update(string $id, ContactRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            return response()->json([
                'errors' => [
                    'message' => 'Contact not found'
                ]
            ])->setStatusCode(404);
        }
        $data = $request->validated();
        $contact->update($data);

        return (new ContactResource($contact))->response();
    }
    public function delete(string $id)
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            return response()->json([
                'errors' => [
                    'message' => 'Contact not found'
                ]
            ])->setStatusCode(404);
        }
        $contact->delete();

        return response()->json([
            'data' => ['deleted' => true]
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where("user_id", $user->id);

        // $contacts = $contacts->where(function (Builder $builder) use ($request) {
        //     $name = $request->input('name');
        //     if ($name) {
        //         $builder->where(function (Builder $builder) use ($name) {
        //             $builder->orWhere('first_name', 'like', "%$name%");
        //             $builder->orWhere('last_name', 'like', "%$name%");
        //         });
        //     }

        //     $email = $request->input('email');
        //     if ($email) {
        //         $builder->where(function (Builder $builder) use ($email) {
        //             $builder->orWhere('email', 'like', "%$email%");
        //         });
        //     }

        //     $phone = $request->input('phone');
        //     if ($phone) {
        //         $builder->where(function (Builder $builder) use ($phone) {
        //             $builder->orWhere('phone', 'like', "%$phone%");
        //         });
        //     }
        // });
        // $contacts = $contacts->paginate(perPage: $size, page: $page);

        $contacts = Contact::query()->where('user_id', $user->id)
            ->when(
                $request->input('name'),
                fn($q, $name) => $q->where(
                    fn($q) =>
                    $q->where('first_name', 'like', "%$name%")
                        ->orWhere('last_name', 'like', "%$name%")
                )
            )
            ->when(
                $request->input('email'),
                fn($q, $email) => $q->where('email', 'like', "%$email%")
            )
            ->when(
                $request->input('phone'),
                fn($q, $phone) => $q->where('phone', 'like', "%$phone%")
            )
            ->paginate($size); // page otomatis dari ?page=


        return response()->json([
            'data' => ContactResource::collection($contacts->items()),
            'meta' => [
                'total'        => $contacts->total(),
                'per_page'     => $contacts->perPage(),
                'current_page' => $contacts->currentPage(),
                'last_page'    => $contacts->lastPage(),
            ]
        ]);
    }
}
