<div class="modal fade" role="dialog" data-modal-car-insurance>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          Pesquisa Seguro Auto
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('car-insurance.progress')

        @include('car-insurance.step-0')

        @include('car-insurance.step-1')

        @include('car-insurance.step-2')

        @include('car-insurance.step-3')

        @include('car-insurance.step-4')
      </div>
      <div class="modal-footer">
        @include('car-insurance.modal-footer')
      </div>
    </div>
  </div>
</div>
