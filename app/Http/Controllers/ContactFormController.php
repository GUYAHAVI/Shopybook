<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactFormController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'business_type' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        try {
            // Send email to info@shopybook.com
            Mail::to('info@shopybook.com')->send(new ContactFormMail($validated));

            // Return success response
            return back()->with('success', 'Thank you for contacting us! We will get back to you shortly.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Contact form submission failed: ' . $e->getMessage());

            // Return error response
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again or contact us directly at info@shopybook.com.');
        }
    }
}
