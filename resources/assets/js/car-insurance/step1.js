const Step1 = {
  start($modal = {}) {
    this.ui           = {}
    this.ui.$modal    = $modal
    this.ui.$inputCep = $('[data-insurance-input-cep]')
    this.ui.$btn      = $('[data-insurance-step-button]:eq(1)')

    this.token = $('meta[name="csrf-token"]').attr('content')

    this.bind().mask()
  },

  bind() {
    this.ui.$modal.on('save:step:one', this.onSaveStepOne.bind(this))
    this.ui.$inputCep.on('blur', this.onBlurInputCep.bind(this))
    this.ui.$btn.on('click', this.onClickBtn.bind(this))
    return this
  },

  mask() {
    this.ui.$inputCep.mask('00.000-000')
    return this
  },

  queryCep(value) {
    const cep = value.replace(/\D/g, '')

    this.ui.$btn.prop('disabled', true)

    if ('' === cep) {
      this.ui.$inputCep.addClass('is-invalid')
      return false
    }

    const pattern = /^[0-9]{8}$/

    if (false === pattern.test(cep)) {
      this.ui.$inputCep.addClass('is-invalid')
      return false
    }

    this.ui.$inputCep.prop('disabled', true)

    return $.getJSON(`https://viacep.com.br/ws/${cep}/json/`)
  },

  onBlurInputCep(event) {
    const fetching = this.queryCep(event.target.value)

    if (fetching) {
      fetching
        .done(this.onQueryCepSuccess.bind(this))
        .fail(this.onQueryCepFail.bind(this))
        .always(this.onQueryCepComplete.bind(this))
    }
  },

  onQueryCepSuccess(json) {
    if (json.erro) {
      this.ui.$inputCep.addClass('is-invalid')
      return false
    }

    this.ui.$inputCep.removeClass('is-invalid')
    this.ui.$btn.prop('disabled', false)
  },

  onQueryCepFail(xhr, status, error) {
    //
    console.log(`Request failed: ${status}, ${error}`)
  },

  onQueryCepComplete() {
    //
    this.ui.$inputCep.prop('disabled', false)
  },

  onClickBtn(event) {
    event.preventDefault()

    const fetching = this.queryCep(this.ui.$inputCep.val())

    if (fetching) {
      fetching
        .done((json) => {
          if (json.erro) {
            this.ui.$inputCep.addClass('is-invalid')
            return
          }

          this.city  = json.localidade
          this.state = json.uf

          this.ui.$inputCep.removeClass('is-invalid')
          this.ui.$modal.trigger('save:step:one')
        })
        .fail(this.onQueryCepFail.bind(this))
    }
  },

  onSaveStepOne(event) {
    const data = {
      _token: this.token,
      cep: $.trim(this.ui.$inputCep.val()),
      city: this.city,
      state: this.state,
    }

    const saving = $.ajax({
      url: '/researches/insurance/step-1',
      dataType: 'json',
      method: 'POST',
      data: data,
    })

    saving
      .done((json) => {
        if (false === json.success) {
          console.log('Erro ao salvar CEP.', json)
          return
        }

        this.ui.$modal.trigger('show:next:step')

        this.ui.$inputCep.prop('disabled', false)
        this.ui.$btn.prop('disabled', false)
      })
      .fail((xhr, status, error) => console.log(`Request failed: ${status}, ${error}`))
  },
}

export default Step1
