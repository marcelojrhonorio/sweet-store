<div class="modal fade" tabindex="-1" role="dialog" data-list-vip-modal>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Entre agora mesmo para a Lista Vip da Sweet!
        </h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Por favor, informe seu nome completo e seu celular:</p>

        <div class="alert alert-success alert-dismissible fade show sr-only" data-list-vip-alert>
          Obrigado! Envie-nos uma mensagem e siga os passos para entrar na Lista Vip da Sweet.
        </div>

        <div class="alert alert-danger alert-dismissible fade show sr-only" data-list-vip-alert-danger>
          Por favor, preencha todos os campos.
        </div>

        <form>
          <div class="form-group" data-form-name>            
            <input
              id="name"
              class="form-control"
              name="name"
              value=""
              data-input-name
              type="name"
              placeholder="Nome completo"              
            >
          </div>
          <div class="form-group" data-form-phone>            
            <input
              id="phone"
              class="form-control"
              name="phone"
              value=""
              data-input-phone
              type="text"
              placeholder="(00)00000-0000"              
            >
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" type="button" btn-cancel-list-vip>
            Cancelar
        </button>
        <button class="btn btn-info" type="button" btn-send-list-vip>
            Confirmar
        </button>
      </div>
    </div>
  </div>
</div>
