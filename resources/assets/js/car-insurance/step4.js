const Step4 = {
  start($modal = {}) {
    this.ui        = {}
    this.ui.$modal = $modal
    this.ui.$btn   = $('[data-insurance-step-button]:eq(4)')

    this.bind()
  },

  bind() {
    this.ui.$btn.on('click', this.onClickBtn.bind(this))
    return this
  },

  onClickBtn(event) {
    event.preventDefault()
    this.ui.$modal.modal('hide')
  },
}

export default Step4
