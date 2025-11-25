<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $hidePrefix = setting('show_option')->hide_phone_prefix ?? false;
        $data['phone'] = preg_replace('/[^0-9+]/', '', $data['phone']);

        if (! $hidePrefix && ! Str::startsWith($data['phone'], '+880')) {
            $data['phone'] = '+880'.$data['phone'];
        }

        Lead::create($data);

        return back()->with([
            'lead_submitted' => true,
            'lead_message' => 'Thank you for reaching out. Our team will contact you soon.',
        ]);
    }
}
