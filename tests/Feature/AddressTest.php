<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->post(
            "/api/contacts/{$contact->id}/addresses",
            [
                'street' => 'test',
                'city' => 'test',
                'province' => 'test',
                'country' => 'test',
                'postal_code' => '213123',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(201);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->post(
            "/api/contacts/{$contact->id}/addresses",
            [
                'street' => 'test',
                'city' => 'test',
                'province' => 'test',
                'country' => '',
                'postal_code' => '213123',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $this->post(
            "/api/contacts/salah/addresses",
            [
                'street' => 'test',
                'city' => 'test',
                'province' => 'test',
                'country' => 'test',
                'postal_code' => '213123',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Contact not found'
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->get("/api/contacts/{$address->contact_id}/addresses/{$address->id}", [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->get("/api/contacts/{$address->contact_id}/addresses/salah", [
            'Authorization' => 'Bearer test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Address not found'
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->put(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => '22222'
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(200);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->put(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => '',
                'postal_code' => '22222'
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->put(
            "/api/contacts/{$address->contact_id}/addresses/salah",
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => '22222'
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Address not found'
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->delete(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => ["deleted" => true]
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::first();

        $this->delete(
            "/api/contacts/{$address->contact_id}/addresses/salah",
            [],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Address not found'
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::first();

        $this->get(
            "/api/contacts/{$contact->id}/addresses",
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(200);
    }

    public function testListContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $this->get(
            "/api/contacts/salah/addresses",
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Contact not found'
                ]
            ]);
    }
}
