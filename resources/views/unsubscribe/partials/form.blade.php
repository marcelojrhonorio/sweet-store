<form class="form-horizontal" style="padding-bottom: 70px;">
  <div class="form-group">
    <div class="row">

      <h2>
        <strong>
          Que pena! 
          <img 
            src="https://images-na.ssl-images-amazon.com/images/I/41-j4TYiqmL.png" 
            style="width: 30px;"
          >
        </strong>
      </h2>

      <br>
      <br>
      
      <p>
        Sentimos por ter vindo até essa página...
        Mas, vamos lá: preencha as informações abaixo para continuar.
      </p>
    </div>  

    <div>
    
    </div>

    <div class="alert alert-warning sr-only" data-form-alert>
    </div>
    
    <div class="row">    
      <p><strong>Para que possamos melhorar nossos serviços, qual motivo o fez querer descadastrar-se?</strong></p>
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="reason-lots-of-emails"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="1"
            data-form-lots-of-emails
          >
          <label class="form-check-label" for="reason-lots-of-emails">
            Estou recebendo uma quantidade muito alta de e-mails
          </label> 
        </div>
      </div>
    </div>

    <div class="row">    
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="points-opportunities"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="2"
            data-form-points-opportunities
          >
          <label class="form-check-label" for="points-opportunities">
            Vejo poucas oportunidades de ganhar pontos
          </label> 
        </div>
      </div>
    </div>

    <div class="row">      
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="technical-problems"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="3"
            data-from-technical-problems
          >
          <label class="form-check-label" for="technical-problems">
            O site passa sempre por problemas técnicos
          </label> 
        </div>         
      </div>
    </div>

    <div class="row">    
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="dissatisfied"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="4"
            data-from-dissatisfied
          >
          <label class="form-check-label" for="dissatisfied">
            Estou insatisfeito(a) com o atendimento
          </label> 
        </div>         
      </div>
    </div>

    <div class="row">    
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="not-registered"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="5"
            data-from-not-registered
          >
          <label class="form-check-label" for="not-registered">
            Não lembro de ter sido cadastrado(a) nesse site
          </label> 
        </div>         
      </div>
    </div>

    <div class="row">    
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="not-interested"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="6"
            data-form-not-interested
          >
          <label class="form-check-label" for="not-interested">
            Não tenho mais interesse
          </label> 
        </div>         
      </div>
    </div>

    <div class="row">    
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="other"
            class="form-check-input"
            name="unsubscribe-reason"
            type="checkbox"
            value="7"
            data-form-other
          >
          <label class="form-check-label" for="other">
            Outro.
          </label> 
        </div>     
      </div>
    </div>

    <div class="row sr-only" data-form-other-reason-wrapper>
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-group">
          <input type="email" class="form-control" placeholder="Outro motivo" data-form-other-reason>
        </div>
      </div>
    </div>    

    <hr>

    <div class="row">
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="delete-account"
            class="form-check-input"
            name="final-unsubscribe"
            type="radio"
            value="delete_account"
            data-form-delete-account
          >
          <label class="form-check-label" for="delete-account">
            Excluir minha conta <br>
            <span style="font-size: 10px">
              Você não terá mais acesso ao portal e 
              também será removido(a) de nossa lista de e-mails.
            </span>
          </label> 
        </div>         
      </div>
    </div>

    <div class="row">
      <div class="col-12" style="padding-bottom: 20px;">
        <div class="form-check form-check-inline">
          <input
            id="unsubscribe-emails"
            class="form-check-input"
            name="final-unsubscribe"
            type="radio"
            value="unsubscribe_emails"
            data-form-unsubscribe-emails
          >
          <label class="form-check-label" for="unsubscribe-emails">
            Apenas me desinscrever na lista de e-mails da Sweet <br>
            <span style="font-size: 10px">
              Você deixará de receber pesquisas e ofertas com pontuação por e-mail,
              mas permanecerá com acesso ao portal.
            </span>
          </label> 
        </div>         
      </div>
    </div>   
  
    <div class="form-group sr-only" data-form-suggestion-wrapper>
      <label for="suggestion">Deixe-nos uma sugestão</label>
      <textarea class="form-control" id="suggestion" rows="3" data-form-suggestion></textarea>
    </div>

    <div class="alert alert-warning sr-only" data-form-alert>
    </div>

    <div class="sr-only">
      {{ csrf_field() }}
      <input type="hidden" name="_method" value="PUT">
    </div>
    <div class="form-group d-flex">
      <div class="row">
        <div class="col-md-6">
          <button class="btn btn-info" type="submit" data-btn-unsubscribe>
            Finalizar
          </button>        
        </div>
      </div>
    </div>

  </div>
</form>