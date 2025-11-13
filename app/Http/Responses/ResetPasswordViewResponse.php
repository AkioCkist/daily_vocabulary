<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Inertia\Inertia;

class ResetPasswordViewResponse implements \Laravel\Fortify\Contracts\ResetPasswordViewResponse, Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $request->route('token'),
            'email' => $request->email,
        ])->toResponse($request);
    }
}