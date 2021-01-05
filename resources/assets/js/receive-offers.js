const OffersReceive = {
    start() {
        this.token    = $('meta[name="csrf-token"]').attr('content');
        
        this.$modal             = $('[data-receive-offers-modal]');
        this.$alert             = this.$modal.find('[data-receive-alert]');
        this.$alertError        = this.$modal.find('[data-receive-alert-danger]');
        
        this.$btnReceiveOffers  = $('[data-receive-offers]');
        
        this.$nextCondition     = $('[btn-receive-next]');
        this.$textCondition     = $('[data-receive-conditions]');
        this.$acceptedCondition = $('[btn-receive-text]');
        this.$indexCondition    = 0;
      
        this.bind()
    },
  
    bind() {
      this.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
      this.$btnReceiveOffers.on('click', this.onConfirmClick.bind(this))
      this.$nextCondition.on('click', this.onNextConditionClick.bind(this))
      this.$acceptedCondition.on('click', this.onAcceptedConditionClick.bind(this))
    },

    onAcceptedConditionClick(event) {
        event.preventDefault()

        $('[title-conditions]').text('PARABÉNS!!!')
        this.$acceptedCondition.addClass('sr-only');
        this.$textCondition.addClass('sr-only');

        const saving = $.ajax({
          method: 'POST',
          url: '/receive-offers/update',
          contentType: 'application/json',
          data: JSON.stringify({
            _token : this.token,
          }),
        })

        saving.done((data) => {
          this.$alert.removeClass('sr-only');

          window.location.href = "/";
        })
  
        saving.fail((error) => {
          console.log('Erro: ', error)
        }) 
    },

    onNextConditionClick(event) {
        event.preventDefault()

        if(0 == this.$indexCondition) {
            this.$textCondition.text('Depois que habilitar, já começará a receber as promoções em seu e-mail e, caso estas te incomode no futuro devido a quantidade e conteúdo, NÃO MANDE PARA SPAM! Entre em contato com a equipe de suporte da Sweet e peça a remoção desta lista. Caso mova os e-mails para Spam nós retiraremos os 60 pontos ganhos de seu saldo.');
        } else {
            this.$textCondition.text('Se ainda tem dúvidas, não exite em mandar um e-mail para nossa equipe de suporte através do e-mail contato@sweetpanels.com. Teremos prazer em atendê-lo e sanar suas dúvidas.');
            $('[btn-receive-next]').addClass('sr-only');
            $('[btn-receive-text]').removeClass('sr-only');
        } 

        this.$indexCondition++; 
    },

    onConfirmClick(event) {
        event.preventDefault()
    
        this.$modal.modal('show');
    },

    onHiddenModal(event) {
        this.$alert.addClass('sr-only')
        this.$alertError.addClass('sr-only')

        this.$indexCondition = 0;
        this.$textCondition.text('Ao aceitar receber emails das melhores ofertas e promoções de nossos parceiros, ' +
        'você tem acesso a uma série de benefícios exclusivos mas, não ganhará nenhum ponto por isso. ' +
        'Você só ganha 60 pontos no momento que habilitar receber estes e-mails.');

        $('[btn-receive-text]').addClass('sr-only');
        $('[btn-receive-next]').removeClass('sr-only');
    },
}

const ReceiveOffers = () => {
    OffersReceive.start()
}
    
export default ReceiveOffers;