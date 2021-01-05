@php
  $gender = session('gender') ?? old('gender');
@endphp

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('success'))
<div class="alert alert-success">
  {{ session('success') }}
</div>
@endif

<input type="hidden" value="{{ env('PROFILE_PICTURE') }}" data-profile-picture>
<input type="hidden" value="{{ $qtdCustomersInterests }}" data-qtd-customers-interests>

@if (session('alert'))
<div class="alert alert-{{ session('alert.type') }} alert-dismissible fade show" role="alert" style="text-align: center" data-form-profile-alert-session-up>
 <strong> {{ session('alert.message1') }}  <br><hr> </strong>
          {{ session('alert.message2') }} 
 <strong> {{ session('alert.message3') }} </strong>
 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
 </button>
</div>
@endif

<div class="alert alert-success alert-dismissible fade show sr-only" data-confirmation-alert-up>
Obrigado! Suas informações foram atualizadas com sucesso. <br>
</div>

<div class="alert alert-warning sr-only" data-updated-alert-up>
Nenhum dado foi alterado. <br>
</div>

<div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert-danger-up>
  Por favor, preencha corretamente os campos obrigatórios.
</div>

<form class="form-horizontal" method="post" enctype="multipart/form-data" data-form-profile-up>
 	
 @if(env('PROFILE_PICTURE'))
  <div class="form-group">   
    <input type="hidden" name="customer-picture" id="customer-picture" value="{{ $usr->avatar }}" customer-picture/> 
    <input type="hidden" name="customer-avatar-mode" id="customer-avatar-mode" value="{{ env('FIRST_CUSTOMER_AVATAR') }}" customer-avatar-mode/> 
   
    <div class="box-upload-image">
      <label class="label-avatar" for='avatar'>
        <img src="{{ asset($user['avatar']) }}" class="avatar-customer" select-avatar />  
        @if(session('avatar'))     
          <div class="label-upload sr-only" login-face>
        @else
          <div class="label-upload" login-face>
        @endif
          <i class="fab fa-facebook-f"></i> &nbsp; Usar do Facebook
        </div>
      </label>      
      <!--input id="avatar" name="avatar" style="display:none" type="file" accept="image/*" data-input-avatar-->   
     
    </div>
    <span class="sr-only span-upload" image-upload-ok> Imagem carregada com sucesso. </span>     
  </div>
  @endif

  <div class="form-group d-flex justify-content-end">
    <button class="btn btn-info" type="submit" data-btn-confirm-up>
      Salvar
    </button>
  </div>

  <div class="form-group">
    <label for="fullname">
      Nome completo
      <span class="required_simbol"> *
      </span>
    </label>
    <input
      id="fullname"
      class="form-control"
      name="fullname"
      type="text"
      value="{{ $usr->fullname }}"
      required
      data-input-name-up
    >
  </div>
  <div class="form-group">
    <label for="email">
      E-mail
      <span class="required_simbol"> *
      </span>
    </label>
    <input
      id="email"
      class="form-control"
      name="email"
      type="email"
      value="{{ $usr->email }}"
      disabled
      data-input-email-up
    >
  </div>
  <div class="form-group">
    <div class="row">
      <div class="col-12">
        <span class="form-label">
          Gênero
          <span class="required_simbol"> *
          </span>
        </span>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-check form-check-inline">
          <input
            id="gender-female"
            class="form-check-input"
            name="gender"
            type="radio"
            value="F"
            @if($gender === 'F') checked @endif
            required
          >
          <label class="form-check-label" for="gender-female">
            Mulher
          </label>
        </div>
        <div class="form-check form-check-inline">
          <input
            id="gender-male"
            class="form-check-input"
            name="gender"
            type="radio"
            value="M"
            @if($gender === 'M') checked @endif
            required
          >
          <label class="form-check-label" for="gender-male">
            Homem
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group">
        <label for="customer-cpf">
              CPF
              <span class="required_simbol"> *
             </span>
        </label>
        <input
          id="customer-cpf"
          class="form-control"
          name="customer-cpf"
          type="text"
          value="{{ $usr->cpf }}"
          data-input-cpf-up
          required
          placeholder="Ex.: 123.456.789-00"
        >      
    </div>  
  <div class="form-group">
    <label for="birthdate">
      Data de nascimento
      <span class="required_simbol"> *
      </span>
    </label>
    <input
      id="birthdate"
      class="form-control"
      name="birthdate"
      type="text"
      value="{{ date('d/m/Y', strtotime($usr->birthdate)) }}" 
      required
      data-mask-date
      data-input-birthdate-up
    >
  </div>
  <div class="form-group">
      <label for="cep">
        CEP 
        <span class="required_simbol"> *
        </span>
      </label>
      <input
        id="cep"
        class="form-control"
        name="cep"
        type="text"
        value="{{ $data->cep ?? '' }}"
        required
        data-mask-cep
        data-input-cep-up
      >
  </div>
  
    <div class="row" data-address-group>
      <div class="form-group col-md-8">
        <label for="street">
          Rua
          <span class="required_simbol"> *
        </span>
        </label>
        <input
              id="customer-street"
              class="form-control"
              name="customer-street"
              type="text"
              value="{{ $data->street ?? '' }}"
              data-input-street-up
              required
            >
      </div>
      <div class="form-group col-md-4">
        <label for="neighborhood">
          Bairro
          <span class="required_simbol"> *
        </span>
        </label>
        <input
          id="customer-neighborhood"
          class="form-control"
          name="customer-neighborhood"
          type="text"
          value="{{ $data->neighborhood ?? '' }}"
          data-input-neighborhood-up
          required
        >       
      </div>
    </div>

    <div class="row" data-address-group>
        <div class="form-group col-md-3">
          <label for="number">
            Nº
            <span class="required_simbol"> *
            </span>
          </label>
          <input
            id="customer-number"
            class="form-control"
            name="customer-number"
            type="text"
            value="{{ $data->number ?? '' }}"
            data-input-number-up
            required
          >      
        </div>
        <div class="form-group col-md-9">
          <label for="complement">
            Complemento
            <span class="required_simbol"> *
            </span>
          </label>
          <input
            id="customer-complement" 
            class="form-control"
            name="customer-complement"
            type="text"
            value="{{ $data->complement ?? '' }}"
            data-input-complement-up
            required
          >
        </div>
    </div>

    <div class="row">
      <div class="form-group col-md-12">
        <label for="reference-point">
          Ponto de Referência
          <span class="required_simbol"> *
           </span>
        </label>
        <input
          id="customer-reference-point"
          class="form-control"
          name="customer-reference-point"
          type="text"
          value="{{ $data->reference_point ?? '' }}"
          data-input-reference-point-up
          required
        >      
      </div>
    </div>

    <div class="row" data-address-group>
      <div class="form-group col-md-10">
          <label for="city">
              Cidade
              <span class="required_simbol"> *
              </span>
          </label>
          <input
            id="customer-city"
            class="form-control"
            name="customer-city"
            type="text"
            value="{{ $data->city ?? '' }}"
            data-input-city-up
            required
          >
      </div>  
      <div class="form-group col-md-2">
          <label for="state">
              Estado
              <span class="required_simbol"> *
              </span>
          </label>
          <input
            id="customer-state"
            class="form-control"
            name="customer-state"
            type="text"
            value="{{ $data->state ?? '' }}"
            placeholder="UF"
            data-input-state-up
            required
          >
      </div>         
    </div>

    <div class="row" data-address-group>
      <div class="form-group col-md-6">
          <label for="phone1">
                Telefone 1
                <span class="required_simbol"> *
                 </span>
          </label>
          <input
            id="customer-phone1"
            class="form-control"
            name="customer-phone1"
            type="tel"
            value="{{ $usr->ddd ?? '' }} {{ $data->customer->phone_number ?? '' }}"
            data-input-phone1-up
            placeholder="Ex.: (11) 12345-6789"
            required
          >
        </div>
        <div class="form-group col-md-6">
         <label for="phone2">
               Telefone 2
          </label>
          <input
            id="customer-phone2"
            class="form-control"
            name="customer-phone2"
            type="tel"
            value="{{ $data->customer->secondary_phone_number ?? '' }}"
            placeholder="Ex.: (11) 12345-6789"
            data-input-phone2-up
          >
        </div>
    </div>

    <div class="row">
      <div class="form-group col-md-12">
        <label for="interests">
            Interesses
            <span class="required_simbol"> *
            </span>
        </label> <br>
        @php $flag = 0; @endphp

        @foreach($interestTypes as $interestType)
          @if($flag == 4)
            <br>
            @php $flag = 0; @endphp
          @else
            @php $flag++; @endphp
          @endif          
            <input 
            type="checkbox" 
            data-interests
            id="{{ $interestType->id }}" 
            name="interests-{{$interestType->id}}" 
            data-id="{{ $interestType->id }}" 
            data-value="{{ $interestType->title }}"

            @foreach($customersInterests as $customersInterest)
              @if($interestType->id == $customersInterest->interest_types_id) checked @endif
            @endforeach          
            > 
            <label for="{{ $interestType->id }}">
            {{ $interestType->title }}          
          </label>
        @endforeach
      </div>
    </div>

  <div class="sr-only">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="PUT">
  </div>
 
  <div class="alert alert-success alert-dismissible fade show sr-only" data-confirmation-alert-up>
  Obrigado! Suas informações foram atualizadas com sucesso. <br>
  </div>

  <div class="alert alert-warning sr-only" data-updated-alert-up>
  Nenhum dado foi alterado. <br>
  </div>

  <div class="alert alert-danger alert-dismissible fade show sr-only" data-confirmation-alert-danger-up>
    Por favor, preencha corretamente os campos obrigatórios.
  </div>

  <div class="form-group d-flex justify-content-end">
    <button class="btn btn-info" type="submit" data-btn-confirm-up>
      Salvar
    </button>
  </div>
</form>



