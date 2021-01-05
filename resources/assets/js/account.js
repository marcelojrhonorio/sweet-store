const AccountCustomer = {
  start () {
    this.$btnProfile = $('[data-account-profile]');
    this.$btnUnsubscribe = $('[data-account-unsubscribe]');

    this.bind();
  },

  bind () {
    this.$btnProfile.on('click', this.onProfileClick.bind(this));
    this.$btnUnsubscribe.on('click', this.onUnsubscribeClick.bind(this));
  },

  onProfileClick (event) {
    event.preventDefault();
    window.location.href = "/profile";
  },

  onUnsubscribeClick (event) {
    event.preventDefault();
    window.location.href = "/unsubscribe";
  },
}

const Account = () => {
  AccountCustomer.start()
}

export default Account