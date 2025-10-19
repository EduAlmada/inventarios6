<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index()
        {
            $users = User::all();
            return view('admin.users.index', compact('users'));
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Pasa el usuario a la vista de edición
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validar la solicitud (mantenemos la validación de name y email)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        // 2. Determinar si es administrador (CORRECCIÓN CRUCIAL)
        // Usamos has() para verificar si el campo 'is_admin' existe en el request.
        // Si existe, es true (1). Si no existe, es false (0).
        $isAdmin = $request->has('is_admin'); 

        // 3. Actualizar el usuario
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $isAdmin, // Usamos la variable booleana correctamente determinada
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}