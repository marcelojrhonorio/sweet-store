const UnsubscribeCustomer = {
  start () {
    this.token    = $('meta[name="csrf-token"]').attr('content');

    // Reasons checks.
    this.$lotsOfEmailCheck         = $('[data-form-lots-of-emails]');
    this.$pointsOpportunitiesCheck = $('[data-form-points-opportunities]');
    this.$technicalProblemsCheck   = $('[data-from-technical-problems]');
    this.$dissatisfiedCheck        = $('[data-from-dissatisfied]');
    this.$notRegisteredCheck       = $('[data-from-not-registered]');
    this.$notInterestedCheck       = $('[data-form-not-interested]');
    this.$otherCheck               = $('[data-form-other]');

    // Other reason text input.
    this.$otherReasonText    = $('[data-form-other-reason]');
    this.$otherReasonWrapper = $('[data-form-other-reason-wrapper]')

    // Decision radio.
    this.$deleteAccountRadio     = $('[data-form-delete-account]');
    this.$unsubscribeEmailsRadio = $('[data-form-unsubscribe-emails]');

    // Suggestion text input.
    this.$suggestionText    = $('[data-form-suggestion]');
    this.$suggestionWrapper = $('[data-form-suggestion-wrapper]');

    // Submit button.
    this.$btn = $('[data-btn-unsubscribe]');

    // Allert feedback.
    this.$alert = $('[data-form-alert]');

    this.bind();
  },

  bind () {
    this.$otherCheck.on('change', this.onOtherClick.bind(this));
    this.$deleteAccountRadio.on('change', this.onDeleteAccountClick.bind(this));
    this.$unsubscribeEmailsRadio.on('change', this.onUnsubscribeClick.bind(this));
    this.$btn.on('click', this.onFormSubmit.bind(this));
  },

  // Hide and show text inputs.
  onOtherClick (event) {
    event.preventDefault();
    if (this.$otherCheck.prop('checked')) {
      this.$otherReasonWrapper.removeClass('sr-only');
    } else {
      this.$otherReasonWrapper.addClass('sr-only');
      this.$otherReasonText.val('');
    }
  },

  onDeleteAccountClick (event) {
    event.preventDefault();
    this.$suggestionWrapper.removeClass('sr-only');
  },

  onUnsubscribeClick (event) {
    event.preventDefault();
    this.$suggestionWrapper.removeClass('sr-only');
  },

  // Submit form.
  onFormSubmit (event) {
    event.preventDefault();

    this.$alert.addClass('sr-only');
    
    // validate form.

    if (false == this.isValidReasons()) {
      this.$alert.removeClass('sr-only');
      this.$alert.text('Você deve informar um motivo para prosseguir.');
      return;
    }

    if (this.$otherCheck.prop('checked') && '' == this.$otherReasonText.val()) {
      this.$alert.removeClass('sr-only');
      this.$alert.text('Digite um motivo para prosseguir.');
      this.$otherReasonText.focus();
      return;
    }

    const finalUnsubscribe = $("input[name='final-unsubscribe']:checked").val();

    if (finalUnsubscribe !== 'unsubscribe_emails' && finalUnsubscribe !== 'delete_account') {
      this.$alert.removeClass('sr-only');
      this.$alert.text('Nos informe o que você deseja fazer: '+
        'se desincrever da nossa lista de e-mails ou excluir sua conta.');
      return;
    }

    const values = this.getValues();
    const text   = this.getTexts(values.final_option);

    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-info',
        cancelButton: 'btn btn-danger',
      },
      buttonsStyling: false,
    })
    
    swalWithBootstrapButtons.fire({
      title: 'Você tem certeza?',
      html: text.before,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, confirmar!',
      cancelButtonText: 'Não, cancelar!',
      reverseButtons: true,
    }).then((result) => {
      if (result.value) {
        const customerUnsubscribed = $.ajax({
          method: 'POST',
          url: '/unsubscribe',
          contentType: 'application/json',
          data: JSON.stringify({
            _token: this.token,
            data  : values,
          }),
        });
        
        customerUnsubscribed.done((data) => {
          switch(data.status) {
            case 'already_unsubscribed':
                Swal.fire({
                  title: 'Descadastro já realizado',
                  type: 'info',
                  html: 'Você já selecionou essa opção anteriormente.',
                  showCancelButton: false,
                  focusConfirm: false,
                  confirmButtonColor: '#33a3b7',
                  confirmButtonText:
                  '<i class="fa fa-thumbs-up"></i> Ok!',
                  confirmButtonAriaLabel: '!',
                });
              break;

            case 'unkdown_error':
                Swal.fire({
                  type: 'error',
                  title: 'Oops...',
                  text: 'Algo errado aconteceu!',
                  confirmButtonColor: '#33a3b7',
                  confirmButtonText:
                  '<i class="fa fa-thumbs-up"></i> Ok!',
                });
              break;

            case 'success':
                swalWithBootstrapButtons.fire(
                  'Sucesso!',
                  text.after,
                  'success'
                ).then((result) => {
                  if ('delete_account' === values.final_option) {
                    window.location.href = "/";
                  }
                });
              break;
          }
        });
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          'Cancelado',
          'Você escolheu cancelar :)',
          'error'
        )
      }
    })

  },

  isValidReasons () {
    if (this.$lotsOfEmailCheck.prop('checked')         == false &&
        this.$pointsOpportunitiesCheck.prop('checked') == false &&
        this.$technicalProblemsCheck.prop('checked')   == false && 
        this.$dissatisfiedCheck.prop('checked')        == false &&
        this.$notRegisteredCheck.prop('checked')       == false &&
        this.$notInterestedCheck.prop('checked')       == false &&
        this.$otherCheck.prop('checked')               == false
        ) {
          return false;
        }
        return true;
  },

  getValues () {
    return {
      another_reason_description  : this.$otherReasonText.val(),
      final_option                : $("input[name='final-unsubscribe']:checked").val(),
      suggestion                  : this.$suggestionText.val(),
      reasons                     : this.getReasonsUnsubscribe(),
    };
  },

  getReasonsUnsubscribe () {
    var reasons = '';

    if (this.$lotsOfEmailCheck.prop('checked')) {
      reasons += '|' + this.$lotsOfEmailCheck.val();
    }
      
    if (this.$pointsOpportunitiesCheck.prop('checked')) {
      reasons += '|' + this.$pointsOpportunitiesCheck.val();
    }

    if (this.$technicalProblemsCheck.prop('checked')) {
      reasons += '|' + this.$technicalProblemsCheck.val();
    }

    if (this.$dissatisfiedCheck.prop('checked')) {
      reasons += '|' + this.$dissatisfiedCheck.val();
    }

    if (this.$notRegisteredCheck.prop('checked')) {
      reasons += '|' + this.$notRegisteredCheck.val();
    }

    if (this.$notInterestedCheck.prop('checked')) {
      reasons += '|' + this.$notInterestedCheck.val();
    }

    if (this.$otherCheck.prop('checked')) {
      reasons += '|' + this.$otherCheck.val();
    }

    reasons += '|';

    return reasons;
  },

  getTexts (finalOption) {
    const 
      beforeUnsubscribe   = 'Você deixará de receber nossos e-mails.',
      beforeDeleteAccount = 'Está prestes à não ter mais acesso ao nosso portal.<br><br>' + 
      'Você poderá voltar a qualquer momento enviando um email para nossa equipe. Entretanto, <strong>seus pontos serão zerados</strong>.',
      afterUnsubscribe    = ' Em até 7 você não receberá nossos e-mails.',
      afterDeleteAccount  = 
        'Sua conta foi excluída som sucesso! Em até 7 dias você deixará de receber nossos e-mails. <br>' +
        'Encerrando sessão...';

    return {
      before : (('unsubscribe_emails' == finalOption) ? beforeUnsubscribe : beforeDeleteAccount),
      after  : (('unsubscribe_emails' == finalOption) ? afterUnsubscribe  : afterDeleteAccount),
    }
  },
}

const Unsubscribe  = () => {
  UnsubscribeCustomer.start();
}

export default Unsubscribe;