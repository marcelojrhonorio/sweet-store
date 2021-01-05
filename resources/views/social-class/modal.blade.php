<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-modal-social>
  <div class="modal-dialog" role="document">
    <div class="modal-content animated bounceInRight">
      <form class="form-horizontal" action="post" enctype="multipart/form-data" data-form-relationship>
        
        <div class="modal-header" style="color: white; background-color: #5ec6c6;">
          <h5 class="modal-title">
            <i class="fas fa-question-circle"></i> <span>Pesquisa sobre seu perfil pessoal</span> 
          </h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <p class="master-title" style="font-size: 12px;"> Por favor, responda corretamente a pergunta abaixo:</p>

          {{-- Forma alert feedback --}}
          <div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert-danger>
            Por favor, preencha corretamente os campos abaixo.
          </div>

          {{-- Loader --}}
          <div class="spinner-border text-info" role="status" data-social-loader>
            <span class="sr-only">Loading...</span>
          </div>

          {{-- Render current question here --}}
          <div class="current-question" data-social-current-question></div>
        </div>

        {{-- Progress bar --}}
        {{-- <div class="progress-bar" style="width:70%;height:3px;background-color: #58e512"></div> --}}

        <div class="modal-footer">
          {{-- <button class="btn btn-info" type="button" style="background-color: #5ec6c6" data-btn-back>
            <i class="fas fa-chevron-circle-left"></i>
          </button> --}}
          <button class="btn btn-info" type="button" style="background-color: #5ec6c6" data-btn-next>
            <i class="fas fa-chevron-circle-right"></i>
          </button>          
        </div>

      </form>
    </div>
  </div>
</div>