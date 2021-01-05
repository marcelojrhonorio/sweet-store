<div id="profile">
  <a class="profile-link" title="Editar perfil" href="{{ url('profile') }}">
    <img class="profile-photo" src="{{ asset($user['avatar']) }}">
    <span class="profile-name">
        {{ $user['name'] }}
    </span>
    <span class="profile-email">
        {{ $user['email'] }}
    </span>
  </a>
  <div class="profile-points">
    <img src="{{ asset('images/profile-points.png') }}">
    <span data-points-total>{{ number_format(session('points'),0,",",".") }}</span><span>pontos</span>
  </div>
</div>
