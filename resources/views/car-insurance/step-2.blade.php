<div class="step-2 sr-only" data-insurance-step>
  <div class="form-group">
    <h5>Etapa 2 de 3</h5>
    <h6>Informações sobre o veículo.</h6>
  </div>
  <div>
    <div class="form-group">
      <p class="mb-2">
        Você possui veículo?
      </p>
      <div class="form-check form-check-inline">
        <input
          id="has_car_y"
          class="form-check-input"
          type="radio"
          name="has_car"
          value="1"
          data-insurance-has-car
        >
        <label class="form-check-label" for="has_car_y">
          Sim
        </label>
      </div>
      <div class="form-check form-check-inline">
        <input
          id="has_car_n"
          class="form-check-input"
          type="radio"
          name="has_car"
          value="0"
          data-insurance-has-car
        >
        <label class="form-check-label" for="has_car_n">
          Não
        </label>
      </div>
    </div>

    <div class="form-group" data-wrap-make>
      <label for="make">
        Marca
      </label>
      <select id="make" class="form-control" name="make" data-insurance-make>
        <option disabled selected>
          Selecione a marca
        </option>
      </select>
    </div>

    <div class="form-group" data-wrap-model>
      <label for="model">
        Modelo
      </label>
      <select id="model" class="form-control" name="model" data-insurance-model>
        <option disabled selected>
          Selecione o modelo
        </option>
      </select>
    </div>

    <div class="form-group" data-wrap-year>
      <label for="year">
        Ano
      </label>
      <select id="year" class="form-control" name="year" data-insurance-year>
        <option disabled selected>
          Selecione o ano
        </option>
      </select>
    </div>

    <div class="form-group" data-wrap-has-insurance>
      <p class="mb-2">
        Possui seguro do veículo?
      </p>
      <div class="form-check form-check-inline">
        <input
          id="has_insurance_y"
          class="form-check-input"
          type="radio"
          name="has_insurance"
          value="1"
          data-insurance-has-insurance
        >
        <label class="form-check-label" for="has_insurance_y">
          Sim
        </label>
      </div>
      <div class="form-check form-check-inline">
        <input
          id="has_insurance_n"
          class="form-check-input"
          type="radio"
          name="has_insurance"
          value="0"
          data-insurance-has-insurance
        >
        <label class="form-check-label" for="has_insurance_n">
          Não
        </label>
      </div>
    </div>

    <div class="form-group" data-wrap-date-insurance>
      <label for="date-insurance">
        Qual a data de renovação do seguro?
      </label>
      <input
        id="date-insurance"
        class="form-control"
        name="date-insurance"
        placeholder="Ex: 09/2019"
        type="text"
        data-date-insurance
      >
    </div>

    <div class="form-group" data-wrap-insurer>
      <label for="insurer">
        Qual seguradora?
      </label>
      <select id="insurer" class="form-control" name="insurer" data-insurance-insurer>
        <option disabled selected>
          Selecione a seguradora
        </option>
      </select>
    </div>
  </div>
</div>
