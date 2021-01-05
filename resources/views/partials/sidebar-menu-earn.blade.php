@if (count($categories))
  <ul class="list-unstyled">
    <li>
      <span class="title">
        <img src="{{ asset('images/menu-icon-ganharpontos.png') }}">
        Ganhar pontos
      </span>
    </li>
    @foreach ($categories as $category)
      <li>
        <a href="{{ url('earn/' . $category->id ) }}">
          <img src="{{ asset($category->icon) }}">
          {{ $category->name }}

          @if($category->news)
            <p><span class="tag-new">Novo!</span></p>
          @endif
        </a>
      </li>
    @endforeach
  </ul>
@endif
