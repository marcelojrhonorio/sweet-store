
<input type="hidden" id="points"   name="points"   value="{{ session('points') }}" data-customer-points>
<input type="hidden" id="customer" name="customer" value="{{ session('id') }}" data-customer-id>
<input type="hidden" id="enabled"  name="enabled"  value="{{ env('EXCHANGE_POINTS')}}" data-feature-enable>
<input type="hidden" id="verify-enabled"  name="verify-enabled"  value="{{ env('VERIFY_STATUS_EXCHANGE')}}" feature-verify-enable>

<div class="modal fade" tabindex="-1" role="dialog" data-modal-exchange>
  <div class="modal-dialog" role="document">
    <div class="modal-content" data-content-enought>
        @include('partials.exchange-points.include-enought-points')
    </div>
    <div class="modal-content" data-content-insufficient>
        @include('partials.exchange-points.include-insufficient-points')
    </div>
    <div class="modal-content" data-content-insufficient-stamps>
        @include('partials.exchange-points.include-insufficient-stamps')
    </div>
    <div class="modal-content" data-content-social-network>
        @include('partials.exchange-points.include-social-network')
    </div>
    <iv class="modal-content" data-content-last-exchange>
        @include('partials.exchange-points.last-exchange-in-progress')
    </div>
  </div>
</div>
