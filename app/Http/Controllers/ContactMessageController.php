<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot spam protection
        if ($request->filled('website')) {
            return redirect('/contact-us#contact-form')
                ->with(
                    'contact_success',
                    'ধন্যবাদ! আপনার বার্তা গ্রহণ করা হয়েছে।'
                );
        }

        $data = $request->validate(
            [
                'name' => ['required', 'string', 'min:2', 'max:100'],
                'email' => ['required', 'email:rfc', 'max:254'],
                'subject' => ['required', 'string', 'min:3', 'max:150'],
                'message' => ['required', 'string', 'min:10', 'max:3000'],
            ],
            [
                'name.required' => 'আপনার নাম লিখুন।',
                'email.required' => 'আপনার ইমেইল লিখুন।',
                'email.email' => 'সঠিক ইমেইল ঠিকানা লিখুন।',
                'subject.required' => 'বিষয় লিখুন।',
                'message.required' => 'আপনার বার্তা লিখুন।',
                'message.min' => 'বার্তা কমপক্ষে ১০ অক্ষরের হতে হবে।',
            ]
        );

        $data['subject'] = Str::of($data['subject'])
            ->replaceMatches('/[\r\n]+/', ' ')
            ->limit(120, '')
            ->toString();

        $recipient = data_get(setting('company'), 'email') ?: config('mail.from.address');

        try {
            Mail::to($recipient)
                ->send(new ContactFormMessage($data));
        } catch (Throwable $e) {
            Log::error('Contact form mail failed', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'contact_error',
                    'দুঃখিত, আপনার বার্তা এই মুহূর্তে পাঠানো যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।'
                );
        }

        return redirect('/contact-us#contact-form')
            ->with(
                'contact_success',
                'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমাদের Customer Care টিম যত দ্রুত সম্ভব আপনার সঙ্গে যোগাযোগ করবে।'
            );
    }
}
