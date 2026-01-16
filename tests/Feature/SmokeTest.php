<?php

it('returns a successful response for the homepage', function () {
    $response = $this->get('/');

    $response->assertOk();
});

it('returns a successful response for health checks', function () {
    $response = $this->get('/health');

    $response->assertOk();
});
