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
            // Валидация входящего запроса
            $validated = $request->validate([
                'Token' => 'required|string'
            ]);

            $token = $validated['Token'];

            $this->checkToken($token);

            Log::info('[TOKEN AUTH] Token received', ['token' => $token]);

            return response()->json([
                'success' => true,
                'message' => 'Token successfully received',
                'token' => $token
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Token validation failed', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to receive token', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process token'
            ], 500);
        }
    }

    /**
     * POST запрос на: /api/suggestion/checkToken
     * Content-Type: application/json
     */
    public function checkToken(string $token): bool
    {
        try {

            // API ключ для доступа (должен быть в .env)
            $apiKey = config('services.token_validator.key');

            if (!$apiKey) {
                throw new \Exception('[TOKEN AUTH] API key is not configured');
            }

            // Отправка запроса на ais.mosgortur.ru
            $response = Http::withOptions([
                'verify' => config('app.mode') === 'production',
            ])->post(config('services.token_validator.url'), [
                'apiKey' => $apiKey,
                'token' => $token
            ]);

            // Проверка успешности запроса
            if ($response->failed()) {
                Log::error('[TOKEN AUTH] Failed to check token', [
                    'token' => $token,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return false;
            }

            // Получаем закодированную почту из ответа
            $encodedEmail = $response->body();

            Log::info('[TOKEN AUTH] Token successfully verified', ['token' => $token]);

            config()->set('app.key', config('services.token_validator.secret'));

            Log::info('[TOKEN AUTH] Trying to decode', ['encodedEmail' => $encodedEmail]);
            $decodedEmail = Crypt::decrypt($encodedEmail);
            Log::info('[TOKEN AUTH] Decode successfully', ['decodedEmail' => $decodedEmail]);

            $user = User::where('email', $decodedEmail)->first();

            if ($user instanceof User) {
                Auth::logout();
                Auth::loginUsingId($user->id);
                Log::info('[TOKEN AUTH] Successfully login', ['email' => $user->email]);
            } else {
                Log::info('[TOKEN AUTH] User with email not found ', ['email' => $decodedEmail]);
            }

            // Возвращаем закодированную почту
            return true;

        } catch (\Exception $e) {
            Log::error('[TOKEN AUTH] Failed to check token', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
