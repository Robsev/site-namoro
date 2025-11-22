<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Services\RecaptchaService;

class ContactController extends Controller
{
    /**
     * Show contact page
     */
    public function index()
    {
        return view('legal.contact');
    }

    /**
     * Send contact form
     */
    public function send(Request $request)
    {
        // Validate reCAPTCHA if configured
        if (config('services.recaptcha.site_key')) {
            $recaptchaService = app(RecaptchaService::class);
            $recaptchaToken = $request->input('g-recaptcha-response');
            
            if (!$recaptchaService->validate($recaptchaToken, $request->ip())) {
                return redirect()->back()
                    ->withErrors(['g-recaptcha-response' => 'Por favor, complete a verificação reCAPTCHA.'])
                    ->withInput();
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|in:general,technical,account,billing,privacy,other',
            'message' => 'required|string|min:10|max:2000',
            'privacy' => 'required|accepted'
        ], [
            'name.required' => __('messages.legal.contact.form.validation.name_required'),
            'email.required' => __('messages.legal.contact.form.validation.email_required'),
            'email.email' => __('messages.legal.contact.form.validation.email_invalid'),
            'subject.required' => __('messages.legal.contact.form.validation.subject_required'),
            'message.required' => __('messages.legal.contact.form.validation.message_required'),
            'message.min' => __('messages.legal.contact.form.validation.message_min'),
            'message.max' => __('messages.legal.contact.form.validation.message_max'),
            'privacy.required' => __('messages.legal.contact.form.validation.privacy_required'),
            'privacy.accepted' => __('messages.legal.contact.form.validation.privacy_accepted'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if mail is configured
            if (config('mail.default') === 'log') {
                // If using log driver, just log the message
                \Log::info('Contact form submission (logged instead of sent):', [
                    'name' => $request->name,
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'message' => $request->message
                ]);
                
                return redirect()->back()->with('success', __('messages.legal.contact.form.success'));
            }
            
            // Send email
            Mail::raw($this->formatMessage($request), function ($message) use ($request) {
                $message->to('suporte@sintoniadeamor.com.br')
                    ->subject('[Sintonia de Amor] ' . $this->getSubjectText($request->subject) . ' - ' . $request->name)
                    ->replyTo($request->email, $request->name);
            });

            return redirect()->back()->with('success', __('messages.legal.contact.form.success'));

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Contact form error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => __('messages.legal.contact.form.error')])
                ->withInput();
        }
    }

    /**
     * Format message for email
     */
    private function formatMessage(Request $request)
    {
        $subjectText = $this->getSubjectText($request->subject);
        
        return "
Nome: {$request->name}
E-mail: {$request->email}
Assunto: {$subjectText}

Mensagem:
{$request->message}

---
Enviado em: " . now()->format('d/m/Y H:i:s') . "
IP: " . $request->ip() . "
User Agent: " . $request->userAgent() . "
        ";
    }

    /**
     * Get subject text in current language
     */
    private function getSubjectText($subject)
    {
        $subjects = [
            'general' => __('messages.legal.contact.form.subject_general'),
            'technical' => __('messages.legal.contact.form.subject_technical'),
            'account' => __('messages.legal.contact.form.subject_account'),
            'billing' => __('messages.legal.contact.form.subject_billing'),
            'privacy' => __('messages.legal.contact.form.subject_privacy'),
            'other' => __('messages.legal.contact.form.subject_other'),
        ];

        return $subjects[$subject] ?? $subject;
    }
}
