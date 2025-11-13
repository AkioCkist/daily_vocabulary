<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Inertia\Inertia;

class VerifyEmailViewResponse implements \Laravel\Fortify\Contracts\VerifyEmailViewResponse, Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return Inertia::render('Auth/VerifyEmail')->toResponse($request);
    }
}