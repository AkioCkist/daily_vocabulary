<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Inertia\Inertia;

class RequestPasswordResetLinkViewResponse implements \Laravel\Fortify\Contracts\RequestPasswordResetLinkViewResponse, Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return Inertia::render('Auth/ForgotPassword')->toResponse($request);
    }
}