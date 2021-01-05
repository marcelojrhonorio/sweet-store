const Popup = () => {
  const $popup = $('[data-popup-modal]')

  if ($popup.length) {
    $popup.modal('show')
  }
}

export default Popup
