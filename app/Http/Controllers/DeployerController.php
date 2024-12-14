<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeployerController extends Controller
{
    public function manualDeploy($token) {
        return response()->json([
            'token' => $token
        ]);
    }
}
