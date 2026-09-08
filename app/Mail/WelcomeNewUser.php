<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeNewUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $role,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to MSU Internal Audit Management System',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-new-user',
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'userRole' => $this->role,
                'userPosition' => $this->user->position,
                'userDepartment' => $this->user->department,
                'userStaffId' => $this->user->staff_id,
                'loginUrl' => config('app.url'),
            ],
        );
    }
}
