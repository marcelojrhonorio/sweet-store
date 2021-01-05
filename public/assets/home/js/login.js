(function($) {
  const Login = {
    start: function() {
      this.$form  = $('[data-form-register]');
      this.$alert = $('[data-form-register-alert]');
      this.$alertSession = $('[data-form-register-alert-session]');  
      this.$page = $('[data-page]');   

      this.$linkCadastro = $('[data-link-cad]');

      this.$emailGroup = $('[data-login-email-group]');
      this.$emailInput = $('[data-login-email-input]');

      this.$passwordGroup = $('[data-login-password-group]');
      this.$passwordInput = $('[data-login-password-input]');

      this.$passwordReset = $('[data-password-reset]');
      this.$btn = $('[data-login-btn]');
      this.$btnText = $('[data-login-btn-text]');

      this.$backEmailGroup = $('[data-login-back-group]');
      this.$backEmailLink  = $('[data-login-link]');

      this.$passwordInput.removeAttr("required");
      this.$btn.removeClass('wait-button');      
      this.$btn.addClass('submit-button');      
      this.$btnText.text('entrar');    

      this.$action = 'verify';
      
      this.applyMasks();
      this.bind();
    },

    applyMasks: function() {
      if(1 == $('[data-change-pass]').val()) {
        this.$passwordInput.mask('00/00/0000')
      }      
    },  
    
    bind () {
      this.$form.on('submit', $.proxy(this.onFormSubmit, this));
      this.$emailInput.on('change', $.proxy(this.onEmailEdit, this));
      this.$passwordInput.on('change', $.proxy(this.onPasswordEdit, this));
      this.$linkCadastro.on('click', this.onLinkCadastroClick.bind(this));
    },
    
    onLinkCadastroClick(event) {
      this.redirectRegister();
    },

    onPasswordEdit (event) {
      event.preventDefault();
      if ('' == this.$passwordInput.val()) {
        if(1 == $('[data-change-pass]').val()) { 
          this.$alert.text('Digite sua data de nascimento para continuar.');
          return;
        } else {
          this.$alert.text('Digite uma senha para continuar.');
          return;
        }        
      }
    },

    onEmailEdit (event) {
      event.preventDefault();
      if ('' == this.$emailInput.val()) {
        this.$alert.text('Digite um e-mail para continuar.');
        return;
      }      
      this.$btn.prop('disabled', false);
    },

    onFormSubmit (event) {
      event.preventDefault();

      this.$alertSession.addClass('sr-only');

      if ('verify' === this.$action){
        this.emailVerify();
        return;
      }

      this.userLogin();
      return;

    },

    emailVerify () {
      this.$btn.prop('disabled', true);
      const email = this.$emailInput.val();
      this.$btn.removeClass('submit-button');
      this.$btn.addClass('wait-button');
      this.$btnText.text('AGUARDE...');

      const verifying = $.ajax({
        url      : `/login/verify-email?email=${email}`,
        dataType : 'json',
        method   : 'get',
      });

      verifying.done($.proxy(this.onVerifySuccess, this));
      verifying.fail($.proxy(this.onVerifyFail, this));   
    },

    onVerifySuccess (data) {
      if (!data.success) {       
        this.$alert.removeClass('sr-only');
        this.$alert.addClass('alert-warning');

        if(!('share-action' === $('[data-page]').val()))
          this.$alert.html('Ops, não encontramos o usuário deste e-mail. Não se preocupe. <a href='+ $('[data-login-sweetbonus-url]').val()+'> Cadastre-se agora!</a>');  
        else
          this.$alert.html('Não encontramos o usuário deste e-mail. Cadastre-se agora! <br><span href=' + this.redirectRegister() + '> Redirecionando...</span>');  

        this.$btn.prop('disabled', true);
        this.$btn.removeClass('wait-button');      
        this.$btn.addClass('submit-button');        
        this.$btnText.text('entrar');
        return;
      }

      const changed = data.data.data.changed_password;

      if(0 == $('[data-change-pass]').val()) {
        if (0 == changed) {
          const email = this.$emailInput.val();
          this.$alert.removeClass('sr-only');
          this.$alert.addClass('alert-warning');
          this.$alert.text('Você precisa criar uma senha. Redirecionando...');
          this.$btn.prop('disabled', true);
          this.$btn.removeClass('submit-button');
          this.$btn.addClass('wait-button');      
          this.$btnText.text('AGUARDE...');
          window.setTimeout(function(){
            window.location.href = '/password/create?email=' + email;
          }, 3000);
          
          return;
        }
      }      

      this.$passwordGroup.removeClass('sr-only');
      this.$backEmailGroup.removeClass('sr-only');
      this.$emailGroup.addClass('sr-only');
      this.$passwordReset.addClass('sr-only');
      this.$alert.addClass('sr-only');
      this.$action = 'login';
      this.$passwordInput.focus();

      this.$btn.prop('disabled', false);
      this.$btn.removeClass('wait-button');      
      this.$btn.addClass('submit-button'); 
      this.$btnText.text('entrar');

      return;
    },

    redirectRegister () {
      window.setTimeout(function(){
        window.location.href = $('[data-login-sweetbonus-url]').val() + 
                                 '/compartilhar/postbackActions?' +
                                 'customer_id=' + $('[customerId]').val() +
                                 '&action_id=' + $('[data-actionId]').val() +
                                 '&action_type=' + $('[data-actionType]').val();
      }, 1000);      
    },

    onVerifyFail (error) {
      console.log(error);
      this.$btn.prop('disabled', false);
      this.$btn.removeClass('wait-button');      
      this.$btn.addClass('submit-button'); 
      this.$btnText.text('entrar');
    },

    userLogin () {
      const password = this.$passwordInput.val();

      if ('' === password)
      {
        this.$alert.removeClass('sr-only');
        this.$alert.addClass('alert-warning');
        if(1 == $('[data-change-pass]').val()) {
          this.$alert.text('Por favor, digite sua data de nascimento para continuar.');
        } else {
          this.$alert.text('Por favor, digite uma senha para continuar.');
        }
        
        this.$passwordInput.focus();
        this.$btn.prop('disabled', false);
        this.$btn.removeClass('wait-button');      
        this.$btn.addClass('submit-button');        
        this.$btnText.text('entrar');
        return;
      }

      const headers = {
        'Accept'      : 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      };
      
      this.$btn.prop('disabled', true);
      this.$btn.removeClass('submit-button');
      this.$btn.addClass('wait-button');   
      this.$btnText.text('AGUARDE...');
      const data = {
        email    : this.$emailInput.val(),
        password : this.$passwordInput.val(),
      }

      const values = JSON.stringify(data);
      
      if(this.$page.val() && this.$page.val() === "share-action")
      {
        const email = this.$emailInput.val();
        const pass = this.$passwordInput.val();

        var authenticating = $.ajax({
          url      : `/loginFromShare?email=${email}&password=${pass}`,
          dataType : 'json',
          method   : 'get',
        });        
      } 
      else
      {
        var authenticating = $.ajax({
          cache      : false,
          type       : 'post',
          dataType   : 'json',
          data       : values,
          headers    : headers,
          url        : '/login',
          contentType: 'application/json; charset=utf-8',
        });
      }
      
      authenticating.done($.proxy(this.onAuthSuccess, this));
      authenticating.fail($.proxy(this.onAuthFail, this));       

    },

    onAuthSuccess (data) {
      if (data.success) {
        const customerData = data.data.data;

        this.$alert.addClass('sr-only');
        this.$btnText.text('REDIRECIONANDO...');

        const createdAt         = new Date(customerData.created_at.date);
        const cAnswersAt        = (null === customerData.campaign_answerswered_at) ? null : new Date(customerData.campaign_answerswered_at);
        const willShowCampaigns = this.willShowCampaigns(createdAt, cAnswersAt);

        

        if (willShowCampaigns) {
          window.location.href = $('[data-login-sweetbonus-url]').val() + '/campaigns/from-store/' + customerData.id;
          return;
        }
 
        

        if(this.$page.val() && this.$page.val() === "share-action") 
        {
          window.location.href =  '/share-action/' +
          'redirect?customer_id=' + $('[customerId]').val() +
          '&action_id=' + $('[data-actionId]').val() +
          '&action_type=' + $('[data-actionType]').val();  
          return;        
        }
        else
        {
          window.location.href =  '/';
          return;
        }       

      }

      this.$alert.removeClass('sr-only');
      this.$alert.addClass('alert-warning');

      if(1 == $('[data-change-pass]').val()) {
        this.$alert.text('Data de nascimento incorreta.');
      } else {
        this.$alert.text('Senha incorreta.');
      }
      
      this.$btn.prop('disabled', false);
      this.$btn.removeClass('wait-button');      
      this.$btn.addClass('submit-button');      
      this.$btnText.text('entrar');
      this.$passwordReset.removeClass('sr-only');
    },
    
    willShowCampaigns (createdAt, cAnswersAt) {
      const now = new Date(Date.now());

      var diffRegister = Math.abs(now - createdAt) / 1000;
      var daysRegister = Math.floor(diffRegister / 86400);

      var diffCampaign = (null === cAnswersAt)   ? null : (Math.abs(now - cAnswersAt) / 1000);
      var daysCampaign = (null === diffCampaign) ? null : (Math.floor(diffCampaign / 86400));

      if (daysRegister > 15 && (daysCampaign === null || daysCampaign > 15)) {
        return true;
      }

      return false;
    },

    onAuthFail (error) {
      console.log(error);
      this.$btn.prop('disabled', false);
      this.$btn.removeClass('wait-button');      
      this.$btn.addClass('submit-button');
      this.$btnText.text('entrar');
    },
  }

  $(function () {
    Login.start();
  });  
})(jQuery);