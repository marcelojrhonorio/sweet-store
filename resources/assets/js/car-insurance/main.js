import Steps from './steps'

const CarInsurance = {
  ui: {
    $modal: null,
    $btnOpen: null,
  },

  start() {
    this.ui.$card = $('[data-insurance-research-card]');
    
    this.ui.$modal   = $('[data-modal-car-insurance]');
    this.ui.$btnOpen = $('[data-trigger-car-insurance]');
    
    this.$destination = $('[data-destination-card]');

    this.bind();

    Steps.start();
  },

  bind() {
    this.ui.$btnOpen.on('click', this.onClickBtnOpen.bind(this));
  },

  onClickBtnOpen(event) {
    event.preventDefault();

    this.ui.$card.hide();
    window.open(this.$destination.val());
    //this.ui.$modal.modal('show')
  },
}

export default CarInsurance
