<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Validate reCAPTCHA token
     *
     * @param string|null $token
     * @param string|null $ip
     * @return bool
     */
    public function validate(?string $token, ?string $ip = null): bool
    {
        // Se não houver token, retorna false
        if (empty($token)) {
            return false;
        }

        // Se as chaves não estiverem configuradas, retorna true (modo desenvolvimento)
        $secretKey = config('services.recaptcha.secret_key');
        if (empty($secretKey)) {
            Log::warning('reCAPTCHA secret key not configured. Skipping validation.');
            return true; // Em desenvolvimento, permite passar sem validação
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            $result = $response->json();

            // Log para debugging (remover em produção se necessário)
            if (config('app.debug')) {
                Log::debug('reCAPTCHA validation response', [
                    'success' => $result['success'] ?? false,
                    'score' => $result['score'] ?? null,
                    'action' => $result['action'] ?? null,
                    'challenge_ts' => $result['challenge_ts'] ?? null,
                    'hostname' => $result['hostname'] ?? null,
                ]);
            }

            // Verifica se a validação foi bem-sucedida
            if (isset($result['success']) && $result['success'] === true) {
                // Para reCAPTCHA v3, também verifica o score (opcional)
                if (isset($result['score'])) {
                    $minScore = config('services.recaptcha.min_score', 0.5);
                    return $result['score'] >= $minScore;
                }
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation error: ' . $e->getMessage());
            // Em caso de erro na comunicação, retorna false por segurança
            return false;
        }
    }

    /**
     * Get reCAPTCHA site key for frontend
     *
     * @return string|null
     */
    public function getSiteKey(): ?string
    {
        return config('services.recaptcha.site_key');
    }
}

