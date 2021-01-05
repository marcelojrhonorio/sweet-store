const ListVipSweet = {
    start() {
        this.token    = $('meta[name="csrf-token"]').attr('content');
        
        this.$modal             = $('[data-list-vip-modal]');
        this.$alert             = this.$modal.find('[data-list-vip-alert]');
        this.$alertError        = this.$modal.find('[data-list-vip-alert-danger]');
               
        this.$btnListVip        = $('[data-list-vip]');

        this.$updatePhone       = 0;
        this.$olderPhone        = '';

        this.$phone             = this.$modal.find('[data-input-phone]'); 
        this.$name              = this.$modal.find('[data-input-name]');

        this.$btnConfirm        = $('[btn-send-list-vip]');

        this.bind() 
        this.applyMasks();  
    },

    applyMasks: function() {
        this.$phone.mask('(00)00000-0000')
    },      

    bind() {
        this.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this));
        this.$btnListVip.on('click', this.onListVipClick.bind(this));
        this.$btnConfirm.on('click', this.onConfirmClick.bind(this))
    },

    onListVipClick(event) {
        event.preventDefault()
    
        this.$modal.modal('show');
    },

    onHiddenModal(event) {
        event.preventDefault()
        
        this.$phone.val('');
        this.$name.val('');

        this.$alertError.addClass('sr-only');
    },

    onConfirmClick(event) {
        event.preventDefault()

        if('' == this.$phone.val() || '' == this.$name.val()) {
            this.$alertError.removeClass('sr-only');
            this.$alertError.text('Todo os campos são obrigatórios!');
        } else {
            this.$alertError.addClass('sr-only');
        }

        this.verifyPhone(this.$phone.val());
    },

    verifyPhone(phone) {

        const verify = $.ajax({
            method: 'POST',
            url: '/vip-list/verify',
            contentType: 'application/json',
            data: JSON.stringify({
              _token  : this.token,
              phone   : phone,
            }),
          })

          verify.done((data) => {

            if (data.success) {     

                Swal.fire({
                    title: 'Já existe um cadastro com o número informado',
                    text: "Deseja informar outro número?",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não'
                  }).then((result) => {
                    if (!result.value) {                     
                        this.$modal.modal('hide');
                        this.$updatePhone = 0;
                    } else {
                        this.$olderPhone = this.$phone.val();
                        this.$updatePhone = 1;
                        this.$phone.val('');
                        this.$phone.focus();
                    }
                  })
              
            } else {
               
                if(0 == this.$updatePhone) 
                {
                    const saving = $.ajax({
                        method: 'POST',
                        url: '/vip-list/create',
                        contentType: 'application/json',
                        data: JSON.stringify({
                          _token  : this.token,
                          phone   : this.$phone.val(),
                          name    : this.$name.val(),
                        }),
                      })
                
                      saving.done((data) => {
            
                        if (data.success) {
            
                        this.$alert.removeClass('sr-only');

                        this.$phone.val('');
                        this.$name.val('');     
                                    
                        window.setTimeout(function(){                          
                          window.open('https://api.whatsapp.com/send?phone=5511957865532&text=Olá,%20gostaria%20de%20fazer%20parte%20da%20Lista%20Vip%20da%20Sweet', '_blank');
                        }, 2000);

                        this.$modal.modal('hide');
                          
                        }            
                        
                      })
                  
                      saving.fail((error) => {
                        console.log('Erro: ', error)
                      }) 

                } else {

                    const updating = $.ajax({
                        method: 'POST',
                        url: '/vip-list/update',
                        contentType: 'application/json',
                        data: JSON.stringify({
                          _token  : this.token,
                          phone   : this.$phone.val(),
                          name    : this.$name.val(),
                          older_p : this.$olderPhone,
                        }),
                      })
                
                      updating.done((data) => {
            
                        if (data.success) {
            
                        this.$alert.removeClass('sr-only');                        

                        window.setTimeout(function(){
                            window.open('https://api.whatsapp.com/send?phone=5511957865532&text=Olá,%20gostaria%20de%20fazer%20parte%20da%20Lista%20Vip%20da%20Sweet', '_blank');
                        }, 2000);

                        this.$modal.modal('hide');
                          
                        }            
                        
                      })
                  
                      updating.fail((error) => {
                        console.log('Erro: ', error)
                      })
                }
                 
            }           
            
          })
      
          verify.fail((error) => {
            console.log('Erro: ', error)
          })  
    },
    
}

const VipListSweet = () => {
    ListVipSweet.start()
}
    
export default VipListSweet;
