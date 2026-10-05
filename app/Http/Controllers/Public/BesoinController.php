<?php

namespace App\Http\Controllers\Public;

use App\Models\NeedRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class BesoinController
{
    public function index(string $locale): mixed
    {
        return view('pages.besoin', [
            'locale' => $locale,
        ]);
    }

    public function send(string $locale): mixed
    {
        $validator = Validator::make([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'organization' => ['string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
        ]);

        $validation = $validator->validate(request()->all());

        if ($validation->fails()) {
            return Redirect::back()
                ->with('errors', $validation->errors())
                ->with('old', request()->all());
        }

        NeedRequest::create([
            'name' => request('name'),
            'email' => request('email'),
            'organization' => request('organization'),
            'description' => request('description'),
        ]);

        return Redirect::to("/{$locale}/besoin")
            ->with('success', __('besoin.success'));
    }
}