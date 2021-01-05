<ul class="list-unstyled">
  {{-- By Category --}}
  <li>
    <span class="title">
      <img src="{{ asset('images/menu-icon-trocarpontos.png') }}">
      Trocar pontos
      <span class="subtitle">
        Por categoria
      </span>
    </span>
  </li>

  @foreach ($categories as $category)
  <li>
    <a href="{{ url('exchange/' . $category->id) }}">
      <img src="{{ asset($category->icon) }}">
      {{ $category->name }}

      @if($category->news)
        <p><span class="tag-new">Novo!</span></p>
      @endif
    </a>    
  </li>
  @endforeach

  {{-- By Points --}}
  <li>
    <span class="title">
      <span class="subtitle">
        Por pontos
      </span>
    </span>
  </li>
  <li>
    <a href="{{ url('exchange/min/1/max/2000') }}">
      <img src="{{ asset('images/menu-icon-pontos.png') }}">
      de 0 a 2.000
    </a>
  </li>
  <li>
    <a href="{{ url('exchange/min/2001/max/5000') }}">
      <img src="{{ asset('images/menu-icon-pontos.png') }}">
      de 2.001 a 5.000
    </a>
  </li>
  <li>
    <a href="{{ url('exchange/min/5001/max/10000') }}">
      <img src="{{ asset('images/menu-icon-pontos.png') }}">
      de 5.001 a 10.000
    </a>
  </li>
  <li>
    <a href="{{ url('exchange/min/10000/max/0') }}">
      <img src="{{ asset('images/menu-icon-pontos.png') }}">
      + de 10.000
    </a>
  </li>
</ul>
