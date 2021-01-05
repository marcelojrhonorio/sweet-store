const ForwardingEmail = {
    start() {
        this.token    = $('meta[name="csrf-token"]').attr('content');
        
        //Home 
        this.$modal                   = $('[data-email-forwarding-modal]');
        this.$alert                   = this.$modal.find('[data-email-forwarding-alert]');
        this.$alertError              = this.$modal.find('[data-email-forwarding-alert-danger]');

        //Submit Proof
        this.$modalProof              = $('[data-submit-proof-modal]');
        this.$alertProof              = this.$modalProof.find('[data-submit-proof-alert]');
        this.$alertErrorProof         = this.$modalProof.find('[data-submit-proof-alert-danger]');
        this.$submitProof             = this.$modalProof.find('[btn-send-submit-proof]');
        this.$formProof               = $('[data-form-submit-proof]');
        this.$addInput                = $('.add-inputs-config');

        //Upload image
        this.$imageProof              = $('[data-input-image-proof]');
        this.$progressProof           = this.$modalProof.find('[data-upload-progress-proof]');  
        this.$wrapUploadProof         = this.$modalProof.find('[data-wrap-upload-proof]');
        this.$wrapFileProof           = this.$modalProof.find('[data-wrap-file-proof]');
        this.$wrapPreviewProof        = this.$modalProof.find('[data-wrap-preview-proof]');
        this.$msgUploadProof          = this.$modalProof.find('[data-msg-upload-proof]');
        this.$pathProof               = this.$modalProof.find('[data-input-path-proof]');

        //Send Email
        this.$modalSendEmail          = $('[data-send-email-modal]');
        this.$alertSendEmail          = this.$modalSendEmail.find('[data-send-email-alert]');
        this.$alertErrorSendEmail     = this.$modalSendEmail.find('[data-send-email-alert-danger]');
        this.$btnConfirmSendEmail     = this.$modalSendEmail.find('[btn-confirm-send-email]');

        this.$bntHome                 = $('[btn-how-home]');               

        //How it works
        this.$modalHowItWorks         = $('[data-how-it-works-modal]');
        this.$alertHowItWorks         = this.$modalHowItWorks.find('[data-how-it-worksl-alert]');
        this.$alertErrorHowItWorks    = this.$modalHowItWorks.find('[data-how-it-works-alert-danger]'); 
        this.$bntStepOne              = this.$modalHowItWorks.find('[step-one]');               
        this.$bntStepTwo              = this.$modalHowItWorks.find('[step-two]');               
        this.$bntStepThree            = this.$modalHowItWorks.find('[step-three]');               
        this.$bntStepFour             = this.$modalHowItWorks.find('[step-four]');    
        this.$bntStepFive             = this.$modalHowItWorks.find('[step-five]');    
        this.$bntStepSix              = this.$modalHowItWorks.find('[step-six]');    
        this.$bntHowPrevious          = this.$modalHowItWorks.find('[btn-how-previous]');    
        this.$bntHowNext              = this.$modalHowItWorks.find('[btn-how-next]');    
        this.$indexStep               = 0;                 
        
        this.$btnEmailForwarding      = $('[data-email-forwarding]'); 
        this.$btnHowItWorks           = $('[btn-how-it-works]'); 
        this.$btnSendEmailForwarding  = $('[btn-send-email-forwarding]'); 
        this.$btnSubmitProof          = $('[btn-submit-proof]'); 
      
        this.bind()
    },

    bind() {
        this.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
        this.$modalProof.on('hidden.bs.modal', this.onHiddenModalProof.bind(this))
        this.$modalSendEmail.on('hidden.bs.modal', this.onHiddenModalSendEmail.bind(this))
        this.$modalHowItWorks.on('hidden.bs.modal', this.onHiddenModalHowItWorks.bind(this))

        this.$btnEmailForwarding.on('click', this.onEmailForwardingClick.bind(this))

        this.$btnHowItWorks.on('click', this.onHowItWorksClick.bind(this))
        this.$bntHowPrevious.on('click', this.onHowPreviousClick.bind(this))
        this.$bntHowNext.on('click', this.onHowNextClick.bind(this))

        this.$btnSendEmailForwarding.on('click', this.onSendEmailForwardinClick.bind(this))
        this.$btnConfirmSendEmail.on('click', this.onConfirmSendEmailClick.bind(this))

        this.$btnSubmitProof.on('click', this.onBtnSubmitProofClick.bind(this))
        this.$submitProof.on('click', this.onSubmitProofClick.bind(this))
        this.$addInput.on('click', this.onAddInputClick.bind(this)) 

        this.$imageProof.on('change', $.proxy(this.onImageProofChange, this));
        this.$formProof.on('click', '[data-destroy-image-proof]', $.proxy(this.onDestroyImageClick, this));

        this.$bntHome.on('click', this.onHomeClick.bind(this))

    },

    onImageProofChange: function(event) {
        event.preventDefault();

        if ('' === event.target.value) {
          console.log('não vai upar');
          return;
        }
    
        this.$progressProof.removeClass('hidden');
    
        const token = $('meta[name="csrf-token"]').attr('content');
    
        const headers = {
          'X-CSRF-TOKEN': token,
        };
    
        const data = new FormData(this.$formProof[0]);
    
        const handleProgress = function() {
          const xhr = $.ajaxSettings.xhr();
    
          if (xhr.upload) {
            xhr.upload.addEventListener('progress', function(event) {
              if (event.lengthComputable) {
                const percentage = Math.round((event.loaded * 100) / event.total);
    
                $('.progress-bar').attr({
                  'aria-valuenow': percentage,
                  'style'        : `width: ${percentage}%`,
                });
              }
            }, false);
    
            xhr.upload.addEventListener('load', function(e) {
              $('.progress-bar').attr({
                'aria-valuenow': '100',
                'style'        : 'width: 100%',
              });
            }, false);
    
            xhr.upload.addEventListener('loadend', function(e) {
              $('.progress-bar').attr({
                'aria-valuenow': '100',
                'style'        : 'width: 100%',
              });
    
              $('.progress').fadeOut(1000);
            }, false);
          }
    
          return xhr;
        };
    
        const uploading = $.ajax({
          cache      : false,
          dataType   : 'json',
          contentType: false,
          processData: false,
          method     : 'POST',
          url        : 'email-forwarding/submit-proof/upload', 
          headers    : headers,
          data       : data,
          xhr        : handleProgress,
        });
    
        uploading.done($.proxy(this.onImageUploadSuccess, this));
    
        uploading.fail($.proxy(this.onImageUploadFail, this));
      },

      onImageUploadSuccess: function(data) {
        var array = data.data;
        var pathImages = '';

        for (let index = 0; index < array.length; index++) {
          pathImages = pathImages + (array[index].path + array[index].name) + ',';          
        }
    
        this.$pathProof.val(pathImages);
        this.$progressProof.addClass('hidden');
        this.$wrapUploadProof.addClass('sr-only');

        this.$msgUploadProof.removeClass('sr-only');
        $('[send-images]').addClass('sr-only');
        this.$msgUploadProof.text('Imagens carregadas com sucesso.');

        this.$wrapPreviewProof
            .html(`
              <div class="col-md-9">
                <button class="btn btn-danger" style="margin-bottom:7%;" type="button" data-path="${pathImages}" data-destroy-image-proof>
                  Excluir imagens carregadas
                </button>
              </div>
            `)
            .removeClass('sr-only');

      },
    
      onImageUploadFail: function(error) {
        console.log(error);
      },
    
      onDestroyImageClick: function(event) {
        event.preventDefault();
    
        this.$wrapPreviewProof.children("div").remove();
    
        this.$imageProof.val('');
        this.$pathProof.val('');
        this.$wrapPreviewProof.addClass('sr-only');
        this.$msgUploadProof.addClass('sr-only');

        $('[send-images').removeClass('sr-only');
        this.$wrapUploadProof.removeClass('sr-only');
      },

    onHowPreviousClick(event) {
        event.preventDefault()
        
        switch (this.$indexStep) {
            case 0:
                this.$bntStepOne.removeClass('sr-only');
                this.$bntStepTwo.addClass('sr-only');  
                this.$bntHowPrevious.addClass('sr-only'); //hide previous button
                break;

            case 1:
                this.$bntStepTwo.removeClass('sr-only');
                this.$bntStepThree.addClass('sr-only');
                this.$indexStep--; 
                break;

            case 2:
                this.$bntStepThree.removeClass('sr-only');
                this.$bntStepFour.addClass('sr-only');
                this.$indexStep--; 
                break;

            case 3:
                this.$bntStepFour.removeClass('sr-only');
                this.$bntStepFive.addClass('sr-only');
                this.$indexStep--; 
                break;

            case 4:
                this.$bntStepFive.removeClass('sr-only');
                this.$bntStepSix.addClass('sr-only');
                this.$bntHowNext.removeClass('sr-only'); //show next button
                this.$indexStep--; 
                break;
        
            default:
                this.$bntStepOne.removeClass('sr-only');                
                break;
        }
           
    },

    onHowNextClick(event) {
        event.preventDefault()

        switch (this.$indexStep) {
            case 0:
                this.$bntStepOne.addClass('sr-only');
                this.$bntStepTwo.removeClass('sr-only');
                this.$bntHowPrevious.removeClass('sr-only'); //show previous button
                this.$indexStep++; 
                break;

            case 1:
                this.$bntStepTwo.addClass('sr-only');
                this.$bntStepThree.removeClass('sr-only');
                this.$indexStep++; 
                break;

            case 2:
                this.$bntStepThree.addClass('sr-only');
                this.$bntStepFour.removeClass('sr-only');
                this.$indexStep++; 
                break;

            case 3:
                  this.$bntStepFour.addClass('sr-only');
                  this.$bntStepFive.removeClass('sr-only');
                  this.$indexStep++; 
                  break;
                
            case 4:
                  this.$bntStepFive.addClass('sr-only');
                  this.$bntStepSix.removeClass('sr-only');
                  this.$bntHowNext.addClass('sr-only'); //hide next button
                  break;
        
            default:            
                break;
        }
        
    },

    onConfirmSendEmailClick(event) {
        event.preventDefault()

        const token = $('meta[name="csrf-token"]').attr('content');
            
        const send = $.ajax({
            method: 'POST',
            url: '/email-forwarding/send-email',
            contentType: 'application/json',
            data: JSON.stringify({
              _token: token,
              dataType: 'json',
            }),
        })
      
        send.done($.proxy(this.onSendSuccess, this));
      
        send.fail($.proxy(this.onSendFail, this));

    },

    onSendSuccess: function(data) {
      if (data.success) {
        this.$alertSendEmail.removeClass('sr-only');
        this.$alertSendEmail.text('E-mail enviado. Por favor, confira sua caixa de entrada, "Spam" e na aba "Promoções". Você ainda tem ' + data.data + ' clique(s) disponíveis hoje.');
        this.$alertErrorSendEmail.addClass('sr-only');
      } else {
        this.$alertSendEmail.addClass('sr-only');
        this.$alertErrorSendEmail.removeClass('sr-only');
        this.$alertErrorSendEmail.text('Você atingiu o limite de 10 e-mails por dia. Por favor, aguarde até amanhã.');
      } 
  },

  onSendFail: function(error) {
      console.log('Failed to SEND email: ', error);
  },

    onSubmitProofClick(event) {
        event.preventDefault()

        var names = [];
        var emails = [];

        var flag = false;// verificar se campos 'name' e 'email' estão vazios. 

        $('input[name^="name_person"]').each(function(index) {
          if('' == $(this).val()) {
            flag = true;
          }          
          names[index] = $(this).val();
        }); 

        $('input[name^="email_person"]').each(function(index) {
          if('' == $(this).val()) {
            flag = true;
          }   
          emails[index] = $(this).val();
        }); 

        if(flag || '' == this.$pathProof.val()) {
          this.$alertErrorProof.removeClass('sr-only');
          this.$alertErrorProof.text('Atenção, todos os campos são obrigatórios.');
          return;
        }

        const token = $('meta[name="csrf-token"]').attr('content');
            
        const saving = $.ajax({
            method: 'POST',
            url: '/email-forwarding/',
            contentType: 'application/json',
            data: JSON.stringify({
              _token: token,
              names : names,
              emails: emails,
              prints: this.$pathProof.val(),
              dataType: 'json',
            }),
        })
      
        saving.done($.proxy(this.onCreateSuccess, this));
      
        saving.fail($.proxy(this.onCreateFail, this));
    },

    onCreateSuccess: function(data) {
        if (data.success) {
            this.$alertErrorProof.addClass('sr-only');
            this.$msgUploadProof.addClass('sr-only');// msg upload image ok

            this.$alertProof.removeClass('sr-only') ;
            this.$alertProof.text('Dados enviados com sucesso! Por favor, aguarde a verificação.') ;

            this.$wrapPreviewProof.addClass('sr-only') ;

            $('#config-inputs-forwarding').addClass('sr-only');
            $('[send-images]').addClass('sr-only');

            $('[btn-cancel-submit-proof]').addClass('sr-only');
            $('[btn-send-submit-proof]').addClass('sr-only');
            $('[btn-how-home]').removeClass('sr-only');

        } else {
            this.$alertErrorProof.removeClass('sr-only');
            this.$alertErrorProof.text('Atenção, você já encaminhou para o e-mail "' + data.data + '". Por favor, informe somente e-mails que ainda não tenha nos enviado. ')
        }
    },

    onCreateFail: function(error) {
        console.log('Failed to CREATE: ', error);
    },

    onEmailForwardingClick(event) {
        event.preventDefault()
            
        this.$modal.modal('show');
    },

    onHowItWorksClick(event) {
        event.preventDefault()
            
        $('[btn-how-home]').removeClass('sr-only');

        this.$modalHowItWorks.modal('show');
        this.$modal.modal('hide');
    },

    onSendEmailForwardinClick(event) {
        event.preventDefault()
            
        $('[btn-how-home]').removeClass('sr-only');

        this.$modalSendEmail.modal('show');
        this.$modal.modal('hide');
    },

    onAddInputClick(event) {
      event.preventDefault()

      var div = $('#config-inputs-forwarding');

      $('<div />').attr('class', 'additional').css({'padding-top':'4%'}).append(
         $('<input />').attr({'type':'text', 'id':'name_person', 'name':'name_person[]', 'placeholder': 'Nome', 'data-name-person': ''}).addClass('input-sm form-control name-person')
      ).append(
          $('<input />').attr({'type':'text', 'id':'email_person', 'name':'email_person[]', 'placeholder': 'E-mail', 'data-email-person': ''}).addClass('input-sm form-control email-person')               
      ).append(                
          $('<a />').attr({'class':'remove', 'title':'Remover campo'}).addClass('btn btn-danger btn-remove-input').append($('<span />').append($('<i />').attr('aria-hidden', 'true').addClass('fa fa-minus-circle icon-add-inputs'))))
      .appendTo(div);
    },
    
    onBtnSubmitProofClick(event) {
        event.preventDefault()
            
        $('#config-inputs-forwarding').removeClass('sr-only');        
        $('[send-images]').removeClass('sr-only');
        
        $('[btn-cancel-submit-proof]').removeClass('sr-only');
        $('[btn-send-submit-proof]').removeClass('sr-only');
        $('[btn-how-home]').addClass('sr-only');

        this.$modalProof.modal('show');
        this.$modal.modal('hide');

        $('#config-inputs-forwarding').on('click', '.remove', function () {
            $(this).parents('div.additional').remove();
            return false;
        });
    },

    onHomeClick(event) {
        event.preventDefault()

        this.startSteps(); //começa steps do zero

        this.$modalHowItWorks.modal('hide');
        this.$modalSendEmail.modal('hide');
        this.$modalProof.modal('hide');
        this.$modal.modal('show');
    },

    onHiddenModal(event) { 
        
    },

    onHiddenModalProof(event) {
        this.$modal.modal('show');

        //Remover divs adicionais
        $('#config-inputs-forwarding').children('.additional').remove();
        $('[data-name-person]').val('');
        $('[data-email-person]').val('');

        this.$imageProof.val('');
        this.$alertProof.addClass('sr-only');
        this.$alertErrorProof.addClass('sr-only');
        this.$msgUploadProof.addClass('sr-only');// msg upload image ok
        this.$wrapUploadProof.removeClass('sr-only');
        this.$wrapPreviewProof.addClass('sr-only');

        this.$pathProof.val('');
    },

    onHiddenModalSendEmail(event) {
        this.$modal.modal('show');

        this.$alertSendEmail.addClass('sr-only');
        this.$alertErrorSendEmail.addClass('sr-only');
    },
    
    onHiddenModalHowItWorks(event) {
        this.startSteps(); //começa steps do zero
        this.$modal.modal('show');
    },

    startSteps() {
        this.$bntStepOne.removeClass('sr-only');
        this.$bntStepTwo.addClass('sr-only');
        this.$bntStepThree.addClass('sr-only');
        this.$bntStepFour.addClass('sr-only');
        this.$bntStepFive.addClass('sr-only');
        this.$bntStepSix.addClass('sr-only');

        this.$indexStep = 0; //começa steps do zero
        this.$bntHowPrevious.addClass('sr-only'); //hide previous button
        this.$bntHowNext.removeClass('sr-only'); //show next button
    },

}

const EmailForwarding = () => {
    ForwardingEmail.start()
}
    
export default EmailForwarding;