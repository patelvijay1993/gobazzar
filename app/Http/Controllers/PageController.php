<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function advertise()
    {
        return view('pages.advertise');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:20|max:2000',
        ]);

        // Store enquiry in session and notify via email (if mail is configured)
        try {
            Mail::raw(
                "Name: {$request->name}\nEmail: {$request->email}\nSubject: {$request->subject}\n\nMessage:\n{$request->message}",
                fn ($m) => $m->to(config('mail.from.address', 'admin@gobazaar.ca'))
                             ->subject("GoBazaar Contact: {$request->subject}")
            );
        } catch (\Throwable $e) {
            // Mail not configured — silently continue
        }

        return back()->with('success', 'Thank you! We will get back to you within 1–2 business days.');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }
}
