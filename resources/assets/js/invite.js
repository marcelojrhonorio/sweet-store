const InviteFriends = {
  start() {
    this.updating = false
    this.token    = $('meta[name="csrf-token"]').attr('content')
    
    this.ui                    = {}   
    this.ui.$emailModal        = $('[data-download-invite-email]')  
    this.ui.$buttonList        = $('data-share-buttons')
    this.ui.$customer          = $('[data-customer-id]')
    this.ui.$btnTwitter        = $('[data-btn-twitter]')
    this.ui.$btnFacebook       = $('[data-btn-facebook]')
    this.ui.$btnWhatsapp       = $('[data-btn-whatsapp]')
    this.ui.$btnEmail          = $('[data-btn-email]')
    this.ui.$sweetbonus        = $('[data-sweetbons-url]')
    this.ui.$btnCopyLink       = $('[data-btn-copy-link]');
    this.ui.$btnDownloadApp    = $('[data-btn-download-app]');
    
    this.bind()
    
  },

  bind() {
    this.ui.$btnEmail.on('click', this.onEmailClick.bind(this))
    this.ui.$btnWhatsapp.on('click', this.onWhatsappClick.bind(this))
    this.ui.$btnFacebook.on('click', this.onFacebookClick.bind(this))
    this.ui.$btnTwitter.on('click', this.onTwitterClick.bind(this))
    this.ui.$btnCopyLink.on('click', this.onCopyLinkClick.bind(this))
    this.ui.$btnDownloadApp.on('click', this.onDownloadAppClick.bind(this))
  },  

  onDownloadAppClick(event) {
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
      html: this.getTextApp(),
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
        
        var url = this.shortUrl('app');        
        
      }
    });  
  },

  onCopyLinkClick(event){

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

        var copyText = document.getElementById("myInput");

        var url = this.shortUrl('direct');

        url.done(function( data ){
          copyText.value = data.shorturl;
          copyText.select();
          document.execCommand("copy");    
        
          $('[data-span-copy-link]').text('Copiado')   
          
          window.setTimeout(function(){
            $('[data-span-copy-link]').text('Copiar Link');
          }, 2000);
        });
      }
    });   
       
  },

  getTextApp() {
    return "<hr>" + 
    "<p style='text-align: justify; font-size: 13px;'>" +
      "Para que sua indicação seja completada, o usuário indicado " + 
      "deve ser membro da Sweet há pelo menos 15 dias, além de baixar o app e usá-lo regularmente. " +
      "Vale ressaltar que caso estes requisitos não sejam cumpridos, sua indicação" +
      " não será computada e também não ganhará os 10 pontos" + 
    "</p>";
  },

  getText () {
    return "<hr>" + 
      "<p style='text-align: justify; font-size: 13px;'>" +
        "Para que sua indicação seja completada, o usuário indicado " + 
        "deve confirmar o e-mail e completar seu cadastro no portal. Vale ressaltar que caso isso não ocorra," + 
        " sua indicação não será computada e também não ganhará os 10 pontos." + 
      "</p>";
  },

  onEmailClick(event){

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

        const customer = this.ui.$customer.val();
        var sending = ''
        $.ajax({
          url : `/member-get-member/email/send/${customer}`,   
          type: `GET`,
          assync: false,
          success: function(text){
            sending = text
            $('[data-download-invite-email]') .modal('show')
            if (sending >= 3) {
              $('[data-btn-email]').hide();
            }
          }
        }) 
      }
    }); 
  },

  onWhatsappClick(event){

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

        const shortingUrl = this.shortUrl('whatsapp');
    
        shortingUrl.done(function( data ){
          var link = 'https://api.whatsapp.com/send?text='
          + 'Parabéns! Você foi convidado para a Sweet e por isso ganhou 50 pontos para juntar e trocar por produtos que você receberá grátis em casa! Não perca tempo, clique agora: '
          + data.shorturl
        
          window.open(link);
        
        });
      }
    }); 
  },

  onFacebookClick(event){  

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

        var url = this.ui.$sweetbonus.val()
        + '/compartilhar/'
        + 'postback?utm_source=facebook&utm_campaign=MemberGetMember&customer_id='
        + this.ui.$customer.val();

        FB.ui({
          method  : 'share',
          display : 'popup',
          href    : url,
          hashtag : '#sweetbonusfacebook', 
          quote   : 'Parabéns! Você foi convidado para a Sweet e por isso ganhou 50 pontos para juntar e trocar por produtos que você receberá grátis em casa! Não perca tempo, clique agora: '
        }, function(response){});
      }
    });     
  },

  onTwitterClick(event){

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

        const shortingUrl = this.shortUrl('twitter');
    
        shortingUrl.done(function( data ){
          var link = 'https://twitter.com/intent/tweet?text='
          + 'Parabéns! Você foi convidado para a Sweet e por isso ganhou 50 pontos para juntar e trocar por produtos que você receberá grátis em casa! Não perca tempo, clique agora: '
          + data.shorturl
          + encodeURIComponent('\n')
          + encodeURIComponent('#sweetbonus #sweetbonustwitter')
          
          window.open(link);
        });
      }
    });     
  },

  shortUrl: function(social)
  {     
    if("app" === social) 
    {
      this.getHash();
      
    } else {
      var url = 
      this.ui.$sweetbonus.val()      
      + '/compartilhar/'
      + 'postback?utm_source='
      + social
      +'&utm_campaign=MemberGetMember&customer_id=' 
      + this.ui.$customer.val(); 
    }          

    const shortUrl = 
    $.getJSON("https://is.gd/create.php?callback=?", {
      url: url,
      format: "json"
    });

    return shortUrl;
  },

  getHash: function() {
    $.ajax({
      url : `/app-indications/verify/hash`,   
      type: `GET`,
      success: function(data){
       
        var url = $('[data-store-url]').val() + 
        '/app-indications/download/' + data;

        var copyText = document.getElementById("myInputApp");

        copyText.value = url;
        copyText.select();
        document.execCommand("copy");    
        
        $('[data-span-download-app]').text('Copiado')   
          
        window.setTimeout(function(){
          $('[data-span-download-app]').text('Link Sweet');
        }, 2000);
        
      }
    }) 
  },

}

const Invite = () => {
    InviteFriends.start()
}

export default Invite;