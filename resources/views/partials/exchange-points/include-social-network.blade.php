<div class="modal-header">
  <h5 class="modal-title" data-title-exchange>
    Eba!!! Você tem pontos suficientes para trocar.
  </h5>
  <button class="close" type="button" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<div class="alert alert-success alert-dismissible fade show sr-only" data-confirmation-alert>
  Seus dados foram enviados para aprovação. <br> Se estiverem em conformidade, seu perfil aparecerá na Sweet dentro de alguns dias. Caso contrário seus pontos serão devolvidos para sua conta. Obrigado! ❤ 
</div>
<div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert-danger>
  Por favor, preencha corretamente os campos abaixo.
</div>
 <p data-form-instructions>
  Para a solicitação desta troca ser realizada são necessárias algumas informações. 
  Basta preencher corretamente os campos abaixo.
 </p>

 <input type="hidden" value='' data-item-id>
 <input type="hidden" value='' data-item-points>
 <input type="hidden" value='' data-submit-ok>
 <input name="path" type="hidden" value="" data-input-path>
 <input name="store-url" type="hidden" value="{{ env('STORE_URL') }}" data-store-url>

 <form class="form-horizontal" data-form-exchange-sm>
    <div class="row" data-sm-group>
      <div class="form-group col-md-12">
        Descreva de maneira resumida o conteúdo do seu perfil:
        <input
          id="subject"
          class="form-control"
          name="subject"
          type="text"
          placeholder="Ex.: finanças pessoais, maquiagem, educação financeira, etc."
          title="Ex.: finanças pessoais, maquiagem, educação financeira, etc."
          data-input-subject
        >
      </div>
      <div class="form-group col-md-12">
        Link do seu perfil:
        <input
          id="profile_link"
          class="form-control"
          name="profile_link"
          type="text"
          title="Link do perfil de sua rede social"
          data-input-profile-link
        >
      </div>  
      <div class="form-group col-md-12">
        Imagem:
        <div class="col-md-10" data-wrap-upload>
          <div class="fileinput fileinput-new" data-provides="fileinput" data-wrap-file>
              <span class="btn btn-default btn-file">
                <input
                  id="image"
                  name="image"
                  type="file"
                  accept="image/*"
                  value=""
                  data-input-image
                >
              </span>
              <span class="fileinput-filename"></span>
          </div>
          <div class="progress progress-bar-default hidden" data-upload-progress>
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
        <div class="col-md-10" data-wrap-preview></div>
      </div>       
    </div>
 </form>
</div>
<div class="modal-footer">
  <button class="btn btn-default" data-dismiss="modal" type="button" btn-cancel-sm>
    Cancelar
  </button>
  <button class="btn btn-info" type="button" btn-confirm-sm>
    Confirmar
  </button>
</div>