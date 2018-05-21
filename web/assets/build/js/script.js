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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJzY3JpcHQuanMiXSwic291cmNlc0NvbnRlbnQiOlsiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG5cbiAgJCgnYXJ0aWNsZS5tYXJrZXQtbWFpbicpLnBhcmFsbGF4KHtcbiAgICBpbWFnZVNyYzogJy9hc3NldHMvYnVpbGQvaW1hZ2VzL2JhY2tncm91bmQuanBnJyxcbiAgICBwYXJhbGxheDogJ3Njcm9sbCcsXG4gICAgc3BlZWQ6ICcxLjInLFxuICAgIHBvc2l0aW9uWTogJzcwcHgnXG4gIH0pO1xuXG4gICQoJ3NlY3Rpb24uaG9tZScpLnBhcmFsbGF4KHtcbiAgICBpbWFnZVNyYzogJy9hc3NldHMvYnVpbGQvaW1hZ2VzL2hvbWUtYmcuanBnJyxcbiAgICBwYXJhbGxheDogJ3Njcm9sbCcsXG4gICAgcG9zaXRpb25ZOiAnLTEwcHgnXG4gIH0pO1xuXG4gICQoJ2Euc2Nyb2xsLXRvJykuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgJCgnaHRtbCwgYm9keScpLmFuaW1hdGUoe1xuICAgICAgc2Nyb2xsVG9wOiAkKCQodGhpcykuYXR0cignaHJlZicpKS5vZmZzZXQoKS50b3AgKyAncHgnXG4gICAgfSwge1xuICAgICAgZHVyYXRpb246IDUwMCxcbiAgICAgIGVhc2luZzogJ3N3aW5nJ1xuICAgIH0pO1xuICAgIHJldHVybiBmYWxzZTtcbiAgfSk7XG5cbiAgJCgnYS5tYWluLW5hdmlnYXRpb25fX2xpbmstLW9yYW5nZScpLmNsaWNrKGZ1bmN0aW9uKCkge1xuICAgICQoJ2Rpdi5sb2dpbi1mb3JtLWNvbnRhaW5lcicpLnRvZ2dsZUNsYXNzKCdoaWRkZW4nKTtcbiAgfSk7XG5cbiAgJCgnYS5tYWluLW5hdmlnYXRpb25fX2xpbmstLW1hcmtldCcpLmNsaWNrKGZ1bmN0aW9uKCkge1xuICAgICQoJ2FydGljbGUuaW5kZXgtbWFpbicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdhcnRpY2xlLmZhcS1wYWdlJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Zvb3Rlci5wYWdlLWZvb3RlcicpLmFkZENsYXNzKCdwYWdlLWZvb3Rlci0tbWFya2V0Jyk7XG4gICAgJCgnYXJ0aWNsZS5tYXJrZXQtbWFpbicpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKVxuICAgICAgLnBhcmFsbGF4KCdkZXN0cm95JylcbiAgICAgIC5wYXJhbGxheCh7XG4gICAgICAgIGltYWdlU3JjOiAnL2Fzc2V0cy9idWlsZC9pbWFnZXMvYmFja2dyb3VuZC5qcGcnLFxuICAgICAgICBwYXJhbGxheDogJ3Njcm9sbCcsXG4gICAgICAgIHNwZWVkOiAnMS4yJyxcbiAgICAgICAgcG9zaXRpb25ZOiAnNzBweCdcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5zY3JvbGxUb3AoMCk7XG4gIH0pO1xuXG4gICQoJ2EuYnV0dG9uLS1mYXEnKS5jbGljayhmdW5jdGlvbigpIHtcbiAgICAkKCdhcnRpY2xlLmluZGV4LW1haW4nKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnYXJ0aWNsZS5mYXEtcGFnZScpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKGRvY3VtZW50KS5zY3JvbGxUb3AoMCk7XG4gIH0pO1xuXG4gICQoJ2EubWFpbi1uYXZpZ2F0aW9uX19saW5rLS1ob21lLCBhLnBhZ2UtbG9nb19fbGluaycpLmNsaWNrKGZ1bmN0aW9uKCkge1xuICAgICQoJ2FydGljbGUuaW5kZXgtbWFpbicpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdhcnRpY2xlLm1hcmtldC1tYWluJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2FydGljbGUuZmFxLXBhZ2UnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZm9vdGVyLnBhZ2UtZm9vdGVyJykucmVtb3ZlQ2xhc3MoJ3BhZ2UtZm9vdGVyLS1tYXJrZXQnKTtcbiAgICAkKCdzZWN0aW9uLmhvbWUnKVxuICAgICAgLnBhcmFsbGF4KCdkZXN0cm95JylcbiAgICAgIC5wYXJhbGxheCh7XG4gICAgICAgIGltYWdlU3JjOiAnL2Fzc2V0cy9idWlsZC9pbWFnZXMvaG9tZS1iZy5qcGcnLFxuICAgICAgICBwYXJhbGxheDogJ3Njcm9sbCcsXG4gICAgICAgIHBvc2l0aW9uWTogJy0xMHB4J1xuICAgIH0pO1xuICAgICQoJ2h0bWwsIGJvZHknKS5hbmltYXRlKHtcbiAgICAgIHNjcm9sbFRvcDogJCgkKHRoaXMpLmF0dHIoJ2hyZWYnKSkub2Zmc2V0KCkudG9wICsgJ3B4J1xuICAgIH0sIHtcbiAgICAgIGR1cmF0aW9uOiA1MDAsXG4gICAgICBlYXNpbmc6ICdzd2luZydcbiAgICB9KTtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH0pO1xuXG4gICQoJ2gyLnRvZ2dsZV9fcGFydG5lcnMnKS5jbGljayhmdW5jdGlvbigpIHtcbiAgICAkKCdoMi50b2dnbGVfX2dyb3VwJykuYWRkQ2xhc3MoJ21haW4tdGl0bGUtLWluYWN0aXZlJyk7XG4gICAgJCgnaDIudG9nZ2xlX19wYXJ0bmVycycpLnJlbW92ZUNsYXNzKCdtYWluLXRpdGxlLS1pbmFjdGl2ZScpO1xuICAgICQoJ2Rpdi5sb2dvcy1wYXJ0bmVycycpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYubG9nb3MtZ3JvdXAnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLmFkZENsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoJ2gyLnRvZ2dsZV9fZ3JvdXAnKS5jbGljayhmdW5jdGlvbigpIHtcbiAgICAkKCdoMi50b2dnbGVfX3BhcnRuZXJzJykuYWRkQ2xhc3MoJ21haW4tdGl0bGUtLWluYWN0aXZlJyk7XG4gICAgJCgnaDIudG9nZ2xlX19ncm91cCcpLnJlbW92ZUNsYXNzKCdtYWluLXRpdGxlLS1pbmFjdGl2ZScpO1xuICAgICQoJ2Rpdi5sb2dvcy1ncm91cCcpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYubG9nb3MtcGFydG5lcnMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5hZGRDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoJ2Rpdi5ib3gtcGFydG5lci0xJykuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLmFkZENsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoJ2Rpdi5ib3gtcGFydG5lci0yJykuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykuYWRkQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoJ2Rpdi5ib3gtcGFydG5lci0zJykuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5hZGRDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoJ2Rpdi5ib3gtcGFydG5lci00JykuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLmFkZENsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoJ2Rpdi5ib3gtZ3JvdXAtMScpLmNsaWNrKGZ1bmN0aW9uKCkge1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMScpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1ncm91cC0zJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctcGFydG5lci0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctcGFydG5lci0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctcGFydG5lci0zJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTEnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0zJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0zJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItNCcpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuYm94LWdyb3VwLTE+aW1nJykuYWRkQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LWdyb3VwLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LWdyb3VwLTM+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0yPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTM+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItND5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICB9KTtcblxuICAkKCdkaXYuYm94LWdyb3VwLTInKS5jbGljayhmdW5jdGlvbigpIHtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1ncm91cC0yJykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMycpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLXBhcnRuZXItMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLXBhcnRuZXItMycpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLXBhcnRuZXItNCcpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTInKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMycpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMycpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmJveC1ncm91cC0xPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1ncm91cC0yPmltZycpLmFkZENsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1ncm91cC0zPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTE+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0zPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTQ+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgfSk7XG5cbiAgJCgnZGl2LmJveC1ncm91cC0zJykuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgJCgnaW1nLmltZy1ncm91cC0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2ltZy5pbWctZ3JvdXAtMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdpbWcuaW1nLWdyb3VwLTMnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTEnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnaW1nLmltZy1wYXJ0bmVyLTQnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tZ3JvdXAtMScpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1ncm91cC0yJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLWdyb3VwLTMnKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci0xJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5jYXB0aW9uLXBhcnRuZXItMicpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAkKCdkaXYuY2FwdGlvbi1wYXJ0bmVyLTMnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgJCgnZGl2LmNhcHRpb24tcGFydG5lci00JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMT5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMj5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtZ3JvdXAtMz5pbWcnKS5hZGRDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci0xPmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gICAgJCgnZGl2LmJveC1wYXJ0bmVyLTI+aW1nJykucmVtb3ZlQ2xhc3MoJ3JpZ2h0LWNvbF9fbG9nb3MtYm94LWltYWdlLS1hY3RpdmUnKTtcbiAgICAkKCdkaXYuYm94LXBhcnRuZXItMz5pbWcnKS5yZW1vdmVDbGFzcygncmlnaHQtY29sX19sb2dvcy1ib3gtaW1hZ2UtLWFjdGl2ZScpO1xuICAgICQoJ2Rpdi5ib3gtcGFydG5lci00PmltZycpLnJlbW92ZUNsYXNzKCdyaWdodC1jb2xfX2xvZ29zLWJveC1pbWFnZS0tYWN0aXZlJyk7XG4gIH0pO1xuXG4gICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICdsaS5tYWluLW5hdmlnYXRpb25fX2l0ZW0tLW1lbnUtYnV0dG9uJywgZnVuY3Rpb24oKSB7XG4gICAgJCgnbGkubWFpbi1uYXZpZ2F0aW9uX19pdGVtOm5vdCg6Zmlyc3Qtb2YtdHlwZSknKS5jc3MoJ2Rpc3BsYXknLCAnYmxvY2snKTtcbiAgICAkKCdsaS5tYWluLW5hdmlnYXRpb25fX2l0ZW06Zmlyc3Qtb2YtdHlwZScpXG4gICAgICAucmVtb3ZlQ2xhc3MoJ21haW4tbmF2aWdhdGlvbl9faXRlbS0tbWVudS1idXR0b24nKVxuICAgICAgLmFkZENsYXNzKCdtYWluLW5hdmlnYXRpb25fX2l0ZW0tLWNsb3NlLWJ1dHRvbicpO1xuICB9KTtcblxuICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnbGkubWFpbi1uYXZpZ2F0aW9uX19pdGVtLS1jbG9zZS1idXR0b24nLCBmdW5jdGlvbigpIHtcbiAgICAkKCdsaS5tYWluLW5hdmlnYXRpb25fX2l0ZW06bm90KDpmaXJzdC1vZi10eXBlKScpLmNzcygnZGlzcGxheScsICdub25lJyk7XG4gICAgJCgnbGkubWFpbi1uYXZpZ2F0aW9uX19pdGVtOmZpcnN0LW9mLXR5cGUnKVxuICAgICAgLnJlbW92ZUNsYXNzKCdtYWluLW5hdmlnYXRpb25fX2l0ZW0tLWNsb3NlLWJ1dHRvbicpXG4gICAgICAuYWRkQ2xhc3MoJ21haW4tbmF2aWdhdGlvbl9faXRlbS0tbWVudS1idXR0b24nKTtcbiAgfSk7XG5cbiAgJCgnYS5yZWctbGluaycpLmNsaWNrKGZ1bmN0aW9uKCkge1xuICAgICQoJ2Rpdi5sb2dpbi1mb3JtLWNvbnRhaW5lcicpLnRvZ2dsZUNsYXNzKCdoaWRkZW4nKTtcbiAgfSk7XG5cbiAgJCgnbGkubWFpbi1uYXZpZ2F0aW9uX19pdGVtOm5vdCg6Zmlyc3Qtb2YtdHlwZSk6bm90KDpsYXN0LW9mLXR5cGUpPmEsaW1nLnBhZ2UtbG9nb19faW1hZ2UnKS5jbGljayhmdW5jdGlvbigpIHtcbiAgICAkKCdkaXYubG9naW4tZm9ybS1jb250YWluZXInKS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gIH0pO1xuXG4gICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICdhcnRpY2xlJywgZnVuY3Rpb24oKSB7XG4gICAgJCgnZGl2LmxvZ2luLWZvcm0tY29udGFpbmVyJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuICB9KTtcblxuICAkKCcuZmFxIGR0Jykub24oJ2NsaWNrJyxmdW5jdGlvbigpe1xuICAgIGlmICgkKHRoaXMpLmlzKCcub3BlbmVkJykpIHtcbiAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ29wZW5lZCcpO1xuICAgICAgJCh0aGlzKS5uZXh0KCdkZCcpLnNsaWRlVXAoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJCh0aGlzKS5hZGRDbGFzcygnb3BlbmVkJyk7XG4gICAgICAkKHRoaXMpLm5leHQoJ2RkJykuc2xpZGVEb3duKCk7XG4gICAgfVxuICB9KTtcblxuICAkKCcuc2hvdy1zdWItZGQnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICBpZiAoJCh0aGlzKS5pcygnLnNob3ctc3ViLWRkLS1hY3RpdmUnKSkge1xuICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcygnc2hvdy1zdWItZGQtLWFjdGl2ZScpO1xuICAgICAgJCgnLnN1Yi1kZCcpLnNsaWRlVXAoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJCh0aGlzKS5hZGRDbGFzcygnc2hvdy1zdWItZGQtLWFjdGl2ZScpO1xuICAgICAgJCgnLnN1Yi1kZCcpLnNsaWRlRG93bigpO1xuICAgIH1cbiAgfSlcblxuICAgICQoZG9jdW1lbnQpLm9uKCdzdWJtaXQnLCAnLmxvZ2luLWZvcm0nLCBmdW5jdGlvbihlKXtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiAkKHRoaXMpLmF0dHIoXCJtZXRob2RcIiksXG4gICAgICAgICAgICB1cmw6ICQodGhpcykuYXR0cihcImFjdGlvblwiKSxcbiAgICAgICAgICAgIGRhdGE6IEpTT04uc3RyaW5naWZ5KHt1c2VybmFtZTogJChcIiNmb3JtX3VzZXJuYW1lXCIpLnZhbCgpLCBwYXNzd29yZDogJChcIiNmb3JtX3Bhc3N3b3JkXCIpLnZhbCgpfSksXG4gICAgICAgICAgICBkYXRhVHlwZTogXCJqc29uXCIsXG4gICAgICAgICAgICBjb250ZW50VHlwZTogJ2FwcGxpY2F0aW9uL2pzb247IGNoYXJzZXQ9VVRGLTgnLFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2Upe1xuICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UudG9rZW4gIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgICAgICAgICAgZG9jdW1lbnQuY29va2llID0gXCJ0b2tlbj1cIiArIHJlc3BvbnNlLnRva2VuICsgXCI7ZG9tYWluPS5za2luczRyZWFsLmNvbTtwYXRoPS9cIjtcbiAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaHJlZiA9ICdodHRwczovL3BhcnRuZXIuc2tpbnM0cmVhbC5jb20vJztcbiAgICAgICAgICAgICAgICAgICAgLy9kb2N1bWVudC5jb29raWUgPSBcInRva2VuPVwiICsgcmVzcG9uc2UudG9rZW4gKyBcIjtwYXRoPS9cIjtcbiAgICAgICAgICAgICAgICAgICAgLy9sb2NhdGlvbi5ocmVmID0gJ2h0dHA6Ly9sb2NhbGhvc3Q6MzMzMyc7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBhbGVydCgn0J3QtdCy0LXRgNC90YvQuSDQu9C+0LPQuNC9INC40LvQuCDQv9Cw0YDQvtC70YwnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH0pO1xuXG4gICQoZG9jdW1lbnQpLm9uKCdzdWJtaXQnLCAnZm9ybS5wYXJ0bmVyX3NlbmQnLCBmdW5jdGlvbihlKXtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJC5hamF4KHtcbiAgICAgICAgdHlwZTogJCh0aGlzKS5hdHRyKFwibWV0aG9kXCIpLFxuICAgICAgICB1cmw6ICQodGhpcykuYXR0cihcImFjdGlvblwiKSxcbiAgICAgICAgZGF0YTogJCh0aGlzKS5zZXJpYWxpemUoKSxcbiAgICAgICAgc3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2Upe1xuICAgICAgICAgICAgaWYgKHJlc3BvbnNlLnN0YXR1cyA9PSBcImVycm9yXCIpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoXCJidXR0b25cIikubm90aWZ5KHJlc3BvbnNlLm1lc3NhZ2UsIFwiZXJyb3JcIik7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJCh0aGlzKS5maW5kKFwiaW5wdXRcIikudmFsKCcnKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZChcImJ1dHRvblwiKS5ub3RpZnkoXCLQodC/0LDRgdC40LHQviwg0LfQsNGP0LLQutCwINC90LAg0L/QvtC00LrQu9GO0YfQtdC90LjQtSDQvtGC0L/RgNCw0LLQu9C10L3QsCFcIiwgXCJzdWNjZXNzXCIpO1xuICAgICAgICB9LmJpbmQodGhpcylcbiAgICB9KVxuICB9KVxuXG4gICAgJChkb2N1bWVudCkub24oJ3N1Ym1pdCcsICdmb3JtLmFkX3NlbmQnLCBmdW5jdGlvbihlKXtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdHlwZTogJCh0aGlzKS5hdHRyKFwibWV0aG9kXCIpLFxuICAgICAgICAgICAgdXJsOiAkKHRoaXMpLmF0dHIoXCJhY3Rpb25cIiksXG4gICAgICAgICAgICBkYXRhOiAkKHRoaXMpLnNlcmlhbGl6ZSgpLFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2Upe1xuICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5zdGF0dXMgPT0gXCJlcnJvclwiKSB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZChcImJ1dHRvblwiKS5ub3RpZnkocmVzcG9uc2UubWVzc2FnZSwgXCJlcnJvclwiKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoXCJpbnB1dFwiKS52YWwoJycpO1xuICAgICAgICAgICAgICAgICQodGhpcykuZmluZChcImJ1dHRvblwiKS5ub3RpZnkoXCLQodC/0LDRgdC40LHQviwg0LfQsNGP0LLQutCwINC+0YLQv9GA0LDQstC70LXQvdCwIVwiLCBcInN1Y2Nlc3NcIik7XG4gICAgICAgICAgICB9LmJpbmQodGhpcylcbiAgICAgICAgfSlcbiAgICB9KVxuXG4gICAgJCgnLm1vYmlsZV9waG9uZScpLmtleXByZXNzKGZ1bmN0aW9uIChldnQpIHtcbiAgICAgICAgdmFyIGNoYXJDb2RlID0gKGV2dC53aGljaCkgPyBldnQud2hpY2ggOiBldnQua2V5Q29kZTtcbiAgICAgICAgaWYgKCQuaW5BcnJheShjaGFyQ29kZSxbMzIsNDAsNDEsNDMsNDVdKSA+IC0xKSB7XG4gICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGNoYXJDb2RlID4gMzEgJiYgKGNoYXJDb2RlIDwgNDggfHwgY2hhckNvZGUgPiA1NykpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcblxufSk7Il0sImZpbGUiOiJzY3JpcHQuanMifQ==
