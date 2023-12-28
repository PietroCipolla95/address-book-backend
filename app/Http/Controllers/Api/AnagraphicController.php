<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anagraphic;
use Illuminate\Http\Request;

class AnagraphicController extends Controller
{
    public function index()
    {
        $anagraphics = Anagraphic::with('contact')->get();
        return response()->json([
            'success' => true,
            'result' => $anagraphics
        ]);
    }

    public function show($id)
    {

        $anagraphic = Anagraphic::with('contact')->where('id', $id)->first();
        if ($anagraphic) {
            return response()->json([
                'success' => true,
                'result' => $anagraphic
            ]);
        } else {
            return response()->json([
                'success' => false,
                'result' => 'Ops! Page not found'
            ]);
        }
    }
}
