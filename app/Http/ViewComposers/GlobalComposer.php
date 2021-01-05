<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class GlobalComposer
{
    public function compose(View $view)
    {
        $currentUser = [
            'name'      => session('name') ?? '',
            'email'     => session('email') ?? '',
            'avatar'    => session('avatar') ?? 'images/profile-default.png',
            'confirmed' => (int) session('confirmed') ?? 0,
        ];

        $view->with('user', $currentUser);
    }
}
