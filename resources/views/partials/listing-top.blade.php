@if(env('MEMBER_GET_MEMBER'))
  @include('partials.share')
@else
  @include('partials.listing-top-title')
@endif
