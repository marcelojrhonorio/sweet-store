<div class="step-1 sr-only" data-insurance-step>
  <div class="form-group">
    <h5>Etapa 1 de 3</h5>
    <h6>Confira seus dados de cadastro:</h6>
  </div>
  <div>
    <div class="form-group">
      <label class="sr-only" for="name">
        Nome
      </label>
      <input
        id="name"
        class="form-control"
        name="name"
        type="text"
        placeholder="Nome"
        value="{{ session('name') ?? '' }}"
        readonly
      >
    </div>
    <div class="form-group">
      <label class="sr-only" for="cep">
        CEP
      </label>
      <input
        id="cep"
        class="form-control"
        name="cep"
        type="text"
        maxlength="10"
        placeholder="CEP"
        value="{{ session('cep') ?? '' }}"
        data-insurance-input-cep
      >
      <div class="invalid-feedback">
        Por favor, informe um CEP válido.
      </div>
    </div>
    <div class="form-group">
      <div class="form-check form-check-inline">
        <input
          id="gender_f"
          class="form-check-input"
          type="radio"
          name="gender"
          value="F"
          @if ('F' !== session('gender')) disabled @endif
          @if ('F' === session('gender')) checked @endif
        >
        <label class="form-check-label" for="gender_f">
          Mulher
        </label>
      </div>
      <div class="form-check form-check-inline">
        <input
          id="gender_m"
          class="form-check-input"
          type="radio"
          name="gender"
          value="M"
          @if ('M' !== session('gender')) disabled @endif
          @if ('M' === session('gender')) checked @endif
        >
        <label class="form-check-label" for="gender_m">
          Homem
        </label>
      </div>
    </div>
    <div class="form-group">
      <label class="sr-only" for="birthdate">
        Data de nascimento
      </label>
      <input
        id="birthdate"
        class="form-control"
        name="birthdate"
        type="text"
        maxlength="10"
        placeholder="Data de nascimento"
        readonly
        value="{{ session('birthdate') ? date('d/m/Y', strtotime(session('birthdate'))) : '' }}"
      >
    </div>
    <div class="form-group">
      <label class="sr-only" for="email">
        E-mail
      </label>
      <input
        id="email"
        class="form-control"
        name="email"
        type="email"
        placeholder="E-mail"
        readonly
        value="{{ session('email') ?? '' }}"
      >
    </div>
  </div>
</div>
