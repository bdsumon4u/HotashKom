<?php

use App\Models\Admin;

it('displays the server info on admin dashboard', function () {
    $admin = Admin::factory()->create();

    $response = $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.home'));

    $response->assertStatus(200);
    $response->assertSee('Server Information');
    $response->assertSee('PHP Version');
    $response->assertSee('Server IP:');
});
