<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function userProfile()
    {
        return response()->json(Auth::user());
    }

    public function makeAdmin($id)
    {
        $adminUser = auth()->user();

        if (!$adminUser || !$adminUser->is_admin) {
            return response()->json(['error' => 'Apenas admins podem promover usuários.'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $user->is_admin = true;
        $user->save();

        return response()->json(['message' => 'Usuário promovido a administrador com sucesso.']);
    }

    public function removeAdmin($id) {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
    
        if (!auth()->user()->is_admin) {
            return response()->json(['message' => 'Apenas administradores podem remover privilégios de admin'], 403);
        }
    
        if ($user->id === auth()->user()->id) {
            return response()->json(['message' => 'Você não pode remover seu próprio acesso de administrador'], 403);
        }
    
        $user->is_admin = false;
        $user->save();
    
        return response()->json(['message' => 'Usuário rebaixado para usuário comum com sucesso'], 200);
    }
    
}
