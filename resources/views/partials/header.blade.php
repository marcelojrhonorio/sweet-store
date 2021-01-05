<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <div class="menu-bar">
    <a class="sidebar-toggle mr-3" href="#">
      <i class="fa fa-bars"></i>
    </a>
    {{--<a class="notifications-mobile" href="#">--}}
      {{--<img src="{{ asset('images/header-notifications-icon.png') }}" alt="">--}}
    {{--</a>--}}
    {{--<a class="search-mobile" href="#">--}}
      {{--<img src="{{ asset('images/header-search-icon.png') }}" alt="">--}}
    {{--</a>--}}
  </div>
  <div class="row">
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-3">
          <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/header-logo.png') }}" alt="Sweet Bonus">
          </a>
        </div>
        <div class="col-md-6 offset-md-3 sr-only">
          <form>
            <input type="text" name="search" placeholder="Busca Inteligente">
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-2 notifications sr-only">
      <span class="icon"></span>
      <span>Você tem notificações</span>
    </div>
  </div>
</nav>
