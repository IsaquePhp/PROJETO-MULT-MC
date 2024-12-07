<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        return Client::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:20',
            'cpf' => 'required|string|max:14|unique:clients',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'postal_code' => 'required|string|max:9'
        ]);

        return Client::create($request->all());
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'cpf' => 'required|string|max:14|unique:clients,cpf,' . $client->id,
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'postal_code' => 'required|string|max:9'
        ]);

        $client->update($request->all());
        return $client;
    }

    public function toggleStatus(Client $client)
    {
        $client->active = !$client->active;
        $client->save();
        return $client;
    }
}
