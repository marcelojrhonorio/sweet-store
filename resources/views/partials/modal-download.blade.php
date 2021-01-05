<div class="modal fade" tabindex="-1" role="dialog" data-download-modal>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Obrigado por confirmar seu e-mail!
        </h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
        @if(Session::has('download.ebook.after.redirect'))
          
          <p>Caso o download não inicie automaticamente, <a href="{{ Session::get('download.ebook.after.redirect') }}">clique aqui</a>.</p>
        
        @endif

        <form>
          <div class="form-group" data-form-download-file>
            
            @if(Session::has('download.ebook.after.redirect'))
              <input 
                id="download_session" 
                name="download_session" 
                type="hidden" 
                value="{{ Session::get('download.ebook.after.redirect') }}"
                data-input-download
              >
            @else
              <input 
                id="download_session" 
                name="download_session" 
                type="hidden" 
                value="default"
                data-input-download
              >
            @endif
          
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button class="btn btn-success" data-dismiss="modal" type="button" data-btn-download-ok>
          Ok.
        </button>
      </div>
    </div>
  </div>
</div>
