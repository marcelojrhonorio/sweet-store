window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});

import SidebarToggler from './toggle'
import Confirmation from './confirmation'
import Exchange from './exchange'
import Download from './download'
import Checkin from './checkin'
import Invite from './invite'
import Popup from './popup'
import Mask from './mask'
import CarInsurance from './car-insurance/main'
import Profile from './profile'
import Account from './account'
import Unsubscribe from './unsubscribe'
import Action from './action'
import SsiResearch from './ssi'
import SocialClassResearch from './social-class'
import ReceiveOffers from './receive-offers'
import VipListSweet from './vip-list-sweet'
import EmailForwarding from './email-forwarding'
import LoginPoints from './login-points'

$(() => {
  SidebarToggler()
  Confirmation()
  Exchange()
  Download()
  Checkin()
  Invite()
  Popup()
  Mask()
  Profile()
  Account()
  Unsubscribe()
  Action()
  SocialClassResearch()
  ReceiveOffers()
  VipListSweet()
  EmailForwarding()
  LoginPoints()
  
  SsiResearch()

  CarInsurance.start()
})
