const Step3 = {
  start($modal = {}) {
    this.ui = {}

    this.ui.$modal = $modal

    this.ui.$inputCell  = $('[data-insurance-input-cell]')
    this.ui.$inputPhone = $('[data-insurance-input-phone]')
    this.ui.$inputCpf   = $('[data-insurance-input-cpf]')

    this.ui.$btn = $('[data-insurance-step-button]:eq(3)')

    this.token = $('meta[name="csrf-token"]').attr('content')

    this.bind().mask()
  },

  bind() {
    this.ui.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
    this.ui.$inputCpf.on('blur', this.validateFieldCpf.bind(this))
    this.ui.$btn.on('click', this.onClickBtn.bind(this))

    return this
  },

  mask() {
    const maskBehavior = function maskBehavior(input) {
      const value = input.replace(/\D/g, '')

      return value.length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'
    }

    const options = {
      onKeyPress: function onKeyPress(value, event, field, options) {
        field.mask(maskBehavior.apply({}, arguments), options);
      }
    }

    this.ui.$inputCell.mask(maskBehavior, options)

    this.ui.$inputPhone.mask(maskBehavior, options)

    this.ui.$inputCpf.mask('000.000.000-00')

    return this
  },

  onHiddenModal() {
    this.ui.$inputCell.removeClass('is-invalid').val('')
    this.ui.$inputPhone.removeClass('is-invalid').val('')
    this.ui.$inputCpf.removeClass('is-invalid').val('')
  },

  validateFieldCpf(event) {
    const value       = event.target.value
    const isValid     = this.isValidCpf(value)
    const addOrRemove = isValid ? 'removeClass' : 'addClass'

    this.ui.$inputCpf[addOrRemove]('is-invalid')
  },

  isValidCpf(input) {
    const number = input.toString().replace(/\.|-/g, '')

    const blackList = [
      '00000000000',
      '11111111111',
      '22222222222',
      '33333333333',
      '44444444444',
      '55555555555',
      '66666666666',
      '77777777777',
      '88888888888',
      '99999999999',
    ]

    if (-1 !== blackList.indexOf(number)) {
      return false;
    }

    let sum;
    let rest;

    sum = 0;

    for (let i = 1; i <= 9; i++) {
      sum = sum + parseInt(number.substring(i - 1, i)) * (11 - i);
    }

    rest = (sum * 10) % 11;

    if ((rest == 10) || (rest == 11)) {
      rest = 0;
    }

    if (rest != parseInt(number.substring(9, 10))) {
      return false;
    }

    sum = 0;

    for (let i = 1; i <= 10; i++) {
      sum = sum + parseInt(number.substring(i - 1, i)) * (12 - i);
    }

    rest = (sum * 10) % 11;

    if ((rest == 10) || (rest == 11)) {
      rest = 0;
    }

    if (rest != parseInt(number.substring(10, 11))) {
      return false;
    }

    return true;
  },

  onClickBtn(event) {
    event.preventDefault()
    
    this.ui.$btn.prop('disabled', true)

    const cell = $.trim(this.ui.$inputCell.val())

    const hasValidCell = 14 === cell.length || 15 === cell.length ? true : false
    const hasValidCpf  = this.isValidCpf(this.ui.$inputCpf.val())

    const cellRemoveOrAdd = hasValidCell ? 'removeClass' : 'addClass'
    const cpfRemoveOrAdd  = hasValidCpf ? 'removeClass' : 'addClass'

    const values = [
      hasValidCell,
      hasValidCpf,
    ]

    const hasInvalid = values.some((value) => false === value)

    this.ui.$inputCell[cellRemoveOrAdd]('is-invalid')

    this.ui.$inputCpf[cpfRemoveOrAdd]('is-invalid')

    if (hasInvalid) {
      return
    }

    const data = {
      _token: this.token,
      cpf: $.trim(this.ui.$inputCpf.val()).replace(/\D/g, ''),
      cell: $.trim(this.ui.$inputCell.val()).replace(/\s/g, ''),
      phone: $.trim(this.ui.$inputPhone.val()).replace(/\s/g, ''),
    }

    const saving = $.ajax({
      url: '/researches/insurance/step-3',
      dataType: 'json',
      method: 'POST',
      data: data,
    })

    saving
      .done((json) => {
        if (false === json.success) {
          if (json.errors) {
            let addOrRemove = json.errors.cell ? 'addClass' : 'removeClass'

            this.ui.$inputCell[addOrRemove]('is-invalid')

            addOrRemove = json.errors.phone ? 'addClass' : 'removeClass'

            this.ui.$inputPhone[addOrRemove]('is-invalid')

            addOrRemove = json.errors.cpf ? 'addClass' : 'removeClass'

            this.ui.$inputCpf[addOrRemove]('is-invalid')
          }

          return
        }

        const $points = $('[data-points-total]')

        $points.text(parseInt($points.text(), 10) + 100)

        $('[data-insurance-research-card]').remove()

        this.ui.$modal.trigger('show:next:step')
        this.ui.$btn.prop('disabled', false)
      })
      .fail((xhr, status, error) => console.log(`Request failed: ${status}, ${error}`))
      
      setTimeout(function(){el.prop('disabled', false); }, 3000)
  },
}

export default Step3
