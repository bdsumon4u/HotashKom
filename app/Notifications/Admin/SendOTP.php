<?php

namespace App\Notifications\Admin;

use App\Notifications\SMSChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

use function url;

class SendOTP extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $otp) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return [SMSChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray($notifiable): array
    {
        $phone = preg_replace('/[^\d]/', '', setting('company')->phone);
        $phone = Str::startsWith($phone, '0')
            ? '88'.$phone
            : Str::replaceFirst('+', '', $phone);

        return [
            'contacts' => $phone,
            'msg' => str_replace('[code]', $this->otp, setting('SMSTemplates')->otp),
        ];
    }
}
