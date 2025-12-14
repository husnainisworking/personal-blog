<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


/**
 * This class represents an EMAIL that will be sent
 * It extends Mailable, which is Laravel's base class for emails
 */
class TwoFactorCodeMail extends Mailable
{
    /**
     * use Queueable:
     * Allows this email to be queued (sent in background)
     * Instead of waiting for email to send, user sees page immediately
     * Email sends later in the background
     *
     * use SerializesModels
     * Is you pass Eloquent models to the mail, this safely converts them to data
     * that can be stored in the queue system
     */
    use Queueable, SerializesModels;

    /**
     * This is a PUBLIC property that holds the 6-digit code
     *
     * Why PUBLIC?
     * Because in the Blade view (emails/two_factor_code.blade.php),
     *
     * Laravel automatically makes all public properties available in the view
     */
    public $twoFactorCode;

    /**
     * Constructor - runs when you create a new email
     *
     * Example usuage:
     * new TwoFactorCodeMail(123456)
     *
     * The code(123456) is passed in and saved to $this->>twoFactorCode
     */
    public function __construct($twoFactorCode)
    {
        // Store the code so we can use it in the email template
        $this->twoFactorCode = $twoFactorCode;
    }

    /**
     * build() method
     *
     * This defines HOW the email should be built
     * It runs when Laravel is ready to send the email
     *
     * Returns a Mailable object with:
     *  - subject: What appears in email subject line
     *  - view: Which Blade template to use for email body
     */
    public function build()
    {
        return $this->subject('Your Two-Factor Authentication Code') //Email subject
            ->view('emails.two_factor_code'); // Points to resources/views/emails/two_factor_code.blade.php
    }

}
