<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class ServerDown extends Notification
{
    use Queueable;

    protected $name;
    protected $url;
    protected $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $url = null, $message = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $url = $this->url;
        $message = $this->message;

        if (is_null($url)) {
            return (new SlackMessage)
                ->success()
                ->content('server: '. $this->name .' is down');
        } else {
            return (new SlackMessage)
                ->success()
                ->content('server: '. $this->name .' is down')
                ->attachment(function ($attachment) use ($url, $message) {
                    $attachment->title('Response', $url)
                   ->content($message);
                });
        }
    }
}
