const PointsLogin = {
    start() {
        this.token       = $('meta[name="csrf-token"]').attr('content');
        
        this.$modal      = $('[data-login-points-modal]');
        this.$titleModal = $('[login-points-title]');
      
        if(1 == $('[login-points]').val()) {
          this.verifyLoginPoints();
        } 
        
        this.bind()
    },

    bind() {
        //this.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
    },

    verifyLoginPoints() { 

        const saving = $.ajax({
            method: 'POST',
            url: '/customer-login-points/create',
            contentType: 'application/json',
            data: JSON.stringify({
              _token : this.token,
              customers_id: $('[data-customer-id]').val(),
            }),
          })
  
          saving.done((data) => {
           
            if(data.success) {
                this.$titleModal.text('Ebaaa! Parabéns, você ganhou 20 pontos.');
                this.$modal.modal('show'); 

                $('[data-points-total]').text(data.data);
            }
          })
    
          saving.fail((error) => {
            console.log('Erro: ', error)
          }) 
        
     },

}

const LoginPoints = () => {
    PointsLogin.start()
}
    
export default LoginPoints;