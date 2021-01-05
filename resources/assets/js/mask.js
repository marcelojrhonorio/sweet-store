const Mask = () => {
  const $fieldDate = $('[data-mask-date]')
  const $fieldCep  = $('[data-mask-cep]')

  if ($fieldDate.length) {
    $fieldDate.mask('00/00/0000')
  }

  if ($fieldCep.length) {
    $fieldCep.mask('00.000-000')
  }
}

export default Mask
