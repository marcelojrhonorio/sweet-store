<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class ProfileComposer
{
    public function compose(View $view)
    {
        $user = [
            'name'   => session('name') ?? '',
            'email'  => session('email') ?? '',
            'avatar' => session('avatar') ?? 'images/profile-default.png',
        ];

        $view->with('user', $user);
    }
}
