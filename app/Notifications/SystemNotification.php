<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SystemNotification extends Notification
{
    use Queueable;

    protected $details;

    /**
     * Create a new notification instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->cc(config('mail.from.address'))
                    ->subject($this->details['title'])
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line($this->details['message'])
                    ->action('View Details', $this->details['link'])
                    ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->details['title'] ?? 'New Notification',
            'message' => $this->details['message'] ?? '',
            'type' => $this->details['type'] ?? 'info',
            'link' => $this->details['link'] ?? '#',
            'icon' => $this->details['icon'] ?? 'bell',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->details['title'] ?? 'New Notification',
            'message' => $this->details['message'] ?? '',
            'type' => $this->details['type'] ?? 'info',
            'link' => $this->details['link'] ?? '#',
            'icon' => $this->details['icon'] ?? 'bell',
            'created_at' => now()->diffForHumans(),
        ]);
    }
}
