/* jQuery Custom Scroll plugin v0.6.6 | (c) 2015 Mostovoy Andrey | https://github.com/standy/custom-scroll/blob/master/LICENSE */
(function($) {
  $.fn.customScroll = function(options) {
    if (!this.length) {
      return $.extend(defaultOptions, options);
    }
    if (options==='destroy') {
      this.each(function() {
        var cs = $(this).data('custom-scroll');
        if (cs) cs.destroy();
      });
      return this;
    }
    if (this.length===1) return customScroll(this, options);
    this.each(function() {
      customScroll($(this), options);
    });
  };


  var defaultOptions = {
    prefix: 'custom-scroll_',
    barMinHeight: 10,
    barMinWidth:  10,
    offsetTop:    0,
    offsetBottom: 0,
    offsetLeft:   0,
    offsetRight:  0,
    trackWidth:   10,
    trackHeight:  10,
    barHtml: '<div><span></span></div>',
    vertical: true,
    horizontal: false,
    preventParentScroll: true
  };

  var DIRS_VERTICAL = {
//    axis: 'y',
    dim: 'height',
    Dim: 'Height',
    dir: 'top',
    Dir: 'Top',
    dir2: 'bottom',
    Dir2: 'Bottom',
    clientAxis: 'clientY',
    suffix: '-y'
  };
  var DIRS_HORIZONTAL = {
//    axis: 'x',
    dim: 'width',
    Dim: 'Width',
    dir: 'left',
    Dir: 'Left',
    dir2: 'right',
    Dir2: 'Right',
    clientAxis: 'clientX',
    suffix: '-x'
  };

  function customScroll($container, options) {
    var cs = $container.data('custom-scroll');
    if (cs) options = cs.options;
    else options = $.extend({}, defaultOptions, options);
    var dirs = {};
    var lastDims = {};

    var isBarHidden = {
      x: +options.vertical,
      y: +options.horizontal
    };

    if (options.horizontal) {
      dirs.x = DIRS_HORIZONTAL;
      lastDims.x = {};
    }
    if (options.vertical) {
      dirs.y = DIRS_VERTICAL;
      lastDims.y = {};
    }

    if ($container.hasClass(options.prefix+'container') && cs) {
      cs.updateBars();
      return cs;
    }
    var $inner = $container.children('.'+options.prefix+'inner');
    if (!$inner.length) {
      $inner = $container.wrapInner('<div class="'+options.prefix+'inner'+'"></div>').children();
    }

    $container.addClass(options.prefix+'container');


    // scroll dimensions in case of hidden element
    var tmp = $('<div class="'+ options.prefix+'inner" />').width(100).height(100).appendTo('body').css({overflow:'scroll'})[0];
    var scrollWidth = tmp.offsetWidth-tmp.clientWidth;
    var scrollHeight = tmp.offsetHeight-tmp.clientHeight;
    tmp.parentElement.removeChild(tmp);

    if (options.vertical) {
      $inner.css({
        /* save the padding */
        paddingLeft: $container.css('paddingLeft'),
        paddingRight: $container.css('paddingRight'),
        /* hide scrolls */
        marginRight: -scrollWidth+'px'

      });
      $container.css({
        paddingLeft: 0,
        paddingRight: 0
      });
    } else {
      $inner.css({overflowY: 'hidden'})
    }
    if (options.horizontal) {
      $inner.css({
        /* hide scrolls */
        marginBottom: -scrollHeight+'px',
        paddingBottom: scrollHeight+'px'
      });
      $container.css({
        paddingTop: 0,
        paddingBottom: 0
      });
    } else {
      $inner.css({overflowX: 'hidden'})
    }

    /* in case of max-height */
    var maxHeight = $container.css('maxHeight');
    if (parseInt(maxHeight)) {
      $container.css('maxHeight', 'none');
      $inner.css('maxHeight', maxHeight);
    }


    $container.scrollTop(0);


    var $body = $('body');

    var $bars = {};
    $.each(dirs, initBar);

    $inner.on('scroll', updateBars);
    updateBars();
    if (options.preventParentScroll) preventParentScroll();

    var data = {
      $container: $container,
      $bar: $bars.y,
      $barX: $bars.x,
      $inner: $inner,
      destroy: destroy,
      updateBars: updateBars,
      options: options
    };
    $container.data('custom-scroll', data);
    return data;


    function preventParentScroll() {
      $inner.on('DOMMouseScroll mousewheel', function(e) {
        var $this = $(this);
        var scrollTop = this.scrollTop;
        var scrollHeight = this.scrollHeight;
        var height = $this.height();
        var delta = (e.type == 'DOMMouseScroll' ? e.originalEvent.detail * -40 : e.originalEvent.wheelDelta);
        var up = delta > 0;

        if (up ? scrollTop === 0 : scrollTop === scrollHeight - height) {
          e.preventDefault();
        }
      });
    }

    function initBar(dirKey, dir) {
//      console.log('initBar', dirKey, dir)
//      var dir = DIRS[dirKey];
      $container['scroll' + dir.Dir](0);

      var cls = options.prefix+'bar'+dir.suffix;
      var $bar = $container.children('.'+ cls);
      if (!$bar.length) {
        $bar = $(options.barHtml).addClass(cls).appendTo($container);
      }

      $bar.on('mousedown touchstart', function(e) {
        e.preventDefault(); // stop scrolling in ie9
        var scrollStart = $inner['scroll' + dir.Dir]();
        var posStart = e[dir.clientAxis] || e.originalEvent.changedTouches && e.originalEvent.changedTouches[0][dir.clientAxis];
        var ratio = getDims(dirKey, dir).ratio;

        $body.on('mousemove.custom-scroll touchmove.custom-scroll', function(e) {
          e.preventDefault(); // stop scrolling
          var pos = e[dir.clientAxis] || e.originalEvent.changedTouches && e.originalEvent.changedTouches[0][dir.clientAxis];
          $inner['scroll' + dir.Dir](scrollStart + (pos-posStart)/ratio);
        });
        $body.on('mouseup.custom-scroll touchend.custom-scroll', function() {
          $body.off('.custom-scroll');
        });
      });
      $bars[dirKey] = $bar;
    }

    function getDims(dirKey, dir) {
//      console.log('getDims', dirKey, dir)
      var total = $inner.prop('scroll' + dir.Dim)|0;
      var dim = $container['inner' + dir.Dim]();
      var inner = $inner['inner' + dir.Dim]();
      var scroll = dim - options['offset' + dir.Dir] - options['offset' + dir.Dir2];
      if (!isBarHidden[dirKey == 'x' ? 'y' : 'x']) scroll -= options['track'+dir.Dim];

      var bar = Math.max((scroll*dim/total)|0, options['barMin' + dir.Dim]);
      var ratio = (scroll-bar)/(total-inner);
//      if (dirKey == 'y' && $container.is('#example-hard')) console.log('dim', dim, inner, scroll, total, bar, ratio)

      return {
        ratio: ratio,
        dim: dim,
        scroll: scroll,
        total: total,
        bar: bar
      }
    }

    function updateBars() {
      try {
          skins.itemsWrapShowIcons();
      } catch (error) {
          console.log(error.toString());
      }

      $.each(dirs, updateBar);
    }
    function updateBar(dirKey, dir) {
//      var dir = DIRS[dirKey];
      var dims = getDims(dirKey, dir);
      if (!dims.total) return;

      var scrollPos = $inner['scroll' + dir.Dir]();
      if (
        lastDims[dirKey].scrollPos === scrollPos &&
        lastDims[dirKey].scroll === dims.scroll &&
        lastDims[dirKey].total === dims.total
      ) return;
      lastDims[dirKey] = dims;
      lastDims[dirKey].scrollPos = scrollPos;


      var isHide = dims.bar>=dims.scroll;
      if (isHide!==isBarHidden[dirKey]) {
        $container.toggleClass(options.prefix+'hidden'+dir.suffix, isHide);
        isBarHidden[dirKey] = isHide;
      }
      var barPos = scrollPos*dims.ratio;
//      console.log('upd', scrollPos, dims.ratio, barPos)
      //if (dirKey === 'y') console.log(barPos, dims.scroll, dims.bar, dims)
      if (barPos<0) barPos = 0;
      if (barPos>dims.scroll-dims.bar) barPos = dims.scroll-dims.bar;
      $bars[dirKey][dir.dim](dims.bar)
        .css(dir.dir, options['offset' + dir.Dir] + barPos);
    }

    function destroy() {
      $.each(dirs, function(key) { $bars[key].remove(); });
      $container
        .removeClass(options.prefix+'container')
        .removeData('custom-scroll')
        .css({padding: '', maxHeight: ''});
      $inner.contents().appendTo($container);
      $inner.remove();
    }
  }
})(jQuery);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJqcXVlcnkuY3VzdG9tLXNjcm9sbC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBqUXVlcnkgQ3VzdG9tIFNjcm9sbCBwbHVnaW4gdjAuNi42IHwgKGMpIDIwMTUgTW9zdG92b3kgQW5kcmV5IHwgaHR0cHM6Ly9naXRodWIuY29tL3N0YW5keS9jdXN0b20tc2Nyb2xsL2Jsb2IvbWFzdGVyL0xJQ0VOU0UgKi9cbihmdW5jdGlvbigkKSB7XG4gICQuZm4uY3VzdG9tU2Nyb2xsID0gZnVuY3Rpb24ob3B0aW9ucykge1xuICAgIGlmICghdGhpcy5sZW5ndGgpIHtcbiAgICAgIHJldHVybiAkLmV4dGVuZChkZWZhdWx0T3B0aW9ucywgb3B0aW9ucyk7XG4gICAgfVxuICAgIGlmIChvcHRpb25zPT09J2Rlc3Ryb3knKSB7XG4gICAgICB0aGlzLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgIHZhciBjcyA9ICQodGhpcykuZGF0YSgnY3VzdG9tLXNjcm9sbCcpO1xuICAgICAgICBpZiAoY3MpIGNzLmRlc3Ryb3koKTtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIGlmICh0aGlzLmxlbmd0aD09PTEpIHJldHVybiBjdXN0b21TY3JvbGwodGhpcywgb3B0aW9ucyk7XG4gICAgdGhpcy5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgY3VzdG9tU2Nyb2xsKCQodGhpcyksIG9wdGlvbnMpO1xuICAgIH0pO1xuICB9O1xuXG5cbiAgdmFyIGRlZmF1bHRPcHRpb25zID0ge1xuICAgIHByZWZpeDogJ2N1c3RvbS1zY3JvbGxfJyxcbiAgICBiYXJNaW5IZWlnaHQ6IDEwLFxuICAgIGJhck1pbldpZHRoOiAgMTAsXG4gICAgb2Zmc2V0VG9wOiAgICAwLFxuICAgIG9mZnNldEJvdHRvbTogMCxcbiAgICBvZmZzZXRMZWZ0OiAgIDAsXG4gICAgb2Zmc2V0UmlnaHQ6ICAwLFxuICAgIHRyYWNrV2lkdGg6ICAgMTAsXG4gICAgdHJhY2tIZWlnaHQ6ICAxMCxcbiAgICBiYXJIdG1sOiAnPGRpdj48c3Bhbj48L3NwYW4+PC9kaXY+JyxcbiAgICB2ZXJ0aWNhbDogdHJ1ZSxcbiAgICBob3Jpem9udGFsOiBmYWxzZSxcbiAgICBwcmV2ZW50UGFyZW50U2Nyb2xsOiB0cnVlXG4gIH07XG5cbiAgdmFyIERJUlNfVkVSVElDQUwgPSB7XG4vLyAgICBheGlzOiAneScsXG4gICAgZGltOiAnaGVpZ2h0JyxcbiAgICBEaW06ICdIZWlnaHQnLFxuICAgIGRpcjogJ3RvcCcsXG4gICAgRGlyOiAnVG9wJyxcbiAgICBkaXIyOiAnYm90dG9tJyxcbiAgICBEaXIyOiAnQm90dG9tJyxcbiAgICBjbGllbnRBeGlzOiAnY2xpZW50WScsXG4gICAgc3VmZml4OiAnLXknXG4gIH07XG4gIHZhciBESVJTX0hPUklaT05UQUwgPSB7XG4vLyAgICBheGlzOiAneCcsXG4gICAgZGltOiAnd2lkdGgnLFxuICAgIERpbTogJ1dpZHRoJyxcbiAgICBkaXI6ICdsZWZ0JyxcbiAgICBEaXI6ICdMZWZ0JyxcbiAgICBkaXIyOiAncmlnaHQnLFxuICAgIERpcjI6ICdSaWdodCcsXG4gICAgY2xpZW50QXhpczogJ2NsaWVudFgnLFxuICAgIHN1ZmZpeDogJy14J1xuICB9O1xuXG4gIGZ1bmN0aW9uIGN1c3RvbVNjcm9sbCgkY29udGFpbmVyLCBvcHRpb25zKSB7XG4gICAgdmFyIGNzID0gJGNvbnRhaW5lci5kYXRhKCdjdXN0b20tc2Nyb2xsJyk7XG4gICAgaWYgKGNzKSBvcHRpb25zID0gY3Mub3B0aW9ucztcbiAgICBlbHNlIG9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgZGVmYXVsdE9wdGlvbnMsIG9wdGlvbnMpO1xuICAgIHZhciBkaXJzID0ge307XG4gICAgdmFyIGxhc3REaW1zID0ge307XG5cbiAgICB2YXIgaXNCYXJIaWRkZW4gPSB7XG4gICAgICB4OiArb3B0aW9ucy52ZXJ0aWNhbCxcbiAgICAgIHk6ICtvcHRpb25zLmhvcml6b250YWxcbiAgICB9O1xuXG4gICAgaWYgKG9wdGlvbnMuaG9yaXpvbnRhbCkge1xuICAgICAgZGlycy54ID0gRElSU19IT1JJWk9OVEFMO1xuICAgICAgbGFzdERpbXMueCA9IHt9O1xuICAgIH1cbiAgICBpZiAob3B0aW9ucy52ZXJ0aWNhbCkge1xuICAgICAgZGlycy55ID0gRElSU19WRVJUSUNBTDtcbiAgICAgIGxhc3REaW1zLnkgPSB7fTtcbiAgICB9XG5cbiAgICBpZiAoJGNvbnRhaW5lci5oYXNDbGFzcyhvcHRpb25zLnByZWZpeCsnY29udGFpbmVyJykgJiYgY3MpIHtcbiAgICAgIGNzLnVwZGF0ZUJhcnMoKTtcbiAgICAgIHJldHVybiBjcztcbiAgICB9XG4gICAgdmFyICRpbm5lciA9ICRjb250YWluZXIuY2hpbGRyZW4oJy4nK29wdGlvbnMucHJlZml4Kydpbm5lcicpO1xuICAgIGlmICghJGlubmVyLmxlbmd0aCkge1xuICAgICAgJGlubmVyID0gJGNvbnRhaW5lci53cmFwSW5uZXIoJzxkaXYgY2xhc3M9XCInK29wdGlvbnMucHJlZml4Kydpbm5lcicrJ1wiPjwvZGl2PicpLmNoaWxkcmVuKCk7XG4gICAgfVxuXG4gICAgJGNvbnRhaW5lci5hZGRDbGFzcyhvcHRpb25zLnByZWZpeCsnY29udGFpbmVyJyk7XG5cblxuICAgIC8vIHNjcm9sbCBkaW1lbnNpb25zIGluIGNhc2Ugb2YgaGlkZGVuIGVsZW1lbnRcbiAgICB2YXIgdG1wID0gJCgnPGRpdiBjbGFzcz1cIicrIG9wdGlvbnMucHJlZml4Kydpbm5lclwiIC8+Jykud2lkdGgoMTAwKS5oZWlnaHQoMTAwKS5hcHBlbmRUbygnYm9keScpLmNzcyh7b3ZlcmZsb3c6J3Njcm9sbCd9KVswXTtcbiAgICB2YXIgc2Nyb2xsV2lkdGggPSB0bXAub2Zmc2V0V2lkdGgtdG1wLmNsaWVudFdpZHRoO1xuICAgIHZhciBzY3JvbGxIZWlnaHQgPSB0bXAub2Zmc2V0SGVpZ2h0LXRtcC5jbGllbnRIZWlnaHQ7XG4gICAgdG1wLnBhcmVudEVsZW1lbnQucmVtb3ZlQ2hpbGQodG1wKTtcblxuICAgIGlmIChvcHRpb25zLnZlcnRpY2FsKSB7XG4gICAgICAkaW5uZXIuY3NzKHtcbiAgICAgICAgLyogc2F2ZSB0aGUgcGFkZGluZyAqL1xuICAgICAgICBwYWRkaW5nTGVmdDogJGNvbnRhaW5lci5jc3MoJ3BhZGRpbmdMZWZ0JyksXG4gICAgICAgIHBhZGRpbmdSaWdodDogJGNvbnRhaW5lci5jc3MoJ3BhZGRpbmdSaWdodCcpLFxuICAgICAgICAvKiBoaWRlIHNjcm9sbHMgKi9cbiAgICAgICAgbWFyZ2luUmlnaHQ6IC1zY3JvbGxXaWR0aCsncHgnXG5cbiAgICAgIH0pO1xuICAgICAgJGNvbnRhaW5lci5jc3Moe1xuICAgICAgICBwYWRkaW5nTGVmdDogMCxcbiAgICAgICAgcGFkZGluZ1JpZ2h0OiAwXG4gICAgICB9KTtcbiAgICB9IGVsc2Uge1xuICAgICAgJGlubmVyLmNzcyh7b3ZlcmZsb3dZOiAnaGlkZGVuJ30pXG4gICAgfVxuICAgIGlmIChvcHRpb25zLmhvcml6b250YWwpIHtcbiAgICAgICRpbm5lci5jc3Moe1xuICAgICAgICAvKiBoaWRlIHNjcm9sbHMgKi9cbiAgICAgICAgbWFyZ2luQm90dG9tOiAtc2Nyb2xsSGVpZ2h0KydweCcsXG4gICAgICAgIHBhZGRpbmdCb3R0b206IHNjcm9sbEhlaWdodCsncHgnXG4gICAgICB9KTtcbiAgICAgICRjb250YWluZXIuY3NzKHtcbiAgICAgICAgcGFkZGluZ1RvcDogMCxcbiAgICAgICAgcGFkZGluZ0JvdHRvbTogMFxuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRpbm5lci5jc3Moe292ZXJmbG93WDogJ2hpZGRlbid9KVxuICAgIH1cblxuICAgIC8qIGluIGNhc2Ugb2YgbWF4LWhlaWdodCAqL1xuICAgIHZhciBtYXhIZWlnaHQgPSAkY29udGFpbmVyLmNzcygnbWF4SGVpZ2h0Jyk7XG4gICAgaWYgKHBhcnNlSW50KG1heEhlaWdodCkpIHtcbiAgICAgICRjb250YWluZXIuY3NzKCdtYXhIZWlnaHQnLCAnbm9uZScpO1xuICAgICAgJGlubmVyLmNzcygnbWF4SGVpZ2h0JywgbWF4SGVpZ2h0KTtcbiAgICB9XG5cblxuICAgICRjb250YWluZXIuc2Nyb2xsVG9wKDApO1xuXG5cbiAgICB2YXIgJGJvZHkgPSAkKCdib2R5Jyk7XG5cbiAgICB2YXIgJGJhcnMgPSB7fTtcbiAgICAkLmVhY2goZGlycywgaW5pdEJhcik7XG5cbiAgICAkaW5uZXIub24oJ3Njcm9sbCcsIHVwZGF0ZUJhcnMpO1xuICAgIHVwZGF0ZUJhcnMoKTtcbiAgICBpZiAob3B0aW9ucy5wcmV2ZW50UGFyZW50U2Nyb2xsKSBwcmV2ZW50UGFyZW50U2Nyb2xsKCk7XG5cbiAgICB2YXIgZGF0YSA9IHtcbiAgICAgICRjb250YWluZXI6ICRjb250YWluZXIsXG4gICAgICAkYmFyOiAkYmFycy55LFxuICAgICAgJGJhclg6ICRiYXJzLngsXG4gICAgICAkaW5uZXI6ICRpbm5lcixcbiAgICAgIGRlc3Ryb3k6IGRlc3Ryb3ksXG4gICAgICB1cGRhdGVCYXJzOiB1cGRhdGVCYXJzLFxuICAgICAgb3B0aW9uczogb3B0aW9uc1xuICAgIH07XG4gICAgJGNvbnRhaW5lci5kYXRhKCdjdXN0b20tc2Nyb2xsJywgZGF0YSk7XG4gICAgcmV0dXJuIGRhdGE7XG5cblxuICAgIGZ1bmN0aW9uIHByZXZlbnRQYXJlbnRTY3JvbGwoKSB7XG4gICAgICAkaW5uZXIub24oJ0RPTU1vdXNlU2Nyb2xsIG1vdXNld2hlZWwnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgIHZhciBzY3JvbGxUb3AgPSB0aGlzLnNjcm9sbFRvcDtcbiAgICAgICAgdmFyIHNjcm9sbEhlaWdodCA9IHRoaXMuc2Nyb2xsSGVpZ2h0O1xuICAgICAgICB2YXIgaGVpZ2h0ID0gJHRoaXMuaGVpZ2h0KCk7XG4gICAgICAgIHZhciBkZWx0YSA9IChlLnR5cGUgPT0gJ0RPTU1vdXNlU2Nyb2xsJyA/IGUub3JpZ2luYWxFdmVudC5kZXRhaWwgKiAtNDAgOiBlLm9yaWdpbmFsRXZlbnQud2hlZWxEZWx0YSk7XG4gICAgICAgIHZhciB1cCA9IGRlbHRhID4gMDtcblxuICAgICAgICBpZiAodXAgPyBzY3JvbGxUb3AgPT09IDAgOiBzY3JvbGxUb3AgPT09IHNjcm9sbEhlaWdodCAtIGhlaWdodCkge1xuICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gaW5pdEJhcihkaXJLZXksIGRpcikge1xuLy8gICAgICBjb25zb2xlLmxvZygnaW5pdEJhcicsIGRpcktleSwgZGlyKVxuLy8gICAgICB2YXIgZGlyID0gRElSU1tkaXJLZXldO1xuICAgICAgJGNvbnRhaW5lclsnc2Nyb2xsJyArIGRpci5EaXJdKDApO1xuXG4gICAgICB2YXIgY2xzID0gb3B0aW9ucy5wcmVmaXgrJ2JhcicrZGlyLnN1ZmZpeDtcbiAgICAgIHZhciAkYmFyID0gJGNvbnRhaW5lci5jaGlsZHJlbignLicrIGNscyk7XG4gICAgICBpZiAoISRiYXIubGVuZ3RoKSB7XG4gICAgICAgICRiYXIgPSAkKG9wdGlvbnMuYmFySHRtbCkuYWRkQ2xhc3MoY2xzKS5hcHBlbmRUbygkY29udGFpbmVyKTtcbiAgICAgIH1cblxuICAgICAgJGJhci5vbignbW91c2Vkb3duIHRvdWNoc3RhcnQnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTsgLy8gc3RvcCBzY3JvbGxpbmcgaW4gaWU5XG4gICAgICAgIHZhciBzY3JvbGxTdGFydCA9ICRpbm5lclsnc2Nyb2xsJyArIGRpci5EaXJdKCk7XG4gICAgICAgIHZhciBwb3NTdGFydCA9IGVbZGlyLmNsaWVudEF4aXNdIHx8IGUub3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlcyAmJiBlLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXNbMF1bZGlyLmNsaWVudEF4aXNdO1xuICAgICAgICB2YXIgcmF0aW8gPSBnZXREaW1zKGRpcktleSwgZGlyKS5yYXRpbztcblxuICAgICAgICAkYm9keS5vbignbW91c2Vtb3ZlLmN1c3RvbS1zY3JvbGwgdG91Y2htb3ZlLmN1c3RvbS1zY3JvbGwnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpOyAvLyBzdG9wIHNjcm9sbGluZ1xuICAgICAgICAgIHZhciBwb3MgPSBlW2Rpci5jbGllbnRBeGlzXSB8fCBlLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXMgJiYgZS5vcmlnaW5hbEV2ZW50LmNoYW5nZWRUb3VjaGVzWzBdW2Rpci5jbGllbnRBeGlzXTtcbiAgICAgICAgICAkaW5uZXJbJ3Njcm9sbCcgKyBkaXIuRGlyXShzY3JvbGxTdGFydCArIChwb3MtcG9zU3RhcnQpL3JhdGlvKTtcbiAgICAgICAgfSk7XG4gICAgICAgICRib2R5Lm9uKCdtb3VzZXVwLmN1c3RvbS1zY3JvbGwgdG91Y2hlbmQuY3VzdG9tLXNjcm9sbCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICRib2R5Lm9mZignLmN1c3RvbS1zY3JvbGwnKTtcbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICAgICRiYXJzW2RpcktleV0gPSAkYmFyO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGdldERpbXMoZGlyS2V5LCBkaXIpIHtcbi8vICAgICAgY29uc29sZS5sb2coJ2dldERpbXMnLCBkaXJLZXksIGRpcilcbiAgICAgIHZhciB0b3RhbCA9ICRpbm5lci5wcm9wKCdzY3JvbGwnICsgZGlyLkRpbSl8MDtcbiAgICAgIHZhciBkaW0gPSAkY29udGFpbmVyWydpbm5lcicgKyBkaXIuRGltXSgpO1xuICAgICAgdmFyIGlubmVyID0gJGlubmVyWydpbm5lcicgKyBkaXIuRGltXSgpO1xuICAgICAgdmFyIHNjcm9sbCA9IGRpbSAtIG9wdGlvbnNbJ29mZnNldCcgKyBkaXIuRGlyXSAtIG9wdGlvbnNbJ29mZnNldCcgKyBkaXIuRGlyMl07XG4gICAgICBpZiAoIWlzQmFySGlkZGVuW2RpcktleSA9PSAneCcgPyAneScgOiAneCddKSBzY3JvbGwgLT0gb3B0aW9uc1sndHJhY2snK2Rpci5EaW1dO1xuXG4gICAgICB2YXIgYmFyID0gTWF0aC5tYXgoKHNjcm9sbCpkaW0vdG90YWwpfDAsIG9wdGlvbnNbJ2Jhck1pbicgKyBkaXIuRGltXSk7XG4gICAgICB2YXIgcmF0aW8gPSAoc2Nyb2xsLWJhcikvKHRvdGFsLWlubmVyKTtcbi8vICAgICAgaWYgKGRpcktleSA9PSAneScgJiYgJGNvbnRhaW5lci5pcygnI2V4YW1wbGUtaGFyZCcpKSBjb25zb2xlLmxvZygnZGltJywgZGltLCBpbm5lciwgc2Nyb2xsLCB0b3RhbCwgYmFyLCByYXRpbylcblxuICAgICAgcmV0dXJuIHtcbiAgICAgICAgcmF0aW86IHJhdGlvLFxuICAgICAgICBkaW06IGRpbSxcbiAgICAgICAgc2Nyb2xsOiBzY3JvbGwsXG4gICAgICAgIHRvdGFsOiB0b3RhbCxcbiAgICAgICAgYmFyOiBiYXJcbiAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiB1cGRhdGVCYXJzKCkge1xuICAgICAgdHJ5IHtcbiAgICAgICAgICBza2lucy5pdGVtc1dyYXBTaG93SWNvbnMoKTtcbiAgICAgIH0gY2F0Y2ggKGVycm9yKSB7XG4gICAgICAgICAgY29uc29sZS5sb2coZXJyb3IudG9TdHJpbmcoKSk7XG4gICAgICB9XG5cbiAgICAgICQuZWFjaChkaXJzLCB1cGRhdGVCYXIpO1xuICAgIH1cbiAgICBmdW5jdGlvbiB1cGRhdGVCYXIoZGlyS2V5LCBkaXIpIHtcbi8vICAgICAgdmFyIGRpciA9IERJUlNbZGlyS2V5XTtcbiAgICAgIHZhciBkaW1zID0gZ2V0RGltcyhkaXJLZXksIGRpcik7XG4gICAgICBpZiAoIWRpbXMudG90YWwpIHJldHVybjtcblxuICAgICAgdmFyIHNjcm9sbFBvcyA9ICRpbm5lclsnc2Nyb2xsJyArIGRpci5EaXJdKCk7XG4gICAgICBpZiAoXG4gICAgICAgIGxhc3REaW1zW2RpcktleV0uc2Nyb2xsUG9zID09PSBzY3JvbGxQb3MgJiZcbiAgICAgICAgbGFzdERpbXNbZGlyS2V5XS5zY3JvbGwgPT09IGRpbXMuc2Nyb2xsICYmXG4gICAgICAgIGxhc3REaW1zW2RpcktleV0udG90YWwgPT09IGRpbXMudG90YWxcbiAgICAgICkgcmV0dXJuO1xuICAgICAgbGFzdERpbXNbZGlyS2V5XSA9IGRpbXM7XG4gICAgICBsYXN0RGltc1tkaXJLZXldLnNjcm9sbFBvcyA9IHNjcm9sbFBvcztcblxuXG4gICAgICB2YXIgaXNIaWRlID0gZGltcy5iYXI+PWRpbXMuc2Nyb2xsO1xuICAgICAgaWYgKGlzSGlkZSE9PWlzQmFySGlkZGVuW2RpcktleV0pIHtcbiAgICAgICAgJGNvbnRhaW5lci50b2dnbGVDbGFzcyhvcHRpb25zLnByZWZpeCsnaGlkZGVuJytkaXIuc3VmZml4LCBpc0hpZGUpO1xuICAgICAgICBpc0JhckhpZGRlbltkaXJLZXldID0gaXNIaWRlO1xuICAgICAgfVxuICAgICAgdmFyIGJhclBvcyA9IHNjcm9sbFBvcypkaW1zLnJhdGlvO1xuLy8gICAgICBjb25zb2xlLmxvZygndXBkJywgc2Nyb2xsUG9zLCBkaW1zLnJhdGlvLCBiYXJQb3MpXG4gICAgICAvL2lmIChkaXJLZXkgPT09ICd5JykgY29uc29sZS5sb2coYmFyUG9zLCBkaW1zLnNjcm9sbCwgZGltcy5iYXIsIGRpbXMpXG4gICAgICBpZiAoYmFyUG9zPDApIGJhclBvcyA9IDA7XG4gICAgICBpZiAoYmFyUG9zPmRpbXMuc2Nyb2xsLWRpbXMuYmFyKSBiYXJQb3MgPSBkaW1zLnNjcm9sbC1kaW1zLmJhcjtcbiAgICAgICRiYXJzW2RpcktleV1bZGlyLmRpbV0oZGltcy5iYXIpXG4gICAgICAgIC5jc3MoZGlyLmRpciwgb3B0aW9uc1snb2Zmc2V0JyArIGRpci5EaXJdICsgYmFyUG9zKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBkZXN0cm95KCkge1xuICAgICAgJC5lYWNoKGRpcnMsIGZ1bmN0aW9uKGtleSkgeyAkYmFyc1trZXldLnJlbW92ZSgpOyB9KTtcbiAgICAgICRjb250YWluZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKG9wdGlvbnMucHJlZml4Kydjb250YWluZXInKVxuICAgICAgICAucmVtb3ZlRGF0YSgnY3VzdG9tLXNjcm9sbCcpXG4gICAgICAgIC5jc3Moe3BhZGRpbmc6ICcnLCBtYXhIZWlnaHQ6ICcnfSk7XG4gICAgICAkaW5uZXIuY29udGVudHMoKS5hcHBlbmRUbygkY29udGFpbmVyKTtcbiAgICAgICRpbm5lci5yZW1vdmUoKTtcbiAgICB9XG4gIH1cbn0pKGpRdWVyeSk7Il0sImZpbGUiOiJqcXVlcnkuY3VzdG9tLXNjcm9sbC5qcyJ9
