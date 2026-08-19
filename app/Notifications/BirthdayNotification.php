<?php

namespace App\Notifications;

use App\Models\DataUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\CustomDatabaseChannel;

class BirthdayNotification extends Notification
{
    use Queueable;

    protected $birthdayUser;

    public function __construct($birthdayUser)
    {
        $this->birthdayUser = $birthdayUser;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toArray($notifiable)
{
    $user = User::find($this->birthdayUser->id_user);
    $dataUser = DataUser::where('id_user', $this->birthdayUser->id_user)->first();

    return [
        'message' => 'Hoy es el cumpleaños de ',
        'id_user' => [
            'id' => $this->birthdayUser->id_user,
            'name' => $user ? $user->name : null,
            'last_name' => $user ? $user->last_name : null,
            'url_img' => $this->birthdayUser->url_img,
            
        ], 
        'datosCUmple' => $this->birthdayUser->toArray(),
    ];
}
}

