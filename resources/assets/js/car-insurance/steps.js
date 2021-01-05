import Step0 from './step0'
import Step1 from './step1'
import Step2 from './step2'
import Step3 from './step3'
import Step4 from './step4'

const Steps = {
  ui: {
    $modal: null,
    $progressWrap: null,
    $progress: null,
    $btnNext: null,
    $btnClose: null,
    $steps: null,
  },

  state: {
    current: 0,
  },

  token: null,

  start() {
    this.ui.$modal = $('[data-modal-car-insurance]')

    this.ui.$progressWrap = $('[data-container-progress]')
    this.ui.$progressBar  = $('[data-insurance-progress]')

    this.ui.$steps   = $('[data-insurance-step]')
    this.ui.$buttons = $('[data-insurance-step-button]')

    this.token = $('meta[name="csrf-token"]').attr('content')

    this.bind()

    Step0.start(this.ui.$modal)
    Step1.start(this.ui.$modal)
    Step2.start(this.ui.$modal)
    Step3.start(this.ui.$modal)
    Step4.start(this.ui.$modal)
  },

  bind() {
    this.ui.$modal.on('show:next:step', this.onShowNextStep.bind(this))
    this.ui.$modal.on('hidden.bs.modal', this.onHiddenModal.bind(this))
  },

  updateProgressBar() {
    const current = this.state.current

    const addOrRemove = current > 0 && current < 4
                        ? 'removeClass'
                        : 'addClass'

    const percent = current / 3 * 100

    this.ui.$progressBar.css('width', `${percent}%`)

    this.ui.$progressWrap[addOrRemove]('sr-only')

    return this
  },

  showCurrentStep() {
    this.ui.$steps
      .addClass('sr-only')
      .eq(this.state.current)
        .removeClass('sr-only')

    this.ui.$buttons
      .addClass('sr-only')
      .eq(this.state.current)
        .removeClass('sr-only')

    return this
  },

  onShowNextStep(event) {
    this.state.current += 1
    this.updateProgressBar().showCurrentStep()
  },

  onHiddenModal(event) {
    this.state.current = 0
    this.updateProgressBar().showCurrentStep()
  }
}

export default Steps
