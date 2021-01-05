const ExchangePoints = {
  
  start() {
    this.token    = $('meta[name="csrf-token"]').attr('content');

    this.ui =                       {}
    this.ui.$modal               =  $('[data-modal-exchange]');
    this.ui.$modalSm             =  $('[data-modal-exchange-sm]');
    this.ui.$form                =  $('[data-form-exchange]');
    this.ui.$formSm              =  $('[data-form-exchange-sm]');

    this.ui.$instructions        =  $('[data-form-instructions]');

    this.ui.$phone               =  $('[data-input-phone]');
    this.ui.$cpf                 =  $('[data-input-cpf]');
    this.ui.$cep                 =  $('[data-input-cep]');
    this.ui.$address             =  $('[data-input-address]');
    this.ui.$number              =  $('[data-input-number]');
    this.ui.$reference           =  $('[data-input-reference-point]');
    this.ui.$neighborhood        =  $('[data-input-neighborhood]');
    this.ui.$city                =  $('[data-input-city]');
    this.ui.$state               =  $('[data-input-state]');
    this.ui.$complement          =  $('[data-input-complement]');
    
    this.ui.$btnConfirmSm        =  $('[btn-confirm-sm]');
    this.ui.$btnConfirm          =  $('[data-btn-confirm]');
    this.ui.$alert               =  $('[data-confirmation-alert]');
    this.ui.$alertError          =  $('[data-confirmation-alert-danger]');
    this.ui.$customerId          =  $('[data-customer-id]');
    this.ui.$enoughtContent      =  $('[data-content-enought]');
    this.ui.$insufficientContent =  $('[data-content-insufficient]');
    this.ui.$insufficientStamps  =  $('[data-content-insufficient-stamps]');
    this.ui.$socialNetwork       =  $('[data-content-social-network]');
    this.ui.$lastExchange        =  $('[data-content-last-exchange]');
    //this.ui.$lastExchange        = this.ui.$modal.find('[data-content-last-exchange]');

    this.ui.$textTitleVerify     =  $('[data-title-verify-exchange]');
    this.ui.$textTitleExchangeOK =  $('[data-title-exchange]');
    this.ui.$textBodyVerify      =  $('[data-body-verify-exchange]');
    this.ui.$textNoStamps        =  $('[data-text-no-stamps]');

    this.ui.$textBtnVerify       =  $('[data-text-button-verify]');    
    this.ui.$btnVerify           =  $('[data-verify-button]');    
    
    this.ui.$btnExchange         =  $('[data-btn-exchange]');
    
    this.ui.$useLastAddressGroup =  $('[data-group-last-address]');
    this.ui.$checkUseLastAddress =  $('[data-check-last-address]');
    this.ui.$lastAddress         =  $('[data-last-address]');
    this.ui.$checkNewAddress     =  $('[data-check-new-address]');
    this.ui.$addressGroup        =  $('[data-address-group]');
    this.ui.$addAddress          =  $('[data-label-add-address]');
    this.ui.$checkAddAddress     =  $('[data-check-add-address]');
    this.ui.$stampsRequired      =  $('[data-required-stamps]');
    this.ui.$noStamps            =  $('[data-no-stamps]');

    /* Social network exchange */    
    this.ui.$image               = $('[data-input-image]');

    this.ui.btnCancel            = this.ui.$modalSm.find('[btn-cancel-sm]');

    this.ui.$subject             = this.ui.$modalSm.find('[data-input-subject]');
    this.ui.$profileLink         = this.ui.$modalSm.find('[data-input-profile-link]');
    this.$progress               = this.ui.$modalSm.find('[data-upload-progress]');  
    this.$wrapUpload             = $('[data-wrap-upload]');
    this.$wrapFile               = $('[data-wrap-file]');
    this.$wrapPreview            = $('[data-wrap-preview]');
    this.$path                   = $('[data-input-path]');

    this.lastAddress             =  {};

    this.$enabled                =  $('[data-feature-enable]');

    this.$itemId                 =  $('data-item-id');
    this.$itemPoints             =  '';

    this.$customerPoints  = '';

    this.labels         = {}
    this.labels.confirm = 'Confirmar!';
    this.labels.success = 'Ok.';

    this.bind();
    this.applyMasks();    

  },

  applyMasks: function() {
    this.ui.$phone.mask('(00)00000-0000')
    this.ui.$cpf.mask('000.000.000-00')
    this.ui.$cep.mask('00.000-000')
  },  

  bind() {
    this.ui.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))    
    this.ui.$modalSm.on('hidden.bs.modal', this.onHiddenModalSm.bind(this))    
    this.ui.$btnExchange.on('click', this.onExchangeClick.bind(this))
    this.ui.$btnConfirmSm.on('click', this.onConfirmSmClick.bind(this))
    this.ui.$btnConfirm.on('click', this.onConfirmClick.bind(this))
    this.ui.$cep.on('change', this.onChangeCep.bind(this))
    this.ui.$checkUseLastAddress.on('change', this.onChangeCheckLastAddress.bind(this))
    this.ui.$checkNewAddress.on('change', this.onChangeNewAddress.bind(this))
    this.ui.$image.on('change', $.proxy(this.onImageChange, this));
    this.ui.$stampsRequired.on('click', '[data-stamp]', $.proxy(this.onStampInfo, this));
    this.ui.$formSm.on('click', '[data-destroy-image]', $.proxy(this.onDestroyImageClick, this));
    this.ui.$profileLink.on('change', this.onChangeLink.bind(this));
  },

  onChangeLink: function(event) {
    event.preventDefault();

      const verify_link = $.ajax({
        method: 'POST',
        url: '/exchange/social-network-exchange/verify-link',
        contentType: 'application/json',
        data: JSON.stringify({
          _token       : this.token,
          profile_link : this.ui.$profileLink.val(),
        }),
      }) 

      verify_link.done((data) => {
      if (data.success) {

        this.ui.$alertError.text(data.data.message);

        this.ui.$alertError.removeClass('sr-only');

        this.ui.$btnConfirmSm.prop('disabled', true);
        
      } else {
        this.ui.$alertError.text('');
        this.ui.$alertError.addClass('sr-only');
        this.ui.$btnConfirmSm.prop('disabled', false);
      }
      
      })

      verify_link.fail((error) => {
        console.log('Erro: ', error)
      })  
  },

  onDestroyImageClick: function(event) {
    event.preventDefault();

    this.$wrapPreview.children("div").remove();

    this.ui.$image.val('');
    this.$path.val('');
    this.$wrapPreview.addClass('sr-only');
    this.$wrapUpload.removeClass('sr-only');
  },

  onImageChange: function(event) {
    event.preventDefault();

    if ('' === event.target.value) {
      console.log('não vai upar');
      return;
    }

    this.$progress.removeClass('hidden');

    const token = $('meta[name="csrf-token"]').attr('content');

    const headers = {
      'X-CSRF-TOKEN': token,
    };

    const data = new FormData(this.ui.$formSm[1]);

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
      url        : 'exchange/social-network-exchange/upload', 
      headers    : headers,
      data       : data,
      xhr        : handleProgress,
    });

    uploading.done($.proxy(this.onImageUploadSuccess, this));

    uploading.fail($.proxy(this.onImageUploadFail, this));
  },

  onImageUploadSuccess: function(data) {
    var image = data.data.path + data.data.name;
    var url = $('[data-store-url]').val() + '/storage/' + image;

    this.$path.val(data.data.path + data.data.name);
    this.$progress.addClass('hidden');
    this.$wrapUpload.addClass('sr-only');

    this.$wrapPreview
        .html(`
          <div class="col-md-3" style="padding-top:4%;padding-bottom:5%;">
            <img alt="" img-preview>
          </div>
          <div class="col-md-9">
            <button class="btn btn-danger" type="button" data-path="${image}" data-destroy-image>
              Excluir
            </button>
          </div>
        `)
        .removeClass('sr-only');
      
    var d = new Date();
    $('[img-preview]').attr('src', url+'?'+d.getTime());    
  },

  onImageUploadFail: function(error) {
    console.log(error);
  },

  onStampInfo: function(event){
    event.preventDefault();
    event.stopPropagation();

    const $btn = $(event.currentTarget);
    const stamp_id = $.trim($btn.data('stampid'));
    const stamp_title = $.trim($btn.data('stamptitle'));
    const stamp_icon = $.trim($btn.data('stampicon'));
    const stamp_type = $.trim($btn.data('stamptype'));
    const required_amount = $.trim($btn.data('requiredamount'));  

    var text = '';
    if(1 == stamp_type) {
      //action
      text = '';
    } else if(2 == stamp_type){
      //email
      text = '';
    } else if(3 == stamp_type){
      //incentive_email
      text = 'Faça ' + required_amount + ' pesquisas identificadas com o selo 🥇.';
    } else if(4 == stamp_type){
      //member_get_member
      text = 'Faça ' + required_amount + ' indicações para a Sweet.';
    } else {
      //profile
      text = 'Faça 1 atualização de perfil.';     
    }

    Swal.fire({
      title: stamp_title,
      text: text,
      imageUrl: $('#sweetmedia').val() + '/storage/' + stamp_icon,
      imageWidth: 200,
      imageHeight: 200,
      imageAlt: 'Stamp image',
      confirmButtonColor: '#55c5c4',
      animation: true
    })
  },

  onChangeCheckLastAddress() {
    if(this.ui.$checkUseLastAddress.is(':checked')) {
      this.ui.$cep.val(this.lastAddress.cep)
      this.ui.$cep.prop('disabled', true)
      this.ui.$cep.addClass('sr-only')
      this.ui.$address.val(this.lastAddress.address)
      this.ui.$address.prop('disabled', true)
      this.ui.$address.addClass('sr-only')
      this.ui.$number.val(this.lastAddress.number)
      this.ui.$number.prop('disabled', true)
      this.ui.$number.addClass('sr-only')
      this.ui.$reference.val(this.lastAddress.reference)
      this.ui.$reference.prop('disabled', true)
      this.ui.$reference.addClass('sr-only')
      this.ui.$neighborhood.val(this.lastAddress.neighborhood)
      this.ui.$neighborhood.prop('disabled', true)
      this.ui.$neighborhood.addClass('sr-only')
      this.ui.$city.val(this.lastAddress.city)
      this.ui.$city.prop('disabled', true)
      this.ui.$city.addClass('sr-only')
      this.ui.$state.val(this.lastAddress.state)
      this.ui.$state.prop('disabled', true)
      this.ui.$state.addClass('sr-only')
      this.ui.$complement.val(this.lastAddress.complement)
      this.ui.$complement.prop('disabled', true)
      this.ui.$complement.addClass('sr-only')
      this.ui.$addressGroup.addClass('sr-only')
      this.ui.$addAddress.addClass('sr-only')
      this.ui.$checkAddAddress.prop('checked', false)
      
    } 
  },

  onChangeNewAddress() {
    if(this.ui.$checkNewAddress.is(':checked')) {
      this.ui.$cep.val('')
      this.ui.$cep.prop('disabled', false)
      this.ui.$cep.removeClass('sr-only')
      this.ui.$address.val('')
      this.ui.$address.prop('disabled', false)
      this.ui.$address.removeClass('sr-only')
      this.ui.$number.val('')
      this.ui.$number.prop('disabled', false)
      this.ui.$number.removeClass('sr-only')
      this.ui.$reference.val('')
      this.ui.$reference.prop('disabled', false)
      this.ui.$reference.removeClass('sr-only')
      this.ui.$neighborhood.val('')
      this.ui.$neighborhood.prop('disabled', false)
      this.ui.$neighborhood.removeClass('sr-only')
      this.ui.$city.val('')
      this.ui.$city.prop('disabled', false)
      this.ui.$city.removeClass('sr-only')
      this.ui.$state.val('')
      this.ui.$state.prop('disabled', false)
      this.ui.$state.removeClass('sr-only')
      this.ui.$complement.val('')
      this.ui.$complement.prop('disabled', false)
      this.ui.$complement.removeClass('sr-only')
      this.ui.$addressGroup.removeClass('sr-only')
      this.ui.$addAddress.removeClass('sr-only')
      this.ui.$addressGroup.removeClass('sr-only')
      this.ui.$checkAddAddress.prop('checked', true)

    }
  },

  onChangeCep(event) {
    event.preventDefault()

    $('[data-confirmation-alert-danger]').addClass('sr-only')
    $('[data-confirmation-alert-danger]').text('Por favor, preencha corretamente os campos abaixo.')
    
    $('[data-input-address]').prop('disabled', false)
    $('[data-input-address]').val('')
    $('[data-input-number]').prop('disabled', false)
    $('[data-input-number]').val('')
    $('[data-input-reference-point]').prop('disabled', false)
    $('[data-input-reference-point]').val('')
    $('[data-input-neighborhood]').prop('disabled', false)
    $('[data-input-neighborhood]').val('')
    $('[data-input-city]').prop('disabled', false)
    $('[data-input-city]').val('')
    $('[data-input-state]').prop('disabled', false)
    $('[data-input-state]').val('')
    $('[data-input-complement]').prop('disabled', false)
    $('[data-input-complement]').val('')

    var cep = this.ui.$cep.val().replace(/[^\d]+/g,'')
    var url = `https://viacep.com.br/ws/${cep}/json/`
    
    if (cep.length < 8) {
      $('[data-confirmation-alert-danger]').text('CEP inválido.')
      $('[data-confirmation-alert-danger]').removeClass('sr-only')
      $('[data-input-cep]').focus()
      $('[data-input-cep]').val('')
      return
    }

    $.ajax({
      url: url,
      dataType: 'jsonp',
      crossDomain: true,
      contentType: "application/json",
      success : function (json) {

        if(false == ('localidade' in json)) {
          $('[data-confirmation-alert-danger]').text('CEP inválido.')
          $('[data-confirmation-alert-danger]').removeClass('sr-only')
          $('[data-input-cep]').focus()
          $('[data-input-cep]').val('')
          return
        }

        if ("" != json.logradouro) {
          $('[data-input-address]').val(json.logradouro)
          $('[data-input-address]').prop('disabled', true)
        }

        if ("" != json.bairro) {
          $('[data-input-neighborhood]').val(json.bairro)
          $('[data-input-neighborhood]').prop('disabled', true)
        }

        if ("" != json.localidade) {
          $('[data-input-city]').val(json.localidade)
          $('[data-input-city]').prop('disabled', true)
        }

        if ("" != json.uf) {
          $('[data-input-state]').val(json.uf)
          $('[data-input-state]').prop('disabled', true)           
        }

        if ("" != json.complemento) {
          $('[data-input-complement]').val(json.complemento)
          $('[data-input-complement]').prop('disabled', true) 
        }

      }
    });
  },

  onHiddenModalSm(event) {
    event.preventDefault()

    $('[data-submit-ok]').val('');
    this.ui.$alert.addClass('sr-only')
    this.ui.$alertError.addClass('sr-only')

    this.ui.$subject.val('')
    this.ui.$profileLink.val('')
    this.ui.$image.val('')
  },

  onHiddenModal(event) {
    event.preventDefault()
    this.ui.$alert.addClass('sr-only')
    this.ui.$alertError.addClass('sr-only')
    this.ui.$phone.val('')
    this.ui.$phone.prop('disabled', false)
    this.ui.$phone.removeClass('sr-only')    
    this.ui.$cep.val('')
    this.ui.$cep.prop('disabled', false)
    this.ui.$cep.removeClass('sr-only')
    this.ui.$address.val('')
    this.ui.$address.prop('disabled', false)
    this.ui.$address.removeClass('sr-only')
    this.ui.$number.val('')
    this.ui.$number.prop('disabled', false)
    this.ui.$number.removeClass('sr-only')
    this.ui.$reference.val('')
    this.ui.$reference.prop('disabled', false)
    this.ui.$reference.removeClass('sr-only')
    this.ui.$neighborhood.val('')
    this.ui.$neighborhood.prop('disabled', false)
    this.ui.$neighborhood.removeClass('sr-only')
    this.ui.$city.val('')
    this.ui.$city.prop('disabled', false)
    this.ui.$city.removeClass('sr-only')
    this.ui.$state.val('')
    this.ui.$state.prop('disabled', false)
    this.ui.$state.removeClass('sr-only')
    this.ui.$complement.val('')
    this.ui.$complement.prop('disabled', false)
    this.ui.$complement.removeClass('sr-only')
    this.ui.$checkAddAddress.prop('checked', true)
  },

  onExchangeClick(event) {
    event.preventDefault() 

    if(1 == $('[feature-verify-enable]').val())
    {
        /**
         * Getting status exchange
         */
        
        const getStatusExchange = $.ajax({
          method: 'POST',
          url: `/exchange/verify-last-exchange`,
          contentType: 'application/json',
          data: JSON.stringify({
            _token: this.token,
            customer_id: this.ui.$customerId.val(),
          }),
        })

        getStatusExchange.done((data) => {

          if(data.data) {
            this.flowExchange(event);
          } else {            
            this.ui.$lastExchange.show();
            this.ui.$insufficientStamps.hide()
            this.ui.$enoughtContent.hide()
            this.ui.$insufficientContent.hide()
            this.ui.$socialNetwork.hide()
            this.ui.$modal.modal('show')  
          }
        })

        getStatusExchange.fail((error) => {
          console.log('Erro: ', error)
        })

    } else {
      this.flowExchange(event);
    }   
    
  },  

  flowExchange(event) {    

    this.ui.$insufficientStamps.hide()
    this.ui.$enoughtContent.hide()
    this.ui.$insufficientContent.hide()
    this.ui.$socialNetwork.hide()
    this.ui.$lastExchange.hide()

    const $btn   = $(event.currentTarget)
    
    $('[data-item-id]').val($.trim($btn.data('id')))
    $('[data-item-points]').val($.trim($btn.data('points')))

    const itemId = $('[data-item-id]').val()
    
    /**
     * Getting item points
     */
    const getItemPonts = $.ajax({
      method: 'POST',
      url: `/exchange/get-product-service`,
      contentType: 'application/json',
      data: JSON.stringify({
        _token: this.token,
        id: itemId,
      }),
    })

    getItemPonts.done((data) => {
      this.$itemPoints = parseInt(data.data.results.points)

      const itemPoints = this.$itemPoints
      
      /**
       * Getting customer points
       */

      const getCustomerPoints = $.ajax({
        method: 'POST',
        url: '/exchange/get-customer-points',
        contentType: 'application/json',
        data: JSON.stringify({
          _token: this.token,
        }),
      })

      getCustomerPoints.done((data) => {
        this.$customerPoints = parseInt(data.data)
        const customerPoints = this.$customerPoints
        
        if(1 == $('#stamps_required').val())
        {   
           /**
           * Verify new conditions for exchange
           * POINTS AND STAMPS REQUIRED           * 
           */

          const verifyStampsRequired = $.ajax({
            method: 'POST',
            url: `/exchange/verify-stamps-required`,
            contentType: 'application/json',
            data: JSON.stringify({
              _token: this.token,
              product_id: itemId,
              customer_id: this.ui.$customerId.val(),
              customer_points: customerPoints,
              item_points: itemPoints,
            }),
          })

          Swal.fire({
            title: 'Aguarde!',
            html: 'Carregando dados...',          
          })

          Swal.showLoading()

          verifyStampsRequired.done((data) => {  

            Swal.close()
            
            if(data.success)
            {
              //PEDIDO DE TROCA AUTORIZADO
              if(1 == this.$enabled.val()) 
              {
                var string = 'data-social-network' + $('[data-item-id]').val();

                if(($('['+ string +']').val()) && ('' != $('['+ string +']').val())) {
                  this.ui.$form.addClass('sr-only');
                  this.ui.$formSm.removeClass('sr-only');
                  this.ui.$textTitleExchangeOK.text('Por favor, preencha as informações sobre seu perfil');
                  $('[data-form-instructions]').text("Para a solicitação desta troca ser realizada são necessárias algumas informações. Basta preencher corretamente os campos abaixo.");                  
                  this.ui.$socialNetwork.show()
                  this.ui.$insufficientStamps.hide()
                  this.ui.$enoughtContent.hide()
                  this.ui.$insufficientContent.hide()
                  this.ui.$lastExchange.hide()
                  
                  this.$wrapUpload.removeClass('sr-only');
                  this.$wrapPreview.addClass('sr-only');

                } else {
                  
                  this.ui.$textTitleExchangeOK.text('Eba!!! Você tem os pontos e os selos suficientes para realizar a troca.');
                  this.ui.$insufficientStamps.hide()
                  this.ui.$enoughtContent.show()
                  this.ui.$insufficientContent.hide()
                  this.ui.$socialNetwork.hide()
                  this.ui.$lastExchange.hide()
                }
              }
              else {
                this.ui.$form.removeClass('sr-only');
                this.ui.$socialNetwork.hide()
                this.ui.$insufficientStamps.hide()
                this.ui.$enoughtContent.hide()
                this.ui.$insufficientContent.show() 
                this.ui.$lastExchange.hide()              
              }
            } 
            else 
            {    
              //PEDIDO DE TROCA NÃO AUTORIZADO   
              this.ui.$enoughtContent.hide()
              this.ui.$insufficientContent.hide()
              this.ui.$lastExchange.hide()

              var message = data.message;
              var stamps_in_progress = data.stamps;
              var title = data.title;

              //não tem selo cadastrado para o produto
              var condition1 = (0 == (data.stamps_required.length));
              var condition2 = (0 == (stamps_in_progress.length));

              var stamps_required = data.stamps_required;
              
              if(0 != stamps_in_progress.length) 
              {
                this.ui.$insufficientStamps.show()

                $('.data-no-stamps').children("div").remove();
                $('.data-no-stamps').hide();

                this.ui.$textNoStamps.text("Selos exigidos para troca deste produto:");
                this.ui.$textTitleVerify.text(title);
                this.ui.$textBodyVerify.text(message);
                
                $('.data-stamps-required').children("div").remove();
                $('.data-stamps-required').hide();                

                if(stamps_required.length == 2)
                { 
                  var class_stamp1 = '';
                  var class_stamp2 = '';

                  var text_stamp1 = '';
                  var text_stamp2 = '';

                  const verify1 = [];
                  const verify2 = [];

                  var class_text1 = '';
                  var class_text2 = '';

                  for (let i = 0; i < stamps_in_progress.length; i++) 
                  { 
                    if(stamps_required[0].id == stamps_in_progress[i].id) {
                      verify1.push(true);
                    } else {
                      verify1.push(false);
                    }

                    if(stamps_required[1].id == stamps_in_progress[i].id) {
                      verify2.push(true);
                    } else {
                      verify2.push(false);
                    }
                  }

                  if(verify1.includes(true)){
                      class_stamp1 = 'stamps_required_one';
                      text_stamp1 = 'Conquistar';
                      class_text1 = 'stamp_to_win1';
                  } else {
                      class_stamp1 = 'stamp_opacity_one';
                      text_stamp1 = 'Conquistado';
                      class_text1 = 'won_stamp1';
                  }

                  if(verify2.includes(true)){
                    class_stamp2 = 'stamps_required_two';
                    text_stamp2 = 'Conquistar';
                    class_text2 = 'stamp_to_win2';
                  } else {
                    class_stamp2 = 'stamp_opacity_two';
                    text_stamp2 = 'Conquistado';
                    class_text2 = 'won_stamp2';
                  }

                  $('.data-stamps-required').append(
                  $('<div />')
                  .addClass('col-3')
                  //adicionando seletores e seus valores
                  .attr('data-stamp', '')
                  .attr('data-stampid', stamps_required[0].id)
                  .attr('data-requiredamount', stamps_required[0].required_amount)
                  .attr('data-stamptype', stamps_required[0].type)
                  .attr('data-stampicon', stamps_required[0].icon)
                  .attr('data-stamptitle', stamps_required[0].title)
                  //img da stamp
                  .append($('<img />')
                  .attr('src', $('#sweetmedia').val() + '/storage/' + stamps_required[0].icon)
                  .addClass('img')
                  .addClass(class_stamp1))
                   //span com informação de selo conquistado ou não
                  .append($('<span />')
                  .text(text_stamp1)
                  .addClass(class_text1))
                  ).show();

                  $('.data-stamps-required').append(
                  $('<div />')
                  .addClass('col-3')
                  //adicionando seletores e seus valores
                  .attr('data-stamp', '')
                  .attr('data-stampid', stamps_required[1].id)
                  .attr('data-requiredamount', stamps_required[1].required_amount)
                  .attr('data-stamptype', stamps_required[1].type)
                  .attr('data-stampicon', stamps_required[1].icon)
                  .attr('data-stamptitle', stamps_required[1].title)
                  //img da stamp
                  .append($('<img />')
                  .attr('src', $('#sweetmedia').val() + '/storage/' + stamps_required[1].icon)
                  .addClass('img')
                  .addClass(class_stamp2))
                   //span com informação de selo conquistado ou não
                  .append($('<span />')
                  .text(text_stamp2)
                  .addClass(class_text2))
                  ).show();
                     
                } 
                else 
                {
                  for (let index = 0; index < stamps_required.length; index++) 
                  {     
                    var class_stamp = '';
                    var text_stamp = '';
                    const verify = [];
                    for (let i = 0; i < stamps_in_progress.length; i++) 
                    { 
                      if(stamps_required[index].id == stamps_in_progress[i].id) {
                        verify.push(true);
                      } else {
                        verify.push(false);
                      }
                    }

                    var class_text = '';
                    //verificação necessária para não sobrescrever o valor de 'class_stamp'
                    if(verify.includes(true)){
                      class_stamp = 'stamps_required';
                      text_stamp = 'Conquistar';
                      class_text = 'stamp_to_win';
                    } else {
                      class_stamp = 'stamp_opacity';
                      text_stamp = 'Conquistado';
                      class_text = 'won_stamp';
                    }
                  
                    $('.data-stamps-required').append(
                    $('<div />')
                    .addClass('col-3')
                    //adicionando seletores e seus valores
                    .attr('data-stamp', '')
                    .attr('data-stampid', stamps_required[index].id)
                    .attr('data-requiredamount', stamps_required[index].required_amount)
                    .attr('data-stamptype', stamps_required[index].type)
                    .attr('data-stampicon', stamps_required[index].icon)
                    .attr('data-stamptitle', stamps_required[index].title)
                    //img da stamp
                    .append($('<img />') 
                    .attr('src', $('#sweetmedia').val() + '/storage/' + stamps_required[index].icon)
                    .addClass('img')
                    .addClass(class_stamp))
                    //span com informação de selo conquistado ou não
                    .append($('<span />')
                    .text(text_stamp)
                    .addClass(class_text))
                    ).show();
                  } 
                }
              } 
              //já tem TODOS os selos exigidos para troca
              else 
              {                
                $('.data-stamps-required').children("div").remove();
                $('.data-stamps-required').hide();

                $('.data-no-stamps').children("div").remove();
                $('.data-no-stamps').hide();

                if(stamps_required.length == 2)
                { 
                  $('.data-stamps-required').append(
                  $('<div />')
                  .addClass('col-3')
                  //adicionando seletores e seus valores
                  .attr('data-stamp', '')
                  .attr('data-stampid', stamps_required[0].id)
                  .attr('data-requiredamount', stamps_required[0].required_amount)
                  .attr('data-stamptype', stamps_required[0].type)
                  .attr('data-stampicon', stamps_required[0].icon)
                  .attr('data-stamptitle', stamps_required[0].title)
                  //img da stamp
                  .append($('<img />')
                  .attr('src', $('#sweetmedia').val() + '/storage/' + stamps_required[0].icon)
                  .addClass('img')
                  .addClass('stamp_opacity_one'))
                  //span com informação de selo conquistado ou não
                  .append($('<span />')
                  .text('Conquistado')
                  .addClass('won_stamp1'))
                  ).show();

                  $('.data-stamps-required').append(
                  $('<div />')
                  .addClass('col-3')
                  //adicionando seletores e seus valores
                  .attr('data-stamp', '')
                  .attr('data-stampid', stamps_required[1].id)
                  .attr('data-requiredamount', stamps_required[1].required_amount)
                  .attr('data-stamptype', stamps_required[1].type)
                  .attr('data-stampicon', stamps_required[1].icon)
                  .attr('data-stamptitle', stamps_required[1].title)
                  //img da stamp
                  .append($('<img />')
                  .attr('src', $('#sweetmedia').val() + '/storage/' + stamps_required[1].icon)
                  .addClass('img')
                  .addClass('stamp_opacity_two'))
                  //span com informação de selo conquistado ou não
                  .append($('<span />')
                  .text('Conquistado')
                  .addClass('won_stamp_two'))
                  ).show();
                     
                } 
                //tem mais de duas stamps exigidas
                else
                {
                  for (let index = 0; index < stamps_required.length; index++) 
                  {                  
                    $('.data-stamps-required').append(
                    $('<div />')
                    .addClass('col-3')
                    //adicionando seletores e seus valores
                    .attr('data-stamp', '')
                    .attr('data-stampid', stamps_required[index].id)
                    .attr('data-requiredamount', stamps_required[index].required_amount)
                    .attr('data-stamptype', stamps_required[index].type)
                    .attr('data-stampicon', stamps_required[index].icon)
                    .attr('data-stamptitle', stamps_required[index].title)
                    //img da stamp
                    .append($('<img />')
                    .attr('src', $('#sweetmedia').val() + '/storage/' + stamps_required[index].icon)
                    .addClass('img')
                    .addClass('stamp_opacity'))
                    //span com informação de selo conquistado ou não
                    .append($('<span />')
                    .text('Conquistado')
                    .addClass('won_stamp'))
                    ).show();
                  } 
                }                

                this.ui.$insufficientStamps.show()
                
                this.ui.$textTitleVerify.text(title);
                this.ui.$textBodyVerify.text(message);
                this.ui.$textNoStamps.text("");
                this.ui.$textBtnVerify.text("Ok, vamos lá!");
              }
            }
          })
        }//verify env

        if(0 == $('#stamps_required').val())
        {
            if(1 == this.$enabled.val() && (customerPoints >= itemPoints)) 
            {
              this.ui.$insufficientStamps.hide()
              this.ui.$enoughtContent.show()
              this.ui.$insufficientContent.hide()
              this.ui.$lastExchange.hide()
            }
            else 
            {
              this.ui.$insufficientStamps.hide()
              this.ui.$insufficientContent.show()
              this.ui.$enoughtContent.hide()
              this.ui.$lastExchange.hide()
            }
        }           
      })

      const customerId = this.ui.$customerId .val()
      const url = `/exchange/get-last-address/${customerId}`
  
      const getLastAddress = $.ajax({
        method: 'GET',
        url: url,
        contentType: 'application/json',
        data: JSON.stringify({
          _token : this.token,
        }),
      })  
  
      getLastAddress.done((data) => {
        if(data.success) {  
          this.ui.$useLastAddressGroup.removeClass('sr-only')
          this.ui.$lastAddress.text(
            data.data.address + 
            ', nº ' + 
            data.data.number + 
            ' - ' + 
            data.data.neighborhood + 
            ', ' + 
            data.data.city + 
            ' ' + 
            data.data.state + 
            '.')
  
            this.lastAddress.city = data.data.city
            this.lastAddress.state = data.data.state
            this.lastAddress.neighborhood = data.data.neighborhood
            this.lastAddress.number = data.data.number
            this.lastAddress.address = data.data.address
            this.lastAddress.cep = data.data.cep
            this.lastAddress.reference = data.data.reference_point
  
            this.ui.$checkUseLastAddress.prop('checked', true)
  
            this.onChangeCheckLastAddress()
        }
      })
  
      getLastAddress.fail((error) => {
        console.log('Erro: ', error)
      })
  
      this.ui.$form.removeClass('sr-only')
      this.ui.$instructions.removeClass('sr-only')

      var string = 'data-social-network' + $('[data-item-id]').val();

      if(($('['+ string +']').val()) && ('' != $('['+ string +']').val())) {
        this.ui.$modalSm.modal('show')  
      } else {
        this.ui.$modal.modal('show')  
      }
         
    })
  },

  onConfirmSmClick(event) {
    event.preventDefault()

    var string = 'data-social-network' + $('[data-item-id]').val();
    var social = $('['+ string +']').val();

    var products_services_id = $('[data-item-id]').val();
    var social_media = social;
    var customers_id = this.ui.$customerId.val()
    var subject = this.ui.$subject.val() ? this.ui.$subject.val() : null;
    var profile_link = this.ui.$profileLink.val() ? this.ui.$profileLink.val() : null;
    var points = $('[data-item-points]').val();
    var profile_picture = this.$path.val() ? this.$path.val() : null;

    if('entendi' == $('[data-submit-ok]').val())
    {
      const saving_sm = $.ajax({
        method: 'POST',
        url: '/exchange/social-network-exchange',
        contentType: 'application/json',
        data: JSON.stringify({
          _token               : this.token,
          products_services_id : products_services_id,
          social_media         : social_media,
          customers_id         : customers_id,
          subject              : subject,
          profile_link         : profile_link,
          points               : points,
          profile_picture      : profile_picture,
        }),
      }) 

      saving_sm.done((data) => {
       if (data.success) {
        $('[data-form-instructions]').addClass("sr-only");
        this.ui.$alert.text('Pronto! Seus dados foram enviados para nossa equipe! Obrigado!');
        this.ui.$alert.removeClass('sr-only');

        this.ui.$btnConfirmSm.prop('disabled', true);
        this.ui.btnCancel.prop('disabled', true);

        window.setTimeout(function(){
          location.reload();
        }, 4000);
        
       }
      
      })

      saving_sm.fail((error) => {
        console.log('Erro: ', error)
      })  

      
    } else {    

      if((null === profile_link) || (null === subject) || (null === profile_picture)) {
        this.ui.$alertError.text('Por favor, informe todos os dados necessários.');
        this.ui.$alertError.removeClass('sr-only');
      } else {
        if(profile_link.indexOf(social_media.toLowerCase()) != -1) {
          this.ui.$alertError.addClass('sr-only');

          this.ui.$textTitleExchangeOK.text('Confirmar troca de pontos?');
          $('[data-form-instructions]').text("Ao confirmar, seus dados serão analisados por nossa equipe. Caso a troca não seja autorizada, seus pontos voltarão para a sua conta. Se você optar por desistir, nada acontecerá com seu saldo.");
          $('[btn-cancel-sm]').removeClass("sr-only");     
          $('[data-submit-ok]').val('entendi') ;
          this.ui.$formSm.addClass("sr-only");
                
        } else { 

          var string = 'data-social-network' + $('[data-item-id]').val();
          var social = $('['+ string +']').val();

          this.ui.$alertError.removeClass('sr-only');
          this.ui.$alertError.text('O link informado não pertence ao ' + social + '.');
        }
      }
  }

    

  },

  onConfirmClick(event) {
    event.preventDefault()

    if(this.labels.success === this.ui.$btnConfirm.text()){
      this.ui.$modal.modal('hide')
      this.ui.$btnConfirm.text(this.labels.confirm)
      this.ui.$phone.prop('disabled', false)
      this.ui.$phone.val('')
      this.ui.$cpf.prop('disabled', false)
      this.ui.$cpf.val('')
      this.ui.$cep.prop('disabled', false)
      this.ui.$cep.val('')
      this.ui.$address.prop('disabled', false)
      this.ui.$address.val('')
      this.ui.$number.prop('disabled', false)
      this.ui.$number.val('')
      this.ui.$reference.prop('disabled', false)
      this.ui.$reference.val('')
      this.ui.$neighborhood.prop('disabled', false)
      this.ui.$neighborhood.val('')
      this.ui.$city.prop('disabled', false)
      this.ui.$city.val('')
      this.ui.$state.prop('disabled', false)
      this.ui.$state.val('')
      this.ui.$complement.prop('disabled', false)
      this.ui.$complement.val('')
      return
    }
    
    var actualPoints = $('[data-points-total]').text().replace(/[^\d]+/g,'')
    actualPoints = parseInt(actualPoints, 10)
    var itemPoints  = this.$itemPoints

    const cpf = $('[data-input-cpf]').val().replace(/[^\d]+/g,'')
    const cep = $('[data-input-cep]').val().replace(/[^\d]+/g,'')
    
    if(this.ui.$phone.val() === '') {
      this.ui.$phone.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$cpf.val() === '') {
      this.ui.$cpf.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$cep.val() === '') {
      this.ui.$cep.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$address.val() === '') {
      this.ui.$address.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$number.val() === '') {
      this.ui.$number.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$reference.val() === '') {
      this.ui.$reference.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$neighborhood.val() === '') {
      this.ui.$neighborhood.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$city.val() === '') {
      this.ui.$city.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    if(this.ui.$state.val() === '') {
      this.ui.$state.focus()
      this.ui.$alertError.removeClass('sr-only')
      return
    }

    var updateCustomerAddress = false;

    if(this.ui.$checkAddAddress.is(':checked')) {
      updateCustomerAddress = true
    }

    const itemId = $('[data-item-id]').val()

    const saving = $.ajax({
      method: 'POST',
      url: '/exchange/checkout',
      contentType: 'application/json',
      data: JSON.stringify({
        _token         : this.token,
        customer_id    : this.ui.$customerId.val(),
        item_points    : this.$itemPoints,
        item_id        : itemId,
        phone          : this.ui.$phone.val(),
        cpf            : cpf,
        cep            : cep,
        address        : this.ui.$address.val(),
        number         : this.ui.$number.val(),
        reference      : this.ui.$reference.val(),
        neighborhood   : this.ui.$neighborhood.val(),
        city           : this.ui.$city.val(),
        state          : this.ui.$state.val(),
        complement     : this.ui.$complement.val(),
        update_address : updateCustomerAddress,
      }),
    })

    saving.done((data) => {
      if (false === data.success) {
        this.ui.$alertError.removeClass('sr-only')
        this.ui.$phone.focus()
        return
      }
      var diff = (actualPoints - itemPoints)
      diff = diff.toLocaleString('pt-BR')
      
      $('[data-points-total]').text(diff)
      this.ui.$phone.prop('disabled', true)
      this.ui.$cpf.prop('disabled', true)
      this.ui.$cep.prop('disabled', true)
      this.ui.$address.prop('disabled', true)
      this.ui.$number.prop('disabled', true)
      this.ui.$reference.prop('disabled', true)
      this.ui.$neighborhood.prop('disabled', true)
      this.ui.$city.prop('disabled', true)
      this.ui.$state.prop('disabled', true)
      this.ui.$complement.prop('disabled', true)
      this.ui.$alertError.addClass('sr-only')
      this.ui.$alert.removeClass('sr-only')
      
      this.ui.$form.addClass('sr-only')
      this.ui.$formSm.addClass('sr-only')
      this.ui.$instructions.addClass('sr-only')
      this.ui.$useLastAddressGroup.addClass('sr-only')
      
      this.ui.$btnConfirm.text(this.labels.success)
      this.ui.$btnConfirm.addClass('sr-only')
      
      window.setTimeout(function(){
        location.reload();
      }, 2000);
      
    })

    saving.fail((error) => {
      console.log('Erro: ', error)
    })    
    
  },  


}

const Exchange = () => {
  ExchangePoints.start()
}

export default Exchange