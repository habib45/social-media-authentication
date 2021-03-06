<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    /**
     * Send a mail with the message to the email specified in the contact form
     *
     * @param  Request  $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|min:3',
            'email' => 'required|email|max:255',
            'message'    => 'required'
        ]);

        $name = $request->input('name');
        $emailToSendTo = $request->input('email');
        $body = $request->input('message');

        Mail::send('emails.contact', ['body' => $body], function ($message) use ($name,$emailToSendTo) {
            $message->from('unicodeveloper@hackathon-starter.com', "From: {$name}");

            $message->to($emailToSendTo)->subject("Message From Laravel Hackathon Starter Contact Form");
        });

        return redirect()->route('contact')->with('info','Your Message has been dispatched successfully');
    }

}
