<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        $parts = explode(' ', (string) $user->full_name, 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';
        
        return response()->json([
            'data' => [
                'id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'role_name' => $user->role->name ?? '',
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users,email,' . $user->id,
            'avatar_url' => 'nullable|string|url',
        ]);

        $user->full_name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $user->email = $validated['email'];
        
        if (array_key_exists('avatar_url', $validated)) {
            $user->avatar_url = $validated['avatar_url'];
        }
        
        if (!empty($request->new_password)) {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|same:new_password_confirmation',
            ]);
            
            if (!Hash::check($request->current_password, $user->password_hash)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The provided current password does not match our records.'],
                ]);
            }
            
            $user->password_hash = Hash::make($request->new_password);
        }
        
        $user->save();
        
        return $this->show($request);
    }
}
