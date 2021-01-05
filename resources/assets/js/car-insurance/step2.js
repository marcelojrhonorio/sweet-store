const Step2 = {
  start($modal = {}) {
    this.ui = {}

    this.ui.$modal = $modal

    this.ui.$inputHasCar = $('[data-insurance-has-car]')

    this.ui.$wrapMake  = $('[data-wrap-make]')
    this.ui.$inputMake = $('[data-insurance-make]')

    this.ui.$wrapModel  = $('[data-wrap-model]')
    this.ui.$inputModel = $('[data-insurance-model]')

    this.ui.$wrapYear  = $('[data-wrap-year]')
    this.ui.$inputYear = $('[data-insurance-year]')

    this.ui.$wrapHasInsurance  = $('[data-wrap-has-insurance]')
    this.ui.$inputHasInsurance = $('[data-insurance-has-insurance]')

    this.ui.$wrapDateInsurance = $('[data-wrap-date-insurance]')
    this.ui.$inputDateInsurance = $('[data-date-insurance]')

    this.ui.$wrapInsurer  = $('[data-wrap-insurer]')
    this.ui.$inputInsurer = $('[data-insurance-insurer]')

    this.ui.$btn = $('[data-insurance-step-button]:eq(2)')

    this.token = $('meta[name="csrf-token"]').attr('content')
    this.sweetApiUrl = `${$('[data-sweet-api-url]').val()}/api/seguroauto/v1/frontend`

    this.ui.$wrapMake.hide()
    this.ui.$wrapModel.hide()
    this.ui.$wrapYear.hide()
    this.ui.$wrapHasInsurance.hide()
    this.ui.$wrapDateInsurance.hide()
    this.ui.$wrapInsurer.hide()

    this.bind().mask().customSelect()
  },

  bind() {
    this.ui.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
    this.ui.$inputHasCar.on('change', this.onChangeInputHasCar.bind(this))
    this.ui.$inputMake.on('change', this.onChangeInputMake.bind(this))
    this.ui.$inputModel.on('change', this.onChangeInputModel.bind(this))
    this.ui.$inputYear.on('change', this.onChangeInputYear.bind(this))
    this.ui.$inputHasInsurance.on('change', this.onChangeHasInsurance.bind(this))
    this.ui.$inputInsurer.on('change', this.onChangeInputInsurer.bind(this))
    this.ui.$btn.on('click', this.onClickBtn.bind(this))

    return this
  },

  mask() {
    this.ui.$inputDateInsurance.mask('00/0000')
    return this
  },

  customSelect() {
    const options = {
      language: 'pt-BR',
      width: '100%',
      theme: 'default',
      minimumInputLength: 3,
      dropdownParent: this.ui.$modal,
      ajax: {
        delay: 250,
        dataType: 'json',
      },
    }

    const optionsMake = $.extend(true, {}, options, {
      placeholder: 'Selecione a marca...',
      ajax: {
        url: (params) => {
          return `${this.sweetApiUrl}/brands?limit=50&like=brand_name,${params.term}`
        },
        processResults(json) {
          return {
            results: json.data.map(brand => ({ id: brand.id, text: brand.brand_name }))
          }
        },
      },
    })

    const optionsModel = $.extend(true, {}, options, {
      placeholder: 'Selecione o modelo...',
      ajax: {
        url: (params) => {
          const brandId = this.ui.$inputMake.val()

          return `${this.sweetApiUrl}/vehicle-models?where[vehicle_type_id]=1&where[brand_id]=${brandId}&like=vehicle_model_name,${params.term}&limit=100`
        },
        processResults(json) {
          return {
            results: json.data.map(model => {
              const years = model.years.map(year => year.year_description)
              const first = Math.min.apply(Math, years)
              const last  = Math.max.apply(Math, years)

              return {
                id: model.id,
                text: `${model.vehicle_model_name}: ${first} - ${last}`
              }
            })
          }
        },
      },
    })

    const optionsYear = $.extend(true, {}, options, {
      placeholder: 'Selecione o ano...',
      minimumInputLength: 0,
      ajax: {
        url: (params) => {
          const modelId = this.ui.$inputModel.val()

          return `${this.sweetApiUrl}/model-years?where[vehicle_model_id]=${modelId}&limit=-1`
        },
        processResults(json) {
          return {
            results: json.data.map(year => ({ id: year.id, text: year.year.year_description }))
          }
        },
      },
    })

    const optionsInsurer = $.extend(true, {}, options, {
      placeholder: 'Selecione a seguradora...',
      ajax: {
        url: (params) => {
          return `${this.sweetApiUrl}/insurance-companys?like=insurance_company_name,${params.term}&limit=50`
        },
        processResults(json) {
          return {
            results: json.data.map(company => ({ id: company.id, text: company.insurance_company_name }))
          }
        },
      },
    })

    this.ui.$inputMake.select2(optionsMake)

    this.ui.$inputModel.select2(optionsModel)

    this.ui.$inputYear.select2(optionsYear)

    this.ui.$inputInsurer.select2(optionsInsurer)

    return this
  },

  onHiddenModal() {
    this.ui.$wrapInsurer.fadeOut()
    this.ui.$inputInsurer.val(null).trigger('change.select2')

    this.ui.$wrapDateInsurance.fadeOut()
    this.ui.$inputDateInsurance.val('')

    this.ui.$wrapHasInsurance.fadeOut()
    this.ui.$inputHasInsurance.prop('checked', false)

    this.ui.$wrapYear.fadeOut()
    this.ui.$inputYear.val(null).trigger('change.select2')

    this.ui.$wrapModel.fadeOut()
    this.ui.$inputModel.val(null).trigger('change.select2')

    this.ui.$wrapMake.fadeOut()
    this.ui.$inputMake.val(null).trigger('change.select2')

    this.ui.$inputHasCar.prop('checked', false)
  },

  onChangeInputHasCar(event) {
    const hasCar = event.target.value

    if ('0' === hasCar) {
      this.ui.$wrapInsurer.fadeOut()
      this.ui.$inputInsurer.val(null).trigger('change.select2')

      this.ui.$wrapDateInsurance.fadeOut()
      this.ui.$inputDateInsurance.val('')

      this.ui.$wrapHasInsurance.fadeOut()
      this.ui.$inputHasInsurance.prop('checked', false)

      this.ui.$wrapYear.fadeOut()
      this.ui.$inputYear.val(null).trigger('change.select2')

      this.ui.$wrapModel.fadeOut()
      this.ui.$inputModel.val(null).trigger('change.select2')

      this.ui.$wrapMake.fadeOut()
      this.ui.$inputMake.val(null).trigger('change.select2')

      this.ui.$btn.prop('disabled', false)

      return
    }

    this.ui.$btn.prop('disabled', true)

    this.ui.$wrapMake.fadeIn()
  },

  onChangeInputMake(event) {
    this.ui.$wrapInsurer.fadeOut()
    this.ui.$inputInsurer.val(null).trigger('change.select2')

    this.ui.$wrapDateInsurance.fadeOut()
    this.ui.$inputDateInsurance.val('')

    this.ui.$wrapHasInsurance.fadeOut()
    this.ui.$inputHasInsurance.prop('checked', false)

    this.ui.$wrapYear.fadeOut()
    this.ui.$inputYear.val(null).trigger('change.select2')

    this.ui.$wrapModel.fadeOut()
    this.ui.$inputModel.val(null).trigger('change.select2')

    this.ui.$wrapModel.fadeIn();
  },

  onChangeInputModel(event) {
    this.ui.$wrapInsurer.fadeOut()
    this.ui.$inputInsurer.val(null).trigger('change.select2')

    this.ui.$wrapDateInsurance.fadeOut()
    this.ui.$inputDateInsurance.val('')

    this.ui.$wrapHasInsurance.fadeOut()
    this.ui.$inputHasInsurance.prop('checked', false)

    this.ui.$wrapYear.fadeOut()
    this.ui.$inputYear.val(null).trigger('change.select2')

    this.ui.$wrapYear.fadeIn()
  },

  onChangeInputYear(event) {
    //
    this.ui.$wrapHasInsurance.fadeIn()
  },

  onChangeHasInsurance(event) {
    const hasInsurance = event.target.value

    if ('0' === hasInsurance) {
      this.ui.$wrapInsurer.fadeOut()
      this.ui.$inputInsurer.val(null).trigger('change.select2')

      this.ui.$wrapDateInsurance.fadeOut()
      this.ui.$inputDateInsurance.val('')

      this.ui.$btn.prop('disabled', false)

      return
    }

    this.ui.$btn.prop('disabled', true)

    this.ui.$wrapDateInsurance.fadeIn()
    this.ui.$wrapInsurer.fadeIn()
  },

  onChangeInputInsurer(event) {
    const date    = $.trim(this.ui.$inputDateInsurance.val())
    const company = $.trim(this.ui.$inputInsurer.val())

    if (7 !== date.length || '' === company) {
      this.ui.$btn.prop('disabled', true)
      return
    }

    this.ui.$btn.prop('disabled', false)
  },

  onClickBtn(event) {
    event.preventDefault()
    this.ui.$btn.prop('disabled', true)
    const hasCar = $.trim($('[data-insurance-has-car]:checked').val())

    const data = {
      _token: this.token,
      hasCar: hasCar,
      year: $.trim($('[data-insurance-year]').val()),
      hasInsurance: $.trim($('[data-insurance-has-insurance]:checked').val()),
      dateInsurance: $.trim($('[data-date-insurance]').val()).replace(/\//, ''),
      insurer: $.trim($('[data-insurance-insurer]').val()),
    }

    const saving = $.ajax({
      url: '/researches/insurance/step-2',
      dataType: 'json',
      method: 'POST',
      data: data,
    })
    
    saving
      .done((json) => {
        if (false === json.success) {
          console.log('Erro ao salvar Step 2.', json)
          this.ui.$btn.prop('disabled', false)
          return
        }

        this.ui.$modal.trigger('show:next:step')
      })
      .fail((xhr, status, error) => console.log(`Request failed: ${status}, ${error}`))
  },
}

export default Step2
