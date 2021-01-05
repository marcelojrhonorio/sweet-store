const ActionShare = {  
    start() { 
      this.token    = $('meta[name="csrf-token"]').attr('content')     
      
      this.ui                    = {}       
      this.ui.$customer          = $('[customer-id]')
      this.ui.$shareFacebook     = $('[data-btn-actions]')
      this.ui.$actionId          = $('[action-id]')
      this.ui.$actionType        = $('[action-type]')
      this.ui.$store             = $('[data-store-url]');
  
      this.bind()
    },
  
    bind() {  
      this.ui.$shareFacebook.on('click', '[data-face]', $.proxy(this.onBtnShareClick, this));
      this.ui.$shareFacebook.on('click', '[btn-copy-link-share]', $.proxy(this.onBtnCopyLink, this));
      this.ui.$shareFacebook.on('click', '[btn-share-whatsapp]', $.proxy(this.onBtnShareWhatsApp, this));
    },    

    onBtnShareWhatsApp: function(event){
      event.preventDefault();
      event.stopPropagation();

      const $btn = $(event.currentTarget);
      const customerid = $.trim($btn.data('customerid'));
      const actiontype = $.trim($btn.data('actiontype'));
      const actionid = $.trim($btn.data('actionid'));

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-info',
          cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false,
      })
    
      const {value: accept} = swalWithBootstrapButtons.fire({
        title: 'Atenção',
        input: 'checkbox',
        html: this.getText(),
        showConfirmButton: true,
        showCancelButton: true,
        inputValue: 0, 
        inputPlaceholder:
          'Estou ciente e quero continuar.',
        confirmButtonText:
          'Continuar',             
        cancelButtonText:
            'Não quero',
        reverseButtons: true,
        inputValidator: (result) => {
          if (!result) {
            return !result && 'É necessário concordar com as condições acima.'
          }  

          var fullUrl = this.ui.$store.val()
          + '/share-action/'
          + 'postback?customer_id=' + customerid
          + '&action_id=' + actionid
          + '&action_type=' + actiontype;

          const shortingUrl = this.shortUrl(fullUrl);
          
          shortingUrl.done(function( data ){
            var link = 'https://api.whatsapp.com/send?text='
            + 'Você recebeu uma oportunidade de ganhar pontos na Sweet Bonus! Não perca tempo, clique agora: '
            + data.shorturl
            
            window.open(link);
            
          });          
        }
      });        
    },

    onBtnCopyLink: function(event){
      event.preventDefault();
      event.stopPropagation();

      const $btn = $(event.currentTarget);
      const customerid = $.trim($btn.data('customerid'));
      const actiontype = $.trim($btn.data('actiontype'));
      const actionid = $.trim($btn.data('actionid'));

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-info',
          cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false,
      })
    
      const {value: accept} = swalWithBootstrapButtons.fire({
        title: 'Atenção',
        input: 'checkbox',
        html: this.getText(),
        showConfirmButton: true,
        showCancelButton: true,
        inputValue: 0, 
        inputPlaceholder:
          'Estou ciente e quero continuar.',
        confirmButtonText:
          'Continuar',             
        cancelButtonText:
            'Não quero',
        reverseButtons: true,
        inputValidator: (result) => {
          if (!result) {
            return !result && 'É necessário concordar com as condições acima.'
          }  

          var fullUrl = this.ui.$store.val()
          + '/share-action/'
          + 'postback?customer_id=' + customerid
          + '&action_id=' + actionid
          + '&action_type=' + actiontype;

          var copyText = document.getElementById("inputShare");

          var url = this.shortUrl(fullUrl);
          
          url.done(function( data ){
            copyText.value = data.shorturl;
            copyText.select();
            document.execCommand("copy");    
          
            $btn[0].innerText = 'Copiado';  
          
            window.setTimeout(function(){
            $btn[0].outerHTML = '<button class="share-copy" data-customerid='+ customerid +
                                ' data-actionid='+ actionid +' data-actiontype='+ actiontype +
                                ' btn-copy-link-share><i class="fas fa-copy"></i><span style="margin-left:inherit;'+
                                'font-size:14px;" span-copy-link-share> Copiar Link</span></button>';        
            }, 2000);
          });          
        }
      }); 
    },
  
    onBtnShareClick: function(event){  
      event.preventDefault();
      event.stopPropagation();

      const $btn = $(event.currentTarget);
      const customerid = $.trim($btn.data('customerid'));
      const actiontype = $.trim($btn.data('actiontype'));
      const actionid = $.trim($btn.data('actionid'));

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-info',
          cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false,
      })
    
      const {value: accept} = swalWithBootstrapButtons.fire({
        title: 'Atenção',
        input: 'checkbox',
        html: this.getText(),
        showConfirmButton: true,
        showCancelButton: true,
        inputValue: 0, 
        inputPlaceholder:
          'Estou ciente e quero continuar.',
        confirmButtonText:
          'Continuar',             
        cancelButtonText:
            'Não quero',
        reverseButtons: true,
        inputValidator: (result) => {
          if (!result) {
            return !result && 'É necessário concordar com as condições acima.'
          }  

          var url = this.ui.$store.val() 
          + '/share-action/'
          + 'postback?customer_id=' + customerid
          + '&action_id=' + actionid
          + '&action_type=' + actiontype;

          FB.ui({
            method  : 'share',
            display : 'popup',
            href    : url,
            hashtag : '#sweetbonusfacebook', 
            quote   : 'Você foi convidado para participar das ações do portal SweetBonus. Junte pontos e troque por produtos que você receberá grátis em casa! Não perca tempo, clique agora: '
          }, function(response){});         
            }
        }); 
      
    },  

    getText () {
      return "<hr>" + 
        "<p style='text-align: justify; font-size: 13px;'>" +
          "Para que sua indicação seja completada, o usuário indicado " + 
          "deve confirmar o e-mail e completar seu cadastro no portal. Vale ressaltar que caso isso não ocorra," + 
          " sua indicação não será computada e também não ganhará os 10 pontos." + 
        "</p>";
    },

    shortUrl: function(fullUrl){      
  
      const shortUrl = 
      $.getJSON("https://is.gd/create.php?callback=?", {
        url: fullUrl,
        format: "json"
      });
  
      return shortUrl;  
    },
  
  }
  
  const Action = () => {
    ActionShare.start()
  }
  
  export default Action;