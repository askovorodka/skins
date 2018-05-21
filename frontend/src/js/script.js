$(document).ready(function() {

  $('article.market-main').parallax({
    imageSrc: '/assets/build/images/background.jpg',
    parallax: 'scroll',
    speed: '1.2',
    positionY: '70px'
  });

  $('section.home').parallax({
    imageSrc: '/assets/build/images/home-bg.jpg',
    parallax: 'scroll',
    positionY: '-10px'
  });

  $('a.scroll-to').click(function() {
    $('html, body').animate({
      scrollTop: $($(this).attr('href')).offset().top + 'px'
    }, {
      duration: 500,
      easing: 'swing'
    });
    return false;
  });

  $('a.main-navigation__link--orange').click(function() {
    $('div.login-form-container').toggleClass('hidden');
  });

  $('a.main-navigation__link--market').click(function() {
    $('article.index-main').addClass('hidden');
    $('article.faq-page').addClass('hidden');
    $('footer.page-footer').addClass('page-footer--market');
    $('article.market-main').removeClass('hidden')
      .parallax('destroy')
      .parallax({
        imageSrc: '/assets/build/images/background.jpg',
        parallax: 'scroll',
        speed: '1.2',
        positionY: '70px'
    });
    $(document).scrollTop(0);
  });

  $('a.button--faq').click(function() {
    $('article.index-main').addClass('hidden');
    $('article.faq-page').removeClass('hidden');
    $(document).scrollTop(0);
  });

  $('a.main-navigation__link--home, a.page-logo__link').click(function() {
    $('article.index-main').removeClass('hidden');
    $('article.market-main').addClass('hidden');
    $('article.faq-page').addClass('hidden');
    $('footer.page-footer').removeClass('page-footer--market');
    $('section.home')
      .parallax('destroy')
      .parallax({
        imageSrc: '/assets/build/images/home-bg.jpg',
        parallax: 'scroll',
        positionY: '-10px'
    });
    $('html, body').animate({
      scrollTop: $($(this).attr('href')).offset().top + 'px'
    }, {
      duration: 500,
      easing: 'swing'
    });
    return false;
  });

  $('h2.toggle__partners').click(function() {
    $('h2.toggle__group').addClass('main-title--inactive');
    $('h2.toggle__partners').removeClass('main-title--inactive');
    $('div.logos-partners').removeClass('hidden');
    $('div.logos-group').addClass('hidden');
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').removeClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').removeClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').addClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('h2.toggle__group').click(function() {
    $('h2.toggle__partners').addClass('main-title--inactive');
    $('h2.toggle__group').removeClass('main-title--inactive');
    $('div.logos-group').removeClass('hidden');
    $('div.logos-partners').addClass('hidden');
    $('img.img-group-1').removeClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').removeClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').addClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('div.box-partner-1').click(function() {
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').removeClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').removeClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').addClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('div.box-partner-2').click(function() {
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').removeClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').removeClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').addClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('div.box-partner-3').click(function() {
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').removeClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').removeClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').addClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('div.box-partner-4').click(function() {
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').removeClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').removeClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').addClass('right-col__logos-box-image--active');
  });

  $('div.box-group-1').click(function() {
    $('img.img-group-1').removeClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').removeClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').addClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('div.box-group-2').click(function() {
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').removeClass('hidden');
    $('img.img-group-3').addClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').removeClass('hidden');
    $('div.caption-group-3').addClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').addClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $('div.box-group-3').click(function() {
    $('img.img-group-1').addClass('hidden');
    $('img.img-group-2').addClass('hidden');
    $('img.img-group-3').removeClass('hidden');
    $('img.img-partner-1').addClass('hidden');
    $('img.img-partner-2').addClass('hidden');
    $('img.img-partner-3').addClass('hidden');
    $('img.img-partner-4').addClass('hidden');
    $('div.caption-group-1').addClass('hidden');
    $('div.caption-group-2').addClass('hidden');
    $('div.caption-group-3').removeClass('hidden');
    $('div.caption-partner-1').addClass('hidden');
    $('div.caption-partner-2').addClass('hidden');
    $('div.caption-partner-3').addClass('hidden');
    $('div.caption-partner-4').addClass('hidden');
    $('div.box-group-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-group-3>img').addClass('right-col__logos-box-image--active');
    $('div.box-partner-1>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-2>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-3>img').removeClass('right-col__logos-box-image--active');
    $('div.box-partner-4>img').removeClass('right-col__logos-box-image--active');
  });

  $(document).on('click', 'li.main-navigation__item--menu-button', function() {
    $('li.main-navigation__item:not(:first-of-type)').css('display', 'block');
    $('li.main-navigation__item:first-of-type')
      .removeClass('main-navigation__item--menu-button')
      .addClass('main-navigation__item--close-button');
  });

  $(document).on('click', 'li.main-navigation__item--close-button', function() {
    $('li.main-navigation__item:not(:first-of-type)').css('display', 'none');
    $('li.main-navigation__item:first-of-type')
      .removeClass('main-navigation__item--close-button')
      .addClass('main-navigation__item--menu-button');
  });

  $('a.reg-link').click(function() {
    $('div.login-form-container').toggleClass('hidden');
  });

  $('li.main-navigation__item:not(:first-of-type):not(:last-of-type)>a,img.page-logo__image').click(function() {
    $('div.login-form-container').addClass('hidden');
  });

  $(document).on('click', 'article', function() {
    $('div.login-form-container').addClass('hidden');
  });

  $('.faq dt').on('click',function(){
    if ($(this).is('.opened')) {
      $(this).removeClass('opened');
      $(this).next('dd').slideUp();
    } else {
      $(this).addClass('opened');
      $(this).next('dd').slideDown();
    }
  });

  $('.show-sub-dd').on('click', function() {
    if ($(this).is('.show-sub-dd--active')) {
      $(this).removeClass('show-sub-dd--active');
      $('.sub-dd').slideUp();
    } else {
      $(this).addClass('show-sub-dd--active');
      $('.sub-dd').slideDown();
    }
  })

    $(document).on('submit', '.login-form', function(e){
        e.preventDefault();

        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: JSON.stringify({username: $("#form_username").val(), password: $("#form_password").val()}),
            dataType: "json",
            contentType: 'application/json; charset=UTF-8',
            success: function(response){
                if (typeof response.token != 'undefined') {
                    document.cookie = "token=" + response.token + ";domain=.skins4real.com;path=/";
                    location.href = 'https://partner.skins4real.com/';
                    //document.cookie = "token=" + response.token + ";path=/";
                    //location.href = 'http://localhost:3333';

                } else {
                    alert('Неверный логин или пароль');
                }
            }
        });
    });

  $(document).on('submit', 'form.partner_send', function(e){
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function(response){
            if (response.status == "error") {
                $(this).find("button").notify(response.message, "error");
                return;
            }
            $(this).find("input").val('');
            $(this).find("button").notify("Спасибо, заявка на подключение отправлена!", "success");
        }.bind(this)
    })
  })

    $(document).on('submit', 'form.ad_send', function(e){
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function(response){
                if (response.status == "error") {
                    $(this).find("button").notify(response.message, "error");
                    return;
                }
                $(this).find("input").val('');
                $(this).find("button").notify("Спасибо, заявка отправлена!", "success");
            }.bind(this)
        })
    })

    $('.mobile_phone').keypress(function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ($.inArray(charCode,[32,40,41,43,45]) > -1) {
          return true;
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

});
