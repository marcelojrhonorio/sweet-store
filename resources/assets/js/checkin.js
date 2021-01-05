const Checkin = () => {
  const $btnCheckin = $('[data-trigger-checkin]')

  if ($btnCheckin.length) {
    $btnCheckin.on('click', function(event) {
      event.preventDefault()

      const $this        = $(this)
      const href         = $this.attr('href')
      const actionId     = $this.data('action')
      const token        = $('meta[name="csrf-token"]').attr('content')
      const newTab       = window.open('', '_blank')

      const checking = $.ajax({
        method: 'POST',
        url: '/checkin',
        contentType: 'application/json',
        data: JSON.stringify({
          _token: token,
          action_id: actionId,
        }),
      })

      checking.done(function(data) {
        if (data.success) {
            $('[data-points-total]').text(data.data.points)
            $this.closest('.item').remove()
            newTab.location = href
        }
      })
    })
  }
}

export default Checkin
