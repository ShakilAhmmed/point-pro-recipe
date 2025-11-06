<?php

it('user registers', function () {

    $payload = [
        'name' => 'Shakil Ahmmed',
        'email' => 'shakilfci461@gmail.com',
        'password' => '11223344',
        'password_confirmation' => '11223344',
    ];

    $res = $this->postJson('/api/v1/auth/register', $payload);
    $res->assertCreated()
        ->assertJsonStructure([
            'code', 'message', 'data' => ['id', 'name', 'email'],
        ]);

});
