<?php

use App\Models\Admin;
use App\Models\Lead;

it('stores a lead submission', function () {
    $formUrl = route('leads.form');

    $response = $this->from($formUrl)->post(route('leads.store'), [
        'name' => 'Test Lead',
        'shop_name' => 'Test Shop',
        'email' => 'lead@example.com',
        'phone' => '01551234567',
        'message' => 'Please reach out to me.',
    ]);

    $response->assertRedirect($formUrl);
    $response->assertSessionHas('lead_submitted', true);

    $this->assertDatabaseHas('leads', [
        'name' => 'Test Lead',
        'shop_name' => 'Test Shop',
        'email' => 'lead@example.com',
        'phone' => '+8801551234567',
    ]);
});

it('filters leads in the admin list and allows deletion', function () {
    $admin = Admin::factory()->create();
    $lead = Lead::factory()->create(['name' => 'Unique Lead']);
    Lead::factory()->create(['name' => 'Another Lead']);

    $indexResponse = $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.leads.index', ['search' => 'Unique']));

    $indexResponse->assertOk();
    $indexResponse->assertSeeText('Unique Lead');
    $indexResponse->assertDontSeeText('Another Lead');

    $deleteResponse = $this
        ->actingAs($admin, 'admin')
        ->delete(route('admin.leads.destroy', $lead));

    $deleteResponse->assertRedirect(route('admin.leads.index'));
    $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
});
