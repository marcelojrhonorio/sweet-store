const ProfileCustomer = {

  start() {
      this.token                = $('meta[name="csrf-token"]').attr('content');

      this.$form                =  $('[data-form-profile-up]');
      this.$modal               =  $('[data-modal]');
      this.btnModal             =  $('[data-modal-confirm]');

      this.$fullname            =  $('[data-input-name-up]');
      this.$email               =  $('[data-input-email-up]');
      this.$birthdate           =  $('[data-input-birthdate-up]');
      this.$cep                 =  $('[data-input-cep-up]');
      this.$street              =  $('[data-input-street-up]');
      this.$neighborhood        =  $('[data-input-neighborhood-up]');
      this.$number              =  $('[data-input-number-up]');
      this.$reference           =  $('[data-input-reference-point-up]');
      this.$city                =  $('[data-input-city-up]');
      this.$state               =  $('[data-input-state-up]');
      this.$cpf                 =  $('[data-input-cpf-up]');
      this.$phone1              =  $('[data-input-phone1-up]');
      this.$phone2              =  $('[data-input-phone2-up]');
      this.$complement          =  $('[data-input-complement-up]');
      this.$avatar              =  $('[data-input-avatar]');
      this.$loginFacebook       =  $('[login-face]');

      this.$interest            = $('[data-interests]');
      this.$interestSelect      = $('[data-qtd-customers-interests]').val();

      this.$btnConfirm          =  $('[data-btn-confirm-up]');
      this.$customerId          =  $('[data-customer-id]');
      this.$alert               =  $('[data-confirmation-alert-up]');
      this.$alertError          =  $('[data-confirmation-alert-danger-up]');
      
      this.labels         = {}
      this.labels.confirm = 'Salvar';
      this.labels.success = 'Ok.';     

      this.bind();
      this.applyMasks();

      window.fbAsyncInit = function() {
        FB.init({
          appId            : document.getElementById('facebookAppId').value,
          autoLogAppEvents : true,
          xfbml            : true,
          version          : 'v3.1'
        });
  
      };
  

  },

  applyMasks: function() {
      this.$phone1.mask('(00)00000-0000')
      this.$phone2.mask('(00)00000-0000')
      this.$cpf.mask('000.000.000-00')
      this.$cep.mask('00.000-000')
  },      

  bind(){

      this.$btnConfirm.on('click', this.onConfirmClick.bind(this))
      this.btnModal.on('click', this.onConfirmModalClick.bind(this))
      this.$cep.on('change', this.onChangeCep.bind(this))
      this.$number.on('change', this.onChangeNumber.bind(this))
      this.$cpf.on('change', this.onChangeCpf.bind(this))
      this.$street.on('change', this.onChangeStreet.bind(this))
      this.$phone1.on('change', this.onChangePhone1.bind(this))
      this.$fullname.on('change', this.onChangeFullname.bind(this))
      this.$birthdate.on('change', this.onChangeBirthdate.bind(this))
      this.$neighborhood.on('change', this.onChangeNeighborhood.bind(this))
      this.$reference.on('change', this.onChangeReference.bind(this))
      this.$city.on('change', this.onChangeCity.bind(this))
      this.$state.on('change', this.onChangeState.bind(this))
      this.$complement.on('change', this.onChangeComplement.bind(this))
      this.$loginFacebook.on('click', this.onImageChange.bind(this))
      this.$modal.on('hide.bs.modal', $.proxy(this.onHideModal, this))

      this.$interest.on('change', this.onChangeInterest.bind(this))
  },

  onConfirmModalClick: function(event) {
    this.$modal.modal('hide'); 
  },

  onHideModal: function() {
    $('#image-container').children("img").remove();   
  },  

  onImageChange: function(event) {
    event.preventDefault();
   
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v3.1&appId='+ document.getElementById('facebookAppId').value + '&autoLogAppEvents=1';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  
    // login with facebook with extra permissions
    FB.login(function(response) {
      if (response.status === 'connected') {
        FB.api('/me', 'GET', {fields: 'first_name,last_name,name,id,picture.width(150).height(150)'}, function(response) {
          if($('[customer-avatar-mode]').val()) {
            $('[select-avatar]').attr('src', response.picture.data.url);
            $('[customer-picture]').val(response.picture.data.url);
          } else {
            $('[select-avatar]').attr('src', 'https://graph.facebook.com/'+response.id+'/picture?type=normal');
            $('[customer-picture]').val('https://graph.facebook.com/'+response.id+'/picture?type=normal');
          }          

          $('[login-face]').addClass('sr-only');
          $('[image-upload-ok]').removeClass('sr-only');
        });
      } else if (response.status === 'not_authorized') {
          
      } else {
        
      }
    }, {scope: 'email'});

  },

  onChangeComplement() {
    if(this.$complement.val() == '') {          
      $('[data-input-complement-up]').addClass('is-invalid')
      $('[data-input-complement-up]').val('')
    } else {
      $('[data-input-complement-up]').removeClass('is-invalid')
      $('[data-input-complement-up]').addClass('is-valid')
    }    
  },

  onChangeState() {
    if(this.$state.val() == '') {          
      $('[data-input-state-up]').addClass('is-invalid')
      $('[data-input-state-up]').val('')
    } else {
      $('[data-input-state-up]').removeClass('is-invalid')
      $('[data-input-state-up]').addClass('is-valid')
    }
  },

  onChangeCity() {
    if(this.$city.val() == '') {          
      $('[data-input-city-up]').addClass('is-invalid')
      $('[data-input-city-up]').val('')
    } else {
      $('[data-input-city-up]').removeClass('is-invalid')
      $('[data-input-city-up]').addClass('is-valid')
    }
  },

  onChangeReference(){
    if(this.$reference.val() == '') {          
      $('[data-input-reference-point-up]').addClass('is-invalid')
      $('[data-input-reference-point-up]').val('')
    } else {
      $('[data-input-reference-point-up]').removeClass('is-invalid')
      $('[data-input-reference-point-up]').addClass('is-valid')
    }
  },

  onChangeNeighborhood() {
    if(this.$neighborhood.val() == '') {          
      $('[data-input-neighborhood-up]').addClass('is-invalid')
      $('[data-input-neighborhood-up]').val('')
    } else {
      $('[data-input-neighborhood-up]').removeClass('is-invalid')
      $('[data-input-neighborhood-up]').addClass('is-valid')
    }
  },

  onChangeBirthdate() {
    if(this.$birthdate.val() == '') {          
      $('[data-input-birthdate-up]').addClass('is-invalid')
      $('[data-input-birthdate-up]').val('')
    } else {
      $('[data-input-birthdate-up]').removeClass('is-invalid')
      $('[data-input-birthdate-up]').addClass('is-valid')
    }
  },

  onChangeFullname() {
    if(this.$fullname.val() == '') {          
      $('[data-input-name-up]').addClass('is-invalid')
      $('[data-input-name-up]').val('')
    } else {
      $('[data-input-name-up]').removeClass('is-invalid')
      $('[data-input-name-up]').addClass('is-valid')
    }
  },

  onChangePhone1() {
    var phone1 = this.$phone1.val();

    if(phone1.length < 14) {          
      $('[data-input-phone1-up]').addClass('is-invalid')
    } else {
      $('[data-input-phone1-up]').removeClass('is-invalid')
      $('[data-input-phone1-up]').addClass('is-valid')
    }
  },

  onChangeCpf() {
      var cpf = this.$cpf.val();

      if(cpf.length < 14) {          
        $('[data-input-cpf-up]').addClass('is-invalid')
        $('[data-input-cpf-up]').val('')
      } else {
        $('[data-input-cpf-up]').removeClass('is-invalid')
        $('[data-input-cpf-up]').addClass('is-valid')
      }
  },

  onChangeStreet() {
      if(this.$street.val() == '') {          
        $('[data-input-street-up]').addClass('is-invalid')
        $('[data-input-street-up]').val('')
      } else {
        $('[data-input-street-up]').removeClass('is-invalid')
        $('[data-input-street-up]').addClass('is-valid')
      }
  },

  onChangeNumber() {

    if(this.$number.val() == '') {          
      $('[data-input-number-up]').addClass('is-invalid')
      $('[data-input-number-up]').val('')
    } else {
      $('[data-input-number-up]').removeClass('is-invalid')
      $('[data-input-number-up]').addClass('is-valid')
    }

    },    

  onChangeCep(event) {
      event.preventDefault()

      $('[data-confirmation-alert-danger-up]').addClass('sr-only')
      $('[data-confirmation-alert-danger-up]').text('Por favor, preencha corretamente os campos obrigatórios.')
            
      var cep = this.$cep.val().replace(/[^\d]+/g,'')
      var url = `https://viacep.com.br/ws/${cep}/json/`
    
      if (cep.length < 8) {
        $('[data-confirmation-alert-danger-up]').text('CEP inválido.')
        $('[data-confirmation-alert-danger-up]').removeClass('sr-only')
        $('[data-input-cep-up]').focus()
        $('[data-input-cep-up]').val('')
        return
      }
  
      $.ajax({
        url: url,
        dataType: 'jsonp',
        crossDomain: true,
        contentType: "application/json",
        success : function (json) {
  
          if(false == ('localidade' in json)) {
            $('[data-confirmation-alert-danger-up]').text('CEP inválido.')
            $('[data-confirmation-alert-danger-up]').removeClass('sr-only')
            $('[data-input-cep-up]').focus()
            $('[data-input-cep-up]').val('')
            return
          }
  
          if ("" != json.logradouro) {
            $('[data-input-street-up]').val(json.logradouro)
            $('[data-input-street-up]').prop('disabled', true)
          }

            
          if ("" != json.bairro) {
            $('[data-input-neighborhood-up]').val(json.bairro)
            $('[data-input-neighborhood-up]').prop('disabled', true)
          }
  
          if ("" != json.localidade) {
            $('[data-input-city-up]').val(json.localidade)
            $('[data-input-city-up]').prop('disabled', true)
          }
  
          if ("" != json.uf) {
            $('[data-input-state-up]').val(json.uf)
            $('[data-input-state-up]').prop('disabled', true)           
          }
  
        }
      });
    },

    onChangeInterest(event) {
      event.preventDefault()

      const $btn = $(event.currentTarget);
      const interest_types_id = $.trim($btn.data('id'));
      const interest = $.trim($btn.data('value'));

      if($("input[name='interests-"+interest_types_id+"']").is(':checked')){
        this.createInterest(interest_types_id, interest, this.$customerId.val());
        this.$interestSelect++;
      } else {
        this.deleteInterest(interest_types_id, this.$customerId.val());
        this.$interestSelect--;
      }

    },

    createInterest(interest_types_id, interest, customerId) {

      const saving_interest = $.ajax({
        method: 'POST',
        url: '/profile/interest/create',
        contentType: 'application/json',
        data: JSON.stringify({
          _token            : this.token,
          interest_types_id : interest_types_id,
          interest          : interest, 
          customers_id      : customerId,
        }),
      })

      saving_interest.done((data) => {        
        
      })
  
      saving_interest.fail((error) => {
        console.log('Erro: ', error)
      }) 

    },

    deleteInterest(interest_types_id, customerId) {

      const delete_interest = $.ajax({
        method: 'POST',
        url: '/profile/interest/delete',
        contentType: 'application/json',
        data: JSON.stringify({
          _token            : this.token,
          interest_types_id : interest_types_id,
          customers_id      : customerId,
        }),
      })

      delete_interest.done((data) => {        
        
      })
  
      delete_interest.fail((error) => {
        console.log('Erro: ', error)
      })   

    },

    onConfirmClick(event) {
      event.preventDefault()

      $('[data-form-profile-alert-session-up]').addClass('sr-only')
      $('[data-updated-alert-up]').addClass('sr-only') 
      this.$btnConfirm.prop('disabled', false)
      this.$alert.addClass('sr-only')

      this.$fullname.removeClass('is-invalid')
      this.$email.removeClass('is-invalid')
      this.$birthdate.removeClass('is-invalid')
      this.$phone1.removeClass('is-invalid')
      this.$cpf.removeClass('is-invalid')
      this.$cep.removeClass('is-invalid')
      this.$street.removeClass('is-invalid')
      this.$number.removeClass('is-invalid')
      this.$reference.removeClass('is-invalid')
      this.$neighborhood.removeClass('is-invalid')
      this.$city.removeClass('is-invalid')
      this.$state.removeClass('is-invalid')
      this.$complement.removeClass('is-invalid')  
      
      const cpf = $('[data-input-cpf-up]').val().replace(/[^\d]+/g,'')
      const cep = $('[data-input-cep-up]').val().replace(/[^\d]+/g,'')
     
      if(this.$fullname.val() === '') {
        this.$fullname.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }
      
      if(this.$email.val() === '') {
        this.$email.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$cpf.val() === '') {
        this.$cpf.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$birthdate.val() === '') {
        this.$birthdate.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$cep.val() === '') {
        this.$cep.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$street.val() === '') {
        this.$street.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$neighborhood.val() === '') {
        this.$neighborhood.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$number.val() === '') {
        this.$number.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$complement.val() === '') {
        this.$complement.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }
  
      if(this.$reference.val() === '') {
        this.$reference.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$city.val() === '') {
        this.$city.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }
  
      if(this.$state.val() === '') {
        this.$state.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }

      if(this.$phone1.val() === '') {
        this.$phone1.addClass('is-invalid')
        this.$alertError.removeClass('sr-only')
        return
      }
      
      if(this.$interestSelect <= 0) {
        this.$alertError.removeClass('sr-only');
        this.$alertError.text('Por favor, nos informe o(s) seu(s) interesse(s).');
        return
      } else {
        this.$alertError.addClass('sr-only');
        this.$alertError.text('Por favor, preencha corretamente os campos obrigatórios.');
      }
     
      const saving = $.ajax({
        method: 'POST',
        url: '/profile/update',
        contentType: 'application/json',
        data: JSON.stringify({
          _token         : this.token,
          fullname       : this.$fullname.val(),
          email          : this.$email.val(), 
          birthdate      : this.$birthdate.val(),
          customer_id    : this.$customerId.val(),
          phone1         : this.$phone1.val(),
          phone2         : this.$phone2.val(),
          cpf            : cpf,
          cep            : cep,
          street         : this.$street.val(),
          number         : this.$number.val(),
          complement     : this.$complement.val(),
          reference      : this.$reference.val(),
          neighborhood   : this.$neighborhood.val(),
          city           : this.$city.val(),
          state          : this.$state.val(),
          avatar         : ($('[customer-picture]').val()) ? $('[customer-picture]').val() : null,
        }),
      })

      saving.done((data) => {

        this.$btnConfirm.prop('disabled', true)

        if (false === data.success) {
          this.$alertError.removeClass('sr-only')
          this.$btnConfirm.prop('disabled', false)
          return
        }

        if (false === data.data.cAddress.original.modified && false === data.data.customer.original.modified) {
          this.$alert.addClass('sr-only')
          $('[data-updated-alert-up]').removeClass('sr-only')
          this.$alertError.addClass('sr-only')  
          this.$btnConfirm.prop('disabled', false)
          return
        }
        
        $('[data-form-profile-alert-session-up]').addClass('sr-only')
        $('[data-updated-alert-up]').addClass('sr-only')
        this.$alert.removeClass('sr-only')  
        this.$alertError.addClass('sr-only')  

        window.location.href = "/";

        $('[data-input-name-up]').removeClass('is-valid')
        $('[data-input-birthdate-up]').removeClass('is-valid')
        $('[data-input-cep-up]').removeClass('is-valid')
        $('[data-input-street-up]').removeClass('is-valid')
        $('[data-input-neighborhood-up]').removeClass('is-valid')
        $('[data-input-number-up]').removeClass('is-valid')
        $('[data-input-reference-point-up]').removeClass('is-valid')
        $('[data-input-city-up]').removeClass('is-valid')
        $('[data-input-state-up]').removeClass('is-valid')
        $('[data-input-cpf-up]').removeClass('is-valid')
        $('[data-input-phone1-up]').removeClass('is-valid')
        $('[data-input-complement-up]').removeClass('is-valid')
        
      })
  
      saving.fail((error) => {
        console.log('Erro: ', error)
      })   
             
    },  

}

const Profile = () => {
  ProfileCustomer.start()
}

export default Profile