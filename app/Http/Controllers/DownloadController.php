<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        
        // Android cihaz kontrolü
        if (preg_match('/Android/i', $userAgent)) {
            return redirect('https://play.google.com/store/apps/details?id=com.tfsoisrael.mobile');
        }
        
        // iOS veya diğer cihazlar için download sayfası
        return Inertia::render('Downloads/Index');
    }
}