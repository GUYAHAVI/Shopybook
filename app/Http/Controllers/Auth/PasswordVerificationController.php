<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordVerificationController extends Controller
{
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8'
            ]);

            if (!Auth::check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            $isValid = Hash::check($request->password, Auth::user()->getAuthPassword());

            return response()->json([
                'status' => 'success',
                'valid' => $isValid,
                'message' => $isValid ? 'Password verified' : 'Invalid password'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'valid' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Password verification failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'valid' => false,
                'message' => 'Server error during verification'
            ], 500);
        }
    }
}