<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Account;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::latest()->paginate(10);

        return ['accounts' => $accounts];
    }
}