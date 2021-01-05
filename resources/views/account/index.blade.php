@extends('layouts.store')

@section('title', 'Perfil')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-9 col-centered">
      <h1 class="page-title">
        Minha <strong>conta</strong>
      </h1>

      <table class=" table my-account-menu-table" style="width: 100%;">
        <tbody>

          @if(Session::get('confirmed'))
          <tr class="my-account-menu-row" data-account-profile>
          @else
          <tr class="my-account-menu-row-disabled">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              Confirme seu e-mail para alterar seus dados.
              Caso não tenha recebido, <a href="#" data-trigger-modal-email><strong>clique aqui</strong></a>.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif
            <td class="icon align-middle" style="width: 10px;"><i class="fas fa-user"></i></th>
            <td colspan="2">
              <strong>Meus dados</strong> <br> 
              <span class="menu-account-description">
                Endereço, telefone, cpf
              </span>
            </td>
            <td class="align-middle" style="text-align:right;"> <i class="fas fa-angle-right"></i> </td>
          </tr>

          <tr class="my-account-menu-row" data-account-unsubscribe>
            <td class="icon align-middle" style="width: 10px;"><i class="fas fa-user-alt-slash"></i></th>
            <td colspan="2">
              <strong>Descadastro</strong> <br> 
              <span class="menu-account-description">
                E-mails, cancelamento de conta
              </span>
            </td>
            <td class="align-middle" style="text-align:right;"> <i class="fas fa-angle-right"></i> </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
