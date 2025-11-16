<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSearchSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateContactSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            '/api/contacts',
            [
                'first_name' => 'first test',
                'last_name' => 'last test',
                'email' => 'emailtest@mail.co',
                'phone' => '123456789101',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(201);
    }
    public function testCreateContactFailed(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            '/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'last test',
                'email' => 'emailtest@mail.co',
                'phone' => '123456789101',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(422)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.',
                ]
            ]
        ]);
    }

    public function testGetContactByIdSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();
        $this->get(
            '/api/contacts/' . $contact->id,
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(200);
    }
    public function testGetContactByIdNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $this->get(
            '/api/contacts/' . 'yayaya',
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404);
    }

    public function testUpdateContactSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();
        $this->put(
            '/api/contacts/' . $contact->id,
            [
                'first_name' => 'update test',
                'last_name' => 'update test',
                'email' => 'updatetest@mail.co',
                'phone' => '1234567891034',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(200);
    }
    public function testUpdateContactFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $this->put(
            '/api/contacts/salah',
            [
                'first_name' => 'update test',
                'last_name' => 'update test',
                'email' => 'updatetest1@mail.co',
                'phone' => '1234567891025',
            ],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404)->assertJson([
            'errors' => [
                'message' => 'Contact not found'
            ]
        ]);
    }

    public function testDeleteContactSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();
        $this->delete(
            '/api/contacts/' . $contact->id,
            [],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(200);
    }
    public function testDeleteContactFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $this->delete(
            '/api/contacts/salah',
            [],
            [
                'Authorization' => 'Bearer test'
            ]
        )->assertStatus(404)->assertJson([
            'errors' => [
                'message' => 'Contact not found'
            ]
        ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, ContactSearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, ContactSearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, ContactSearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, ContactSearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=11111', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, ContactSearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, ContactSearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
