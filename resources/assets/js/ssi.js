const Ssi = {
  start () {
    this.$btnResearch = $('[data-btn-research]');
    this.$modal       = $('[data-modal-ssi]');

    this.$comingFromEmail = $('[data-coming-from-email]').val();
    
    if (this.$comingFromEmail.includes("http")) {
      this.emailClick();
    }

    this.bind();
  },

  bind() {
    this.$btnResearch.on('click', this.btnResearchClick.bind(this));
  },

  emailClick () {
    const $link = this.$comingFromEmail;

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
        window.open($link, '_blank');
      }
    });
  },

  btnResearchClick(event) {
    event.preventDefault();

    const $btn  = $(event.currentTarget);
    const $link = $.trim($btn.data('link'));
    
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
        window.open($link, '_blank');
      }
    });
  },

  getText () {
    return "<hr>" + 
      "<p style='text-align: justify; font-size: 13px;'>" +
        "<strong>1.</strong> As pesquisas dessa seção são postadas periodicamente pelo nosso cliente " + 
        "externo e não temos controle sobre sua disponibilidade no momento que for acessada." + 
      "</p>" +      
      "<p style='text-align: justify; font-size: 13px;'>" +
        "<strong>2.</strong> Caso o período da campanha já tenha encerrado, você será redirecionado " + 
        "ao portal e <strong>não receberá nenhuma pontuação.</strong> " + 
      "</p>" + 
      "<p style='text-align: justify; font-size: 13px;'>" +
        "<strong>3.</strong> Pode ser identificado ao longo da pesquisa que você não possui perfil " +
        "e serão atribuídos apenas <strong>10 pontos</strong> ao seu saldo." +  
      "</p>" +
      "<p style='text-align: justify; font-size: 13px;'>" +
        "<strong>4.</strong> Você pode possuir perfil, entretanto pode ocorrer de o grupo de respondentes para a " +
        "pesquisa já possuir respostas suficientes. Serão atribuídos <strong>10 pontos</strong> ao seu saldo." +  
      "</p>" + 
      "<p style='text-align: justify; font-size: 13px;'>" +
        "<strong>5.</strong> Só serão atribuídos os <strong>100 pontos</strong> caso seu perfil seja aceito e " +
        "a pesquisa ainda não tenha atingido o número de respostas suficientes." +   
      "</p>";
  },

}

const SsiResearch = () => {
  Ssi.start();
}

export default SsiResearch;