<div class="modal fade" tabindex="-1" role="dialog" data-confirmation-modal>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Você ainda não confirmou o seu e-mail!
        </h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Por favor verifique no SPAM. O e-mail cadastrado foi:</p>

        <div class="alert alert-success alert-dismissible fade show sr-only" data-confirmation-alert>
          Enviado! Por favor, verifique também no SPAM.
        </div>

        <input name="usr_email" type="hidden" value="{{ $user['email'] }}" data-input-usr_email>

        <div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert-danger>
          Por favor, preencha corretamente o campo e-mail.
        </div>

        <form>
          <div class="form-group" data-form-change-email>
            <label for="customer-email" class="sr-only">
              E-mail cadastrado
            </label>
            <input
              id="customer-email"
              class="form-control"
              name="customer-email"
              value="{{ $user['email'] }}"
              data-input-email
              type="email"
              disabled
            >
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-warning" type="button" data-btn-email-update>
          Alterar e-mail cadastrado
        </button>
        <button class="btn btn-success" type="button" data-btn-email-resent>
          Reenviar
        </button>
      </div>
    </div>
  </div>
</div>
