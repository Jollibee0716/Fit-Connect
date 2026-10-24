<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordNotification extends ResetPassword
{
    // Inherits the constructor ($token), via(), and the reset URL
    // (route 'password.reset' with ?email=...) from Laravel's ResetPassword.
}