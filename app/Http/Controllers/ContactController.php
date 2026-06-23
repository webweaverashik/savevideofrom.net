<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Services\Settings\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(SettingsService $settings): View
    {
        $page = Page::where('slug', 'contact')->where('is_published', true)->first();

        return view('pages.contact', [
            'page'    => $page,
            'email'   => $settings->get('contact_email'),
            'phone'   => $settings->get('contact_phone'),
            'address' => $settings->get('contact_address'),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $data['ip_hash'] = hash('sha256', (string) $request->ip());
        ContactMessage::create($data);

        return back()->with('contact_success', 'Thanks! Your message has been sent.');
    }
}
