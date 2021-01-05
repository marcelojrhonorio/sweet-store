<div class="modal fade" tabindex="-1" role="dialog" data-submit-proof-modal>
  <div class="modal-dialog responsive-width-modal" role="document">
    <div class="modal-content">

      <div class="modal-header" style="background-color:#5ec6c6;color:#fff">
        <h6 class="modal-title" title-conditions>
        <i class="far fa-id-card"></i>
          ENVIAR COMPROVAÇÕES DE ENCAMINHAMENTOS
        </h6>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p style='text-align: justify; font-size: 13px;' data-submit-proof-conditions>
         Enviar provas de encaminhamento:
        </p>

        <div class="alert alert-success alert-dismissible fade show sr-only" data-submit-proof-alert>
        </div>  
        
        <div class="alert alert-danger alert-dismissible fade show sr-only" data-submit-proof-alert-danger>
        </div>

        <input name="path" type="hidden" value="" data-input-path-proof>

        <div class="form-group bg-muted p-h-md" id="config-inputs-forwarding"> 
          <input type="text" id="name_person" name="name_person[]" class="input-sm form-control name-person" placeholder="Nome" data-name-person />
          <input type="text" id="email_person" name="email_person[]" class="input-sm form-control email-person" placeholder="E-mail" data-email-person />
          <a title="Adicionar novo campo" class="btn add-inputs-config custom-btn-add"><span><i class="fa fa-plus-circle icon-add-inputs" aria-hidden="true"></i></span></a>
        </div>

        <form class="form-horizontal" data-form-submit-proof>        
            <div class="form-group col-md-12" send-images>
              Envio de prints:
              <div class="col-md-10" data-wrap-upload-proof>
                <div class="fileinput fileinput-new" data-provides="fileinput" data-wrap-file-proof>
                    <span class="btn btn-default btn-file custom-input-file">
                      <input
                        id="image"
                        name="image[]"
                        type="file"
                        accept="image/*"
                        value=""
                        multiple=""
                        data-input-image-proof
                      >
                    </span>
                    <span class="fileinput-filename"></span>
                </div>
                <div class="progress progress-bar-default hidden" data-upload-progress-proof>
                  <div
                    class="progress-bar"
                    style="width: 0%"
                    role="progressbar"
                    aria-valuemax="100"
                    aria-valuemin="0"
                    aria-valuenow="0"
                  >
                  </div>
                </div>      
              </div> 
            </div>
          <div class="col-md-10" data-wrap-preview-proof></div>        
          <div class="col-md-12 alert alert-success alert-dismissible fade show sr-only" data-msg-upload-proof></div>
        </form>
      </div> 

      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" type="button" btn-cancel-submit-proof>
            Cancelar
        </button>
        <button class="btn btn-info sr-only" title="Menu Principal" type="button" style="background-color: #5ec6c6" btn-how-home>
            <i class="fas fa-home" icon-home></i>
        </button>
        <button class="btn btn-info" type="button" btn-send-submit-proof>
            Confirmar
        </button>     
      </div>

    </div>
  </div>
</div>
