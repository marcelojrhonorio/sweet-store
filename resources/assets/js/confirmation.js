const ActionEmail = {
  start() {
    this.updating = false
    this.token    = $('meta[name="csrf-token"]').attr('content')

    this.ui                  = {}
    this.ui.$modal           = $('[data-confirmation-modal]')
    this.ui.$form            = this.ui.$modal.find('[data-form-change-email]')
    this.ui.$alert           = this.ui.$modal.find('[data-confirmation-alert]')
    this.ui.$alertError      = this.ui.$modal.find('[data-confirmation-alert-danger]')
    this.ui.$input           = this.ui.$modal.find('[data-input-email]')
    this.ui.$inputHidden     = this.ui.$modal.find('[data-input-usr_email]')
   
    this.ui.$btnUpdate       = this.ui.$modal.find('[data-btn-email-update]')
    this.ui.$btnResent       = this.ui.$modal.find('[data-btn-email-resent]')
    this.ui.$btnConfirmation = $('[data-trigger-modal-email]')

    this.labels         = {}
    this.labels.update  = 'Alterar e-mail cadastrado'
    this.labels.resent  = 'Reenviar'
    this.labels.loader  = 'Carregando...'
    this.labels.confirm = 'Salvar'

    if (this.ui.$btnConfirmation.length < 1) {
      return
    }

    this.bind()
  },

  bind() {
    this.ui.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
    this.ui.$btnUpdate.on('click', this.onUpdateClick.bind(this))
    this.ui.$btnResent.on('click', this.onResentClick.bind(this))
    this.ui.$btnConfirmation.on('click', this.onConfirmClick.bind(this))
  },

  onHiddenModal(event) {
    this.ui.$alert.addClass('sr-only')
    this.ui.$alertError.addClass('sr-only')
    this.ui.$input.prop('disabled', true)
    this.ui.$input.val(this.ui.$inputHidden.val())
    this.ui.$btnUpdate.prop('disabled', false).text(this.labels.update)
    this.ui.$btnResent.prop('disabled', false).text(this.labels.resent)
  },

  onConfirmClick(event) {
    event.preventDefault()

    this.ui.$modal.modal('show')
  },

  onUpdateClick(event) {
    event.preventDefault()

    this.updating = !this.updating

    if (this.updating) {
      this.ui.$btnResent.prop('disabled', true)
      this.ui.$btnUpdate.text(this.labels.confirm)
      this.ui.$input.prop('disabled', false).focus()
      return
    }

    this.ui.$alert.addClass('sr-only')
    this.ui.$alertError.addClass('sr-only')
    this.ui.$input.prop('disabled', true)
    this.ui.$btnResent.prop('disabled', true)
    this.ui.$btnUpdate.prop('disabled', true).text(this.labels.loader)

    const saving = $.ajax({
      method: 'POST',
      url: '/profile/email',
      contentType: 'application/json',
      data: JSON.stringify({
        _method: 'PUT',
        _token: this.token,
        email: this.ui.$input.val()
      }),
    })

    saving.done((data) => {
      if (false === data.success) {        
        if (data.errors.invalid) {
          this.ui.$alertError.text(data.errors.invalid).removeClass('sr-only') 
        }  
        this.ui.$alertError.removeClass('sr-only')     
        this.ui.$input.prop('disabled', false).focus()
        this.ui.$btnUpdate.prop('disabled', false).text(this.labels.confirm)
        this.ui.$btnResent.prop('disabled', true).text(this.labels.resent)
        return
      }

      if(this.ui.$inputHidden.val() === this.ui.$input.val()) {
        this.ui.$alertError.text('Mesmo e-mail informado.').removeClass('sr-only') 
        this.ui.$input.prop('disabled', false).focus()
        this.ui.$btnUpdate.prop('disabled', false).text(this.labels.confirm)
        this.ui.$btnResent.prop('disabled', true).text(this.labels.resent)
        return
      }

      this.ui.$inputHidden.val(this.ui.$input.val())
      this.ui.$alert.text('Você usará este e-mail agora para acessar o portal.').removeClass('sr-only')
      this.ui.$btnUpdate.prop('disabled', false).text(this.labels.update)
      this.ui.$btnResent.prop('disabled', false)
    })

    saving.fail((error) => {
      console.log('Erro: ', error)
    })
  },

  onResentClick(event) {
    event.preventDefault()

    this.ui.$alert.addClass('sr-only')
    this.ui.$alertError.addClass('sr-only')
    this.ui.$btnUpdate.prop('disabled', true)
    this.ui.$btnResent.prop('disabled', true).text(this.labels.loader)

    const saving = $.ajax({
      method: 'POST',
      url: '/profile/email/resend',
      contentType: 'application/json',
      data: JSON.stringify({
        _token: this.token,
      }),
    })

    saving.done((data) => {
      if (false === data.success) {
        if (data.errors.invalid) {
          this.ui.$alertError.text(data.errors.invalid).removeClass('sr-only') 
        }
        this.ui.$alertError.text(data.errors.limit).removeClass('sr-only')                                          
        this.ui.$input.prop('disabled', false).focus()
        this.ui.$btnUpdate.prop('disabled', false)
        this.ui.$btnResent.prop('disabled', false).text(this.labels.resent)
        return
      }

      this.ui.$alert.text('Enviado! Por favor, verifique também no SPAM.').removeClass('sr-only')
      this.ui.$btnUpdate.prop('disabled', false)
      this.ui.$btnResent.prop('disabled', false).text(this.labels.resent)
    })

    saving.fail((error) => {
      console.log('Erro: ', error)
    })
  }
}

const Confirmation = () => {
  ActionEmail.start()
}

export default Confirmation
