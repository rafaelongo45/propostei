<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('seo.contato', [
            'title' => 'Entre em contato | propostei',
            'description' => 'Fale com o propostei para tirar dúvidas, enviar sugestões ou pedir ajuda sobre propostas comerciais online.',
            'h1' => 'Entre em contato',
            'canonical' => 'https://propostei.com/contato',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => mb_strtolower(trim((string) $request->input('email'))),
            'subject' => trim((string) $request->input('subject')),
            'message' => trim((string) $request->input('message')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:180'],
            'subject' => ['nullable', 'string', 'max:140'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['nullable', 'string', 'max:0'],
        ], [
            'name.required' => 'Informe seu nome.',
            'name.max' => 'O nome deve ter no máximo 120 caracteres.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 180 caracteres.',
            'subject.max' => 'O assunto deve ter no máximo 140 caracteres.',
            'message.required' => 'Escreva sua mensagem.',
            'message.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
            'message.max' => 'A mensagem deve ter no máximo 3000 caracteres.',
            'website.max' => 'Não foi possível enviar a mensagem.',
        ]);

        $recipientAddress = config('mail.contact.address') ?: config('mail.from.address');

        Mail::to($recipientAddress, config('mail.contact.name'))
            ->send(new ContactMessageMail(
                name: $validated['name'],
                email: $validated['email'],
                subject: $validated['subject'] ?? null,
                messageText: $validated['message'],
            ));

        return back()->with('status', 'Recebemos sua mensagem. Vamos responder em breve.');
    }
}
