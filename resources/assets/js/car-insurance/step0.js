const Step0 = {
  start($modal = {}) {
    this.ui        = {}
    this.ui.$modal = $modal
    this.ui.$btn   = $('[data-insurance-step-button]:eq(0)')

    this.bind()
  },

  bind() {
    this.ui.$btn.on('click', this.onClickBtn.bind(this))
    return this
  },

  onClickBtn(event) {
    event.preventDefault()
    this.ui.$modal.trigger('show:next:step')
  },
}

export default Step0
