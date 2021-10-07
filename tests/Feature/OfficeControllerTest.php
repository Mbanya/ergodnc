<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Office;
use App\Models\Reservation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OfficeControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_it_lists_offices_in_paginated_form()
    {
        Office::factory(3)->create();
        $response = $this->get('/api/offices');

        $response->assertOk();
        $response->assertJsonStructure(['meta','links']);
        $this->assertNotNull($response->json('data')[0]['id']);
    }

    public function test_offices_not_hidden_and_approved()
    {
        Office::factory(3)->create();

        Office::factory()->create(['hidden'=>true]);
        Office::factory()->create(['approval_status'=>Office::APPROVAL_PENDING]);

        $response = $this->get('/api/offices');
        $response->assertOk();
        $response->assertJsonCount(3,'data');
//        $response->dump();

        $this->assertNotNull($response->json('data')[0]['id']);
    }

    public function test_it_filters_by_host_id()
    {
        Office::factory(3)->create();

        $host = User::factory()->create();

        $office = Office::factory()->for($host)->create();

        $response = $this->get('/api/offices?host_id='.$host->id);
        $response->assertOk();
        $response->assertJsonCount(1,'data');
        $this->assertEquals($office->id, $response->json('data')[0]['id']);
//        $response->dump();

        $this->assertNotNull($response->json('data')[0]['id']);

    }

    public function test_it_filters_by_user_id()
    {
        Office::factory(3)->create();

        $user = User::factory()->create();

        $office = Office::factory()
            ->create();

        Reservation::factory()->for(Office::factory()->create())->create();

        Reservation::factory()->for($office)->for($user)->create();

        $response = $this->get('/api/offices?user_id='.$user->id);
        $response->assertOk();
        $response->assertJsonCount(1,'data');
        $this->assertEquals($office->id, $response->json('data')[0]['id']);
//        $this->assertNotNull($response->json('data')[0]['id']);

    }

    public function test_it_includes_tags_images_user()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $office = Office::factory()
            ->for($user)
            ->create();

        $office->tags()->attach($tag);
        $office->images()->create(['path'=>'image.jpg']);

        $response = $this->get('/api/offices');

        $response->assertOk();
        $this->assertIsArray($response->json('data')[0]['tags']);
        $this->assertIsArray($response->json('data')[0]['images']);
        $this->assertEquals($user->id,$response->json('data')[0]['user']['id']);

    }

    public function test_it_returns_number_of_active_reservations()
    {
        $office = Office::factory()
            ->create();
        Reservation::factory()->for($office)->create(['status'=>Reservation::STATUS_ACTIVE]);
        Reservation::factory()->for($office)->create(['status'=>Reservation::STATUS_CANCELLED]);
        $response = $this->get('/api/offices');
        $response->assertOk();
        $response->assertJsonCount(1,'data');

        $response->dump();
    }

    public function test_it_orders_by_distance_when_coords_are_provided()
    {
//        38.72539461008931, -9.138575534660234

        $office1 = Office::factory()->create([
            'lat' =>'39.7494120668652',
            'lng' => '-8.808083114949584',
            'title'=>'Leiria'
        ]);
        $office2 = Office::factory()->create([
            'lat' =>'39.09887199290368',
            'lng' => '-9.260925822241425',
            'title'=>'Torres Vedras'
        ]);

        $response = $this->get('/api/offices?lat=38.72539461008931&lng=-9.138575534660234');

        $response->assertOk();
        $this->assertEquals('Torres Vedras',$response->json('data')[0]['title']);
        $this->assertEquals('Leiria',$response->json('data')[1]['title']);


    }
}
