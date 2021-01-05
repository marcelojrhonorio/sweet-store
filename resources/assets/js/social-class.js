const SocialClass = {
  start() {
    this.$btn = $('[data-social-show-modal]');
    this.$modal = $('[data-modal-social]');
    
    // Back and next buttons.
    this.$next = $('[data-btn-next]');
    this.$back = $('[data-btn-back]');

    // All questions from API.
    this.$questions = [];

    // Index to render questions array.
    this.$questionIndex = 0;

    // Div to render questions.
    this.$currentSocialQuestion = $('[data-social-current-question]');

    // Loader.
    this.$loader = $('[data-social-loader]');


    // Hide current question.
    this.$currentSocialQuestion.addClass('sr-only');

    this.bind();
  },

  bind() {
    this.$btn.on('click', this.onBtnClick.bind(this));
    this.$next.on('click', this.onNextClick.bind(this));
    this.$back.on('click', this.onBackClick.bind(this));
    this.$modal.on('hide.bs.modal', this.onCloseModal.bind(this));
  },

  onCloseModal() {
    // Hide current social question on close.
    this.$currentSocialQuestion.addClass('sr-only');
    this.$questionIndex = 0;
  },

  onNextClick(event) {
    event.preventDefault();
    const answer = $("input[name='answer']:checked").val();

    this.customAlert('remove');

    if (answer == null) {
      this.customAlert('danger');
      return;
    }

    this.$currentSocialQuestion.fadeOut("slow", function() {
      SocialClass.saveAnswer(answer);
    });
  },

  saveAnswer(answer) {
    // Get token.
    const token = $('meta[name="csrf-token"]').attr('content');
    
    // Show Loader.
    this.$loader.removeClass('sr-only');

    $.ajax({
      method: 'POST',
      url: '/social-class/save',
      contentType: 'application/json',
      data: JSON.stringify({
        _token: token,
        answer: answer,
      }),
      success: function(result) {
      },
    });

    this.$questionIndex++;
    this.renderQuestions(this.$questionIndex);
  },

  onBackClick(event) {
    event.preventDefault();
  },

  onBtnClick(event) {
    event.preventDefault();
    this.$modal.modal('show');
    
    // Get token.
    const token = $('meta[name="csrf-token"]').attr('content');

    // Show Loader.
    this.$loader.removeClass('sr-only');
    
    $.ajax({
      method: 'POST',
      url: '/social-class/search',
      contentType: 'application/json',
      data: JSON.stringify({
        _token: token,
      }),
      success: function(result) {
        // Hide loader.
        SocialClass.$loader.addClass('sr-only');

        // Show current question.
        SocialClass.$currentSocialQuestion.removeClass('sr-only');

        // Render question data.
        SocialClass.$questions = result.data;
        SocialClass.renderQuestions();
      },
    });

  },

  renderQuestions() {
    // Verify if research is finished.
    if (this.$questions.length === (this.$questionIndex + 1)) {
      this.checkoutCustomer();

      // Remove form and card.
      this.$loader.addClass('sr-only');
      $('.master-title').text('Pesquisa finalizada, obrigado! :)');
      this.$back.prop('disabled', true);

      window.setTimeout(function(){
        SocialClass.$modal.modal('hide');
        $('[data-social-research-card]').addClass('sr-only');
      }, 3000);

      return;
    }

    // Clear data.
    $('.question-title').remove();
    $('.answer').remove();

    // Hide loader.
    this.$loader.addClass('sr-only');

    // Remove danger.
    this.customAlert('remove');

    // Render Question.
    const currentQuestion = this.$questions[this.$questionIndex];
    this.$currentSocialQuestion.append("<h6 class='question-title' style='padding-top: 3px;'>" + currentQuestion.description + "</h6>");
    
    // Render Options.
    $.each(currentQuestion.research_options, function (index, value) {
      SocialClass.$currentSocialQuestion
      .append("<div class='answer' style='padding-left: 5px;'><label style='font-size: 15px;'><input type='radio' name='answer' value='"+ String(value.id) +"' class='answer' data-value='"+ String(value.id) +"'> "+ value.description +"</label></div>");
    });

    this.$currentSocialQuestion.fadeIn("slow");
  },

  customAlert(type) {
    switch(type){
      case 'danger': 
        this.$currentSocialQuestion.css('background', '#ffc4c4');
        this.$currentSocialQuestion.css('border-left', '6px solid red');      
        break;

      case 'remove':
        this.$currentSocialQuestion.css('background', 'rgba(0,0,0,0)');
        this.$currentSocialQuestion.css('border-left', 'none');        
        break;
    }
    
  },

  checkoutCustomer() {
    // Get token.
    const token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      method: 'POST',
      url: '/social-class/checkout',
      contentType: 'application/json',
      data: JSON.stringify({
        _token: token,
      }),
      success: function(result) {
        const points = result.data.points;
        
        //format points
        $('[data-points-total]').text(points.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'));
      },
    });
  },

}

const SocialClassResearch = () => {
  SocialClass.start();
}

export default SocialClassResearch;