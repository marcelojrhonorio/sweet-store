const DownloadFile = {
  start() {
    this.updating = false
    this.token    = $('meta[name="csrf-token"]').attr('content')

    this.ui                  = {}
    this.ui.$modal           = $('[data-download-modal]')
    this.ui.$form            = this.ui.$modal.find('[data-form-download-file]')
    this.ui.$input           = this.ui.$modal.find('[data-input-download]')
    this.ui.$btnOk           = this.ui.$modal.find('[data-btn-download-ok]')
    
    if(this.ui.$input.val() !== 'default'){
      this.ui.$modal.modal('show');
      
    }

    this.bind()
  },

  bind() {
    this.ui.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
    this.ui.$btnOk.on('click', this.onOkClick.bind(this))
  },

  onHiddenModal(event) {
    this.ui.$btnOk.prop('disabled', false).text(this.labels.update)
  },

  onOkClick(event) {
    event.preventDefault()

    this.ui.$modal.modal('close')
  },

}

const Download = () => {
  DownloadFile.start()
}

export default Download
