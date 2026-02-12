<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    /**
     * POST эндпоинт: /api/token/receive
     * Content-Type: application/x-www-form-urlencoded
     */
    public function receiveToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'Token' => 'required|string'
            ]);

            $token = $validated['Token'];

            $apiKey = config('services.token_validator.key');
            if (!$apiKey) {
                throw new \Exception('[TOKEN AUTH] API key is not configured');
            }

            $response = Http::withOptions([
                'verify' => config('app.mode') === 'production',
            ])->post(config('services.token_validator.url'), [
                'apiKey' => $apiKey,
                'token' => $token
            ]);

            if ($response->failed()) {
                Log::error('[TOKEN AUTH] Failed to check token', [
                    'token' => $token,
                    'status' => $response->status()
                ]);
                return redirect()->back()->with('error', 'Token validation failed');
            }

            $encodedEmail = $response->body();

            $originalKey = config('app.key');
            config()->set('app.key', config('services.token_validator.secret'));

            try {
                $decodedEmail = Crypt::decryptString($encodedEmail);
            } finally {
                config()->set('app.key', $originalKey);
            }

            Log::info('[TOKEN AUTH] Decoded email', ['email' => $decodedEmail]);

            // Ищем пользователя
            $user = User::where('email', $decodedEmail)->first();

            if (!$user) {
                Log::error('[TOKEN AUTH] User not found', ['email' => $decodedEmail]);
                return redirect()->back()->with('error', 'User not found');
            }

            Auth::logout();
            Auth::login($user, true); // true = remember me

            session()->regenerate();
            session()->save();

            // ПРОВЕРЯЕМ, ЧТО АВТОРИЗАЦИЯ РАБОТАЕТ
            if (!Auth::check()) {
                Log::error('[TOKEN AUTH] Auth check failed after login');
                throw new \Exception('Failed to authenticate user');
            }

            Log::info('[TOKEN AUTH] Successfully login', [
                'email' => $user->email,
                'session_id' => session()->getId(),
                'auth_check' => Auth::check()
            ]);

            return redirect(config('services.token_validator.redirect', '/'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Token validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->with('error', 'Validation failed');

        } catch (\Exception $e) {
            Log::error('Failed to receive token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to process token: ' . $e->getMessage());
        }
    }
}
