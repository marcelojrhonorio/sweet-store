<div class="modal-header">
  <h5 class="modal-title" data-title-exchange>
    Eba!!! Você tem pontos suficientes para trocar.
  </h5>
  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert>
Estamos passando por problemas técnicos e por isso o processamento da troca pode demorar alguns dias. 
Por favor aguarde. Em breve retornaremos para confirmar as informações!
</div>
<div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert-danger>
  Por favor, preencha corretamente os campos abaixo.
</div>
 <p data-form-instructions>
  Para o processo de troca ser realizado é necessário algumas informações adicionais. 
  Basta preencher corretamente os campos abaixo e o produto será enviado para seu endereço.
 </p>

 <div class="alert alert-warning alert-dismissible fade show sr-only" data-group-last-address>
  <label>
    <input type="radio" name="address" value="use-last-address" data-check-last-address> 
    Enviar para <strong data-last-address></strong>  
  </label>
  <label>
    <input type="radio" name="address" value="create-new-address" data-check-new-address> 
    Cadastrar um novo endereço.
  </label>
 </div>

 <input type="hidden" value='' data-item-id>

 <form class="form-horizontal" data-form-exchange>
    <div class="row">
      <div class="form-group col-md-6">
        <input
          id="customer-phone"
          class="form-control"
          name="customer-phone"
          type="tel"
          placeholder="Celular"
          data-input-phone
        >
      </div>
      <div class="form-group col-md-6">
        <input
          id="customer-cpf"
          class="form-control"
          name="customer-cpf"
          type="text"
          placeholder="CPF"
          data-input-cpf
        >      
      </div>       
    </div>

    <div class="row" data-address-group>
      <div class="form-group col-md-4">
        <input
          id="customer-cep"
          class="form-control"
          name="customer-cep"
          type="text"
          placeholder="CEP"
          data-input-cep
        >      
      </div>
      <div class="form-group col-md-8">
        <input
          id="customer-address"
          class="form-control"
          name="customer-address"
          type="text"
          placeholder="Endereço"
          data-input-address
        >
      </div>   
    </div>

    <div class="row" data-address-group>
      <div class="form-group col-md-3">
        <input
          id="customer-number"
          class="form-control"
          name="customer-number"
          type="text"
          placeholder="Nº"
          data-input-number
        >      
      </div>
      <div class="form-group col-md-9">
        <input
          id="customer-reference-point"
          class="form-control"
          name="customer-reference-point"
          type="text"
          placeholder="Ponto de referência"
          data-input-reference-point
        >      
      </div>
    </div>

    <div class="row" data-address-group>
      <div class="form-group col-md-6">
        <input
          id="customer-neighborhood"
          class="form-control"
          name="customer-neighborhood"
          type="text"
          placeholder="Bairro"
          data-input-neighborhood
        >       
      </div>
      <div class="form-group col-md-6">
        <input
          id="customer-city"
          class="form-control"
          name="customer-city"
          type="text"
          placeholder="Cidade"
          data-input-city
        >       
      </div>
    </div>

    <div class="row" data-address-group>
      <div class="form-group col-md-4">
        <input
          id="customer-state"
          class="form-control"
          name="customer-state"
          type="text"
          placeholder="UF"
          data-input-state
        >
      </div>
      <div class="form-group col-md-8">
        <input
          id="customer-complement"
          class="form-control"
          name="customer-complement"
          type="text"
          placeholder="Complemento"
          data-input-complement
        >
      </div>      
    </div>

    <label data-label-add-address style="font-size: 13px; color:#595959">
      <input type="checkbox" name="add-new-address" value="add-new-address" checked data-check-add-address> 
      Usar este endereço nas minhas informações cadastrais. 
    </label>

 </form>
</div>
<div class="modal-footer">
  <button class="btn btn-info" type="button" data-btn-confirm>
    Confirmar!
  </button>
</div>