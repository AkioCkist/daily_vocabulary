<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class TokenManagerController extends Controller
{
    /**
     * Display the API Token Manager page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('TokenManager');
    }
}
