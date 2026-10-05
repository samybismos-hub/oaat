<?php

namespace App\Http\Controllers\Public;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ContactController
{
    public function index(string $locale): mixed
    {
        return view('pages.contact', [
            'locale' => $locale,
        ]);
    }

    public function send(string $locale): mixed
    {
        $validator = Validator::make([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $validation = $validator->validate(request()->all());

        if ($validation->fails()) {
            return Redirect::back()
                ->with('errors', $validation->errors())
                ->with('old', request()->all());
        }

        ContactMessage::create([
            'name' => request('name'),
            'email' => request('email'),
            'subject' => request('subject'),
            'message' => request('message'),
        ]);

        return Redirect::to("/{$locale}/contact")
            ->with('success', __('contact.success'));
    }
}