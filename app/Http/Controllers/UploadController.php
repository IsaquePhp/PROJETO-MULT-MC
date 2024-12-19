<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // max 2MB
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Salvar no diretório public/uploads/products
                $path = $image->storeAs('uploads/products', $filename, 'public');
                
                return response()->json([
                    'url' => asset('storage/' . $path)
                ]);
            }
            
            return response()->json([
                'error' => 'Nenhuma imagem foi enviada'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao fazer upload da imagem'
            ], 500);
        }
    }
}
