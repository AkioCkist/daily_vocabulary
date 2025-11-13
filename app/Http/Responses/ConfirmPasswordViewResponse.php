<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Inertia\Inertia;

class ConfirmPasswordViewResponse implements \Laravel\Fortify\Contracts\ConfirmPasswordViewResponse, Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return Inertia::render('Auth/ConfirmPassword')->toResponse($request);
    }
}