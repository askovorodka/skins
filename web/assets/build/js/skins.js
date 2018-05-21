
var skins = {
    filterText: '',
    filterAppId: '',
    finalPrice: 0,
    countSellItems: 0,
    skinsCountUser: 0,
    invAllPrice: 0,
    depositInfo: {},
    windowWidth: window.innerWidth,
    finalPriceBl: $('#js-final-value'),
    finalPriceInput: $('#inventory_itemsValue'),
    rarity: {
        'Base Grade': 1,
        'Consumer Grade': 2,
        'Mil-Spec Grade': 3,
        'Restricted': 4,
        'Covert': 5,
        'Industrial Grade': 6,
        'Classified': 7
    },
    sum: function () {
        var result = 0;
        for (var i = 0, max = arguments.length; i < max; i++) {
            result += arguments[i] * 10;
        }
        return (result / 10).toFixed(2);
    },
    toggleType: function (id) {
        var item = this.initObj[id];
        if (item.type == 1) {
            item.type = 2;
        }
        else {
            item.type = 1;
        }
        this.renderItems();
    },
    submitForm: function () {
        var self = this;
        var finalInventory = [];
        $('#skinsSubmit').addClass('clicked');
        for (p in self.initObj) {
            if (self.initObj[p].type == 2) {
                finalInventory.push({id:self.initObj[p].id, market_hash_name: self.initObj[p].market_hash_name});
            }
        }
        var sending = {
            deposit_id: depositId,
            items: finalInventory
        };

        $.ajax({
            url: Routing.generate('inventory_submit', {'_locale': locale}) + '?token='+csrfToken,
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify(sending),
            success: function (res) {
                if (res.status == 'success') {
                    $('#skinsBlLast, #tSent').removeClass('hide');
                    $('#skinsBl').addClass('hide');
                    self.initTimer();
                    self.worker();
                    $('#confirmTradeLink').attr('href', res.confirm_trade_offer_url)
                    $('#tradeHash').html(res.deposit.trade_hash);
                } else {
                    $('#skinsBl').addClass('hide');
                    $('.skinsFinalFail, .container.oneColl').removeClass('hide');
                    $('#skinsFinalFail-message').html(res.message)
                }
            }
        });
    },
    clickItems: function (item) {
        this.toggleType($(item).attr('data-index'));
    },
    filterItems: function (findName) {
        var self = this;
        self.filterText = findName;
        self.renderItems();
    },
    itemsWrapShowIcons: function(){
        try {
            var bottomLine = $(".custom-scroll_inner").height() + $(".custom-scroll_inner").scrollTop();

            if (isNaN(bottomLine)) {
                throw new Error('bottom line isNan');
            }

            bottomLine += 50;
            var showedElements = $(".skinsItem").filter(function () {
                var bottomElement = ($(this).offset().top - 237) + 100;
                return bottomElement <= bottomLine;
            });
            $(showedElements).each(function () {
                if (!$(".skinsItem__img__i", $(this)).find("img").length) {
                    $(".skinsItem__img__i", $(this)).append($("<img />").attr("src", $(".skinsItem__img__i", $(this)).data('icon')));
                }
            })
        } catch (error) {
            console.log('Error in load icons: ' + error.toString());
            $(".skinsItem").each(function(){
                if (!$(".skinsItem__img__i", $(this)).find("img").length) {
                    $(".skinsItem__img__i", $(this)).append($("<img />").attr("src", $(".skinsItem__img__i", $(this)).data('icon')));
                }
            })
        }
    },
    renderItems: function (items) {
        var self = this,
            html = '',
            htmlDis = '',
            sellHtml = '';
        self.finalPrice = 0;
        self.countSellItems = 0;

        if (items === undefined) {
            items = this.initObj
        }
        if (self.windowWidth > 1000) {
            $('.skins__items').customScroll('destroy');
        }

        for (i in items) {
            var activeClass = '',
                acceptable = 'skinsItem--acceptable',
                disableBl = '';
            if (items[i].type == 2 && self.windowWidth < 1000) {
                activeClass = 'skinsItem--active';
            }
            if (items[i].acceptable == false) {
                acceptable = 'skinsItem--deceptable';
                disableBl = '<div class="skinsItem__disableBl"></div>';
            }
            var title = items[i].acceptable == false ? unacceptableTranslate : items[i].market_name;
            var it = '<div class="skinsItem ' + activeClass + ' ' + acceptable + '" data-id=' + items[i].id + '" data-index=' + i + ' data-app_id=' + items[i].app_id + '>' + disableBl +
                '<div class="skinsItem__name">' + title + '</div>' +
                '<div class="skinsItem__img">' +
                '<div class="skinsItem__img__circle" style="border-color: #' + items[i].color + '"></div>' +
                '<div class="skinsItem__img__i" data-icon="' + items[i].icon_url + '100x100">' +
                //'<img src="' + items[i].icon_url + '100x100" alt="">' +
                '</div>' +
                '</div>' +
                '<div class="skinsItem__price">' + items[i].price + ' ' + currency + '</div>' +
                '</div>';

            var marketHashName = items[i].market_hash_name.toLowerCase();
            var marketName;
            if (items[i].market_name == null) {
                marketName = items[i].market_hash_name.toLowerCase();
            } else {
                marketName = items[i].market_name.toLowerCase();
            }
            var itemAppId = items[i].app_id;

            var disabled = false;
            if (self.filterText != '') {
                if (marketName.indexOf(self.filterText.toLocaleLowerCase()) < 0){
                    disabled = true;
                }
                if (marketHashName.indexOf(self.filterText.toLocaleLowerCase()) < 0){
                    disabled = true;
                }
            }
            if (self.filterAppId != ''){
                if (self.filterAppId != itemAppId){
                    disabled = true;
                }
            }
            if (items[i].type == 1
                && !disabled
                ) {
                if (items[i].acceptable == true) {
                    html += it;
                }
                else {
                    //htmlDis += it;
                }
            }
            else if (items[i].type == 2) {
                if (self.windowWidth > 1000) {
                    sellHtml += it;
                }
                else {
                    html += it;
                }
                self.countSellItems++;
                self.finalPrice = self.sum(self.finalPrice, parseFloat(items[i].price_raw));
            }
        }
        html = html + htmlDis;
        $(self.finalPriceInput).val(self.finalPrice);
        $(self.finalPriceBl).html(parseFloat(self.finalPrice).toFixed(2) + ' ' + currency);
        $('#countSellItems').html(self.countSellItems);
        $(countSellItemsMob).html(self.countSellItems);
        this.itemsWrap.innerHTML = html;
        this.itemsWrapSell.innerHTML = sellHtml;
        this.clickableItems = $('.skinsItem.skinsItem--acceptable');
        if (self.countSellItems > 0) {
            self.skinsSubmit.classList.remove('disabled');
        }
        else {
            self.skinsSubmit.classList.add('disabled');
        }
        $(this.clickableItems).on('click', function () {
            self.clickItems(this);
        });

        $('#skinsCountSum').html(parseFloat(self.invAllPrice).toFixed(2) + ' ' + currency);
        $('#skinsCountUser').html(self.skinsCountUser);
        $('#countSellItemsMob').html(self.countSellItems);
        $('#countSellItems').html(self.countSellItems);

        if (self.windowWidth > 1000) {
            $('.skins__items').customScroll();
        }
        else {
            $('.skins__items').customScroll('destroy');
        }

        this.itemsWrapShowIcons();
        this.itemsWrap.addEventListener("scroll", function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.itemsWrapShowIcons();
        }.bind(this));

    },
    getTimeRemaining: function (endtime) {
        var t = Date.parse(endtime) - Date.parse(new Date());
        var seconds = Math.floor((t / 1000) % 60);
        var minutes = Math.floor((t / 1000 / 60) % 60);
        return {
            'total': t,
            'minutes': minutes,
            'seconds': seconds
        };
    },
    updateClock: function () {
        var self = this;
        var t = this.getTimeRemaining(this.deadline);
        timerMin.innerHTML = t.minutes;
        timerSec.innerHTML = t.seconds;
        if (t.total <= 0) {
            self.worker();
            clearInterval(self.timeinterval);
        }
    },
    initTimer: function () {
        var self = this;
        if ($.cookie('date' + depositId) == undefined) { // куки нет
            this.deadline = new Date();
            this.deadline.setMinutes(this.deadline.getMinutes() + 3);
            $.cookie('date' + depositId, this.deadline, {expires: this.deadline, path: '/'});
        }
        else {
            this.deadline = $.cookie('date' + depositId);
        }
        this.updateClock();
        this.timeinterval = setInterval(function () {
            self.updateClock();
        }, 1000);
    },
    changeLang: function (lang) {
        window.location.href = location.origin + '/' + lang + '/' + location.search;
    },
    checkSteamStatus: function () {
        $.ajax({
            url: Routing.generate('is_steam_rip', {'_locale': locale}),
            type: 'GET',
            complete: function (res) {
                if (typeof res.responseJSON.status == 'string')
                {
                    if (typeof res.responseJSON.message == 'string')
                    {
                        $(".head__steam__circle").addClass('circle__' + res.responseJSON.status);
                        $('#steamStatus').addClass(res.responseJSON.status).text(res.responseJSON.message);
                    }
                }
            }
        });
    },
    worker: function () {
        $.ajax({
            url: Routing.generate('check_deposit', {'_locale': locale, 'depositId': depositId}),
            success: function (data) {
                if (data.action == 'show_success') {
                    $('#js-deposit-amount').text(data.amount);
                    $('.tSent').addClass('hide');
                    $('.skinsFinalSuccess').removeClass('hide');
                }
                else if (data.action == 'show_fail') {
                    $('.tSent').addClass('hide');
                    $('.skinsFinalFail').removeClass('hide');
                    $('#skinsFinalFail-message').html(data.message)
                }
                else if (data.action == 'show_pending') {
                    setTimeout(skins.worker, 10000);
                }
            }
        });
    },
    loadInventory: function () {
        var self = this;
        $.ajax({
            url: Routing.generate('inventory', {'_locale': locale}),
            type: 'GET',
            success: function (res) {
                if (res.status == 'success') {
                    skins.responseItems = res.inventory.items;
                    skins.responseItemsValue = res.inventory.itemsValue;
                    self.depositInfo = res.inventory.deposit;
                    $('#confirmTradeLink').attr('href', res.confirm_trade_offer_url);
                    skins.init();
                }
                else {
                    $('#invtLoadErr').html('<p>' + res.message + '</p>').removeClass('hide');
                    $('#skinsBlPre').addClass('error');
                }
            }
        });
    },
    preInit: function () {
        this.depositInfo.status = typeof depositStatus !== 'undefined' ? depositStatus : null;
        this.checkSteamStatus();

        if (this.depositInfo.status == 'new' || this.depositInfo.status == 'error_inventory') {
            this.loadInventory();
        }
        if (this.depositInfo.status == 'pending' || this.depositInfo.status == 'error_bot' || this.depositInfo.status == 'error_unacceptable_item') {
            this.init();
        }
    },
    init: function () {
        var self = this;

        $('#skinsBlPre').hide();

        if (self.depositInfo.status == 'new' || self.depositInfo.status == 'error_inventory') {
            $('#skinsBl').removeClass('hide');
            self.skinsSubmit = document.getElementById('skinsSubmit');

            $('.skins__items').customScroll();

            this.itemsWrap = document.getElementById('skinsItemsWrap');
            this.itemsWrapSell = document.getElementById('skinsItemsWrapSell');

            var initItems = $('.skinsItem');
            this.initObj = {};

            for (var i = 0; i < self.responseItems.length; i++) {
                //self.initObj[self.responseItems[i].id] = {
                self.initObj[i] = {
                    id: self.responseItems[i].id,
                    icon_url: self.responseItems[i].icon_url,
                    market_name: self.responseItems[i].market_name,
                    market_hash_name: self.responseItems[i].market_hash_name,
                    price: self.responseItems[i].price,
                    price_raw: self.responseItems[i].price_raw,
                    color: self.responseItems[i].color,
                    rarity: self.responseItems[i].rarity,
                    type: self.responseItems[i].type,
                    app_id: self.responseItems[i].app_id,
                    acceptable: self.responseItems[i].acceptable
                };
                if (self.responseItems[i].acceptable) {
                    self.skinsCountUser++;
                }
                self.invAllPrice = self.responseItemsValue;

            }

            self.renderItems();

            var timeOutId;

            window.addEventListener('resize', function () {
                clearInterval(timeOutId);
                timeOutId = setTimeout(function () {
                    self.windowWidth = window.innerWidth;
                    self.renderItems();
                }, 500);
            });

            document.getElementById('skinsSearch').oninput = function () {
                if (this.value.length > 0) {
                    $('label[for=skinsSearch]').hide()
                }
                else {
                    $('label[for=skinsSearch]').show()
                }
                self.filterItems(this.value);
            };

            document.getElementById('filterByAppId').addEventListener('change', function(event){
                self.filterAppId = event.target.value;
                self.filterItems(self.filterText);
            });

        }
        else if (self.depositInfo.status == 'pending') {
            self.initTimer();
            self.worker();
            $('#skinsBlLast, #tSent').removeClass('hide');
        }
        else if (self.depositInfo.status == 'completed') {
            $('#skinsBlLast, #skinsFinalSuccess').removeClass('hide');
        }
        else if (self.depositInfo.status == 'error_bot' || self.depositInfo.status == 'error_unacceptable_item') {
            $('#skinsBlLast, #skinsFinalFail').removeClass('hide');
        }
    }
};

$(document).ready(skins.preInit());

var modaln = {
    open: function (id, that) {

        if ($('#last_popup').length && id.indexOf('modal-task-pop') < 0 && id.indexOf('modal-result-task') < 0 && id.indexOf('modal-semenih') < 0) id = '#modal-semenih2';

        $('body').css('top', -RealScroll);
        if (id == '#modal-result-task') {
            var people_item = $(that).parents('[data-avatar]');
            $('#modal-result-task .name span').text(people_item.attr('data-name'));
            $('#modal-result-task .modal-result-task-name').attr('href', people_item.attr('data-link'));

            $vote_btn = $('#modal-result-task .result-task-vote-btn');
            $vote_btn.attr('data-id', people_item.attr('data-id'));
            var user_voted = people_item.attr('data-user-voted');
            if (user_voted == '1') {
                $vote_btn.find('span').html('Вы уже голосовали');
                $vote_btn.addClass('cant_vote');
            } else {
                $vote_btn.find('span').html('Проголосуй');
                $vote_btn.removeClass('cant_vote');
            }


            $('#modal-result-task .description').text(people_item.attr('data-text'));
            $('#modal-result-task .image img').attr('src', people_item.attr('data-photo'));
            $('#modal-result-task .likes').html('<i></i> ' + people_item.attr('data-likes'));
            $('#modal-result-task .modal-result-task-name .img').css('background-image', 'url(' + people_item.attr('data-avatar') + ')');
        }

        if (id == '#modal-delete') {
            $('#modal-delete a').attr('href', '/lk?delete_post=' + $(that).attr('data-id-delete'));
        }

        $('body').addClass('modalnOpened');
        $('html').addClass('fixed');
        $(id).show();
    },
    close: function (e, that) {
        bodyElem.css('top', 0);
        bodyElem.scrollTop(RealScroll);
        if (!$(e.target).closest('.modaln-content').length) {
            $('body').removeClass('modalnOpened');
            $('html').removeClass('fixed');
            if ($(that).hasClass('modaln')) $(that).hide();
            else $(that).parents('.modaln').hide();
        }
    }
};

var RealScroll = 0;
var bodyElem = $('body');
$(window).scroll(function () {
    CurrentScroll = $(window).scrollTop();
    if (CurrentScroll !== 0) {
        RealScroll = CurrentScroll;
    }
});

$(document).on('click', '.modaln .close, .btn.back, .modaln .btn-close', function () {
    $(this).parents('.modaln').hide();
    bodyElem.removeClass('modalnOpened');
    bodyElem.css('top', 0);
    bodyElem.scrollTop(RealScroll);
});
$(document).on('click', '[data-modal]', function () {
    modaln.open('#' + $(this).attr('data-modal-name'), this);
});
$(document).on('click', '.modal', function (e) {
    if (!$(e.target).closest('.modal-content').length) {
        $('.modal').hide();
        $('body').removeClass('modalnOpened');
        $('html').removeClass('fixed');
    }
});
$(document).on('click', '.close', function (e) {
    $('.modal').hide();
    $('body').removeClass('modalnOpened');
    $('html').removeClass('fixed');
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJza2lucy5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJcbnZhciBza2lucyA9IHtcbiAgICBmaWx0ZXJUZXh0OiAnJyxcbiAgICBmaWx0ZXJBcHBJZDogJycsXG4gICAgZmluYWxQcmljZTogMCxcbiAgICBjb3VudFNlbGxJdGVtczogMCxcbiAgICBza2luc0NvdW50VXNlcjogMCxcbiAgICBpbnZBbGxQcmljZTogMCxcbiAgICBkZXBvc2l0SW5mbzoge30sXG4gICAgd2luZG93V2lkdGg6IHdpbmRvdy5pbm5lcldpZHRoLFxuICAgIGZpbmFsUHJpY2VCbDogJCgnI2pzLWZpbmFsLXZhbHVlJyksXG4gICAgZmluYWxQcmljZUlucHV0OiAkKCcjaW52ZW50b3J5X2l0ZW1zVmFsdWUnKSxcbiAgICByYXJpdHk6IHtcbiAgICAgICAgJ0Jhc2UgR3JhZGUnOiAxLFxuICAgICAgICAnQ29uc3VtZXIgR3JhZGUnOiAyLFxuICAgICAgICAnTWlsLVNwZWMgR3JhZGUnOiAzLFxuICAgICAgICAnUmVzdHJpY3RlZCc6IDQsXG4gICAgICAgICdDb3ZlcnQnOiA1LFxuICAgICAgICAnSW5kdXN0cmlhbCBHcmFkZSc6IDYsXG4gICAgICAgICdDbGFzc2lmaWVkJzogN1xuICAgIH0sXG4gICAgc3VtOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciByZXN1bHQgPSAwO1xuICAgICAgICBmb3IgKHZhciBpID0gMCwgbWF4ID0gYXJndW1lbnRzLmxlbmd0aDsgaSA8IG1heDsgaSsrKSB7XG4gICAgICAgICAgICByZXN1bHQgKz0gYXJndW1lbnRzW2ldICogMTA7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIChyZXN1bHQgLyAxMCkudG9GaXhlZCgyKTtcbiAgICB9LFxuICAgIHRvZ2dsZVR5cGU6IGZ1bmN0aW9uIChpZCkge1xuICAgICAgICB2YXIgaXRlbSA9IHRoaXMuaW5pdE9ialtpZF07XG4gICAgICAgIGlmIChpdGVtLnR5cGUgPT0gMSkge1xuICAgICAgICAgICAgaXRlbS50eXBlID0gMjtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgIGl0ZW0udHlwZSA9IDE7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXJJdGVtcygpO1xuICAgIH0sXG4gICAgc3VibWl0Rm9ybTogZnVuY3Rpb24gKCkge1xuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgICAgIHZhciBmaW5hbEludmVudG9yeSA9IFtdO1xuICAgICAgICAkKCcjc2tpbnNTdWJtaXQnKS5hZGRDbGFzcygnY2xpY2tlZCcpO1xuICAgICAgICBmb3IgKHAgaW4gc2VsZi5pbml0T2JqKSB7XG4gICAgICAgICAgICBpZiAoc2VsZi5pbml0T2JqW3BdLnR5cGUgPT0gMikge1xuICAgICAgICAgICAgICAgIGZpbmFsSW52ZW50b3J5LnB1c2goe2lkOnNlbGYuaW5pdE9ialtwXS5pZCwgbWFya2V0X2hhc2hfbmFtZTogc2VsZi5pbml0T2JqW3BdLm1hcmtldF9oYXNoX25hbWV9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICB2YXIgc2VuZGluZyA9IHtcbiAgICAgICAgICAgIGRlcG9zaXRfaWQ6IGRlcG9zaXRJZCxcbiAgICAgICAgICAgIGl0ZW1zOiBmaW5hbEludmVudG9yeVxuICAgICAgICB9O1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB1cmw6IFJvdXRpbmcuZ2VuZXJhdGUoJ2ludmVudG9yeV9zdWJtaXQnLCB7J19sb2NhbGUnOiBsb2NhbGV9KSArICc/dG9rZW49Jytjc3JmVG9rZW4sXG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgZGF0YTogSlNPTi5zdHJpbmdpZnkoc2VuZGluZyksXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzKSB7XG4gICAgICAgICAgICAgICAgaWYgKHJlcy5zdGF0dXMgPT0gJ3N1Y2Nlc3MnKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJyNza2luc0JsTGFzdCwgI3RTZW50JykucmVtb3ZlQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnI3NraW5zQmwnKS5hZGRDbGFzcygnaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICBzZWxmLmluaXRUaW1lcigpO1xuICAgICAgICAgICAgICAgICAgICBzZWxmLndvcmtlcigpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjY29uZmlybVRyYWRlTGluaycpLmF0dHIoJ2hyZWYnLCByZXMuY29uZmlybV90cmFkZV9vZmZlcl91cmwpXG4gICAgICAgICAgICAgICAgICAgICQoJyN0cmFkZUhhc2gnKS5odG1sKHJlcy5kZXBvc2l0LnRyYWRlX2hhc2gpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQoJyNza2luc0JsJykuYWRkQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnLnNraW5zRmluYWxGYWlsLCAuY29udGFpbmVyLm9uZUNvbGwnKS5yZW1vdmVDbGFzcygnaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjc2tpbnNGaW5hbEZhaWwtbWVzc2FnZScpLmh0bWwocmVzLm1lc3NhZ2UpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9LFxuICAgIGNsaWNrSXRlbXM6IGZ1bmN0aW9uIChpdGVtKSB7XG4gICAgICAgIHRoaXMudG9nZ2xlVHlwZSgkKGl0ZW0pLmF0dHIoJ2RhdGEtaW5kZXgnKSk7XG4gICAgfSxcbiAgICBmaWx0ZXJJdGVtczogZnVuY3Rpb24gKGZpbmROYW1lKSB7XG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgICAgc2VsZi5maWx0ZXJUZXh0ID0gZmluZE5hbWU7XG4gICAgICAgIHNlbGYucmVuZGVySXRlbXMoKTtcbiAgICB9LFxuICAgIGl0ZW1zV3JhcFNob3dJY29uczogZnVuY3Rpb24oKXtcbiAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgIHZhciBib3R0b21MaW5lID0gJChcIi5jdXN0b20tc2Nyb2xsX2lubmVyXCIpLmhlaWdodCgpICsgJChcIi5jdXN0b20tc2Nyb2xsX2lubmVyXCIpLnNjcm9sbFRvcCgpO1xuXG4gICAgICAgICAgICBpZiAoaXNOYU4oYm90dG9tTGluZSkpIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ2JvdHRvbSBsaW5lIGlzTmFuJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGJvdHRvbUxpbmUgKz0gNTA7XG4gICAgICAgICAgICB2YXIgc2hvd2VkRWxlbWVudHMgPSAkKFwiLnNraW5zSXRlbVwiKS5maWx0ZXIoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciBib3R0b21FbGVtZW50ID0gKCQodGhpcykub2Zmc2V0KCkudG9wIC0gMjM3KSArIDEwMDtcbiAgICAgICAgICAgICAgICByZXR1cm4gYm90dG9tRWxlbWVudCA8PSBib3R0b21MaW5lO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAkKHNob3dlZEVsZW1lbnRzKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoISQoXCIuc2tpbnNJdGVtX19pbWdfX2lcIiwgJCh0aGlzKSkuZmluZChcImltZ1wiKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgJChcIi5za2luc0l0ZW1fX2ltZ19faVwiLCAkKHRoaXMpKS5hcHBlbmQoJChcIjxpbWcgLz5cIikuYXR0cihcInNyY1wiLCAkKFwiLnNraW5zSXRlbV9faW1nX19pXCIsICQodGhpcykpLmRhdGEoJ2ljb24nKSkpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pXG4gICAgICAgIH0gY2F0Y2ggKGVycm9yKSB7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnRXJyb3IgaW4gbG9hZCBpY29uczogJyArIGVycm9yLnRvU3RyaW5nKCkpO1xuICAgICAgICAgICAgJChcIi5za2luc0l0ZW1cIikuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgIGlmICghJChcIi5za2luc0l0ZW1fX2ltZ19faVwiLCAkKHRoaXMpKS5maW5kKFwiaW1nXCIpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAkKFwiLnNraW5zSXRlbV9faW1nX19pXCIsICQodGhpcykpLmFwcGVuZCgkKFwiPGltZyAvPlwiKS5hdHRyKFwic3JjXCIsICQoXCIuc2tpbnNJdGVtX19pbWdfX2lcIiwgJCh0aGlzKSkuZGF0YSgnaWNvbicpKSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgfVxuICAgIH0sXG4gICAgcmVuZGVySXRlbXM6IGZ1bmN0aW9uIChpdGVtcykge1xuICAgICAgICB2YXIgc2VsZiA9IHRoaXMsXG4gICAgICAgICAgICBodG1sID0gJycsXG4gICAgICAgICAgICBodG1sRGlzID0gJycsXG4gICAgICAgICAgICBzZWxsSHRtbCA9ICcnO1xuICAgICAgICBzZWxmLmZpbmFsUHJpY2UgPSAwO1xuICAgICAgICBzZWxmLmNvdW50U2VsbEl0ZW1zID0gMDtcblxuICAgICAgICBpZiAoaXRlbXMgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgaXRlbXMgPSB0aGlzLmluaXRPYmpcbiAgICAgICAgfVxuICAgICAgICBpZiAoc2VsZi53aW5kb3dXaWR0aCA+IDEwMDApIHtcbiAgICAgICAgICAgICQoJy5za2luc19faXRlbXMnKS5jdXN0b21TY3JvbGwoJ2Rlc3Ryb3knKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAoaSBpbiBpdGVtcykge1xuICAgICAgICAgICAgdmFyIGFjdGl2ZUNsYXNzID0gJycsXG4gICAgICAgICAgICAgICAgYWNjZXB0YWJsZSA9ICdza2luc0l0ZW0tLWFjY2VwdGFibGUnLFxuICAgICAgICAgICAgICAgIGRpc2FibGVCbCA9ICcnO1xuICAgICAgICAgICAgaWYgKGl0ZW1zW2ldLnR5cGUgPT0gMiAmJiBzZWxmLndpbmRvd1dpZHRoIDwgMTAwMCkge1xuICAgICAgICAgICAgICAgIGFjdGl2ZUNsYXNzID0gJ3NraW5zSXRlbS0tYWN0aXZlJztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChpdGVtc1tpXS5hY2NlcHRhYmxlID09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgYWNjZXB0YWJsZSA9ICdza2luc0l0ZW0tLWRlY2VwdGFibGUnO1xuICAgICAgICAgICAgICAgIGRpc2FibGVCbCA9ICc8ZGl2IGNsYXNzPVwic2tpbnNJdGVtX19kaXNhYmxlQmxcIj48L2Rpdj4nO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdmFyIHRpdGxlID0gaXRlbXNbaV0uYWNjZXB0YWJsZSA9PSBmYWxzZSA/IHVuYWNjZXB0YWJsZVRyYW5zbGF0ZSA6IGl0ZW1zW2ldLm1hcmtldF9uYW1lO1xuICAgICAgICAgICAgdmFyIGl0ID0gJzxkaXYgY2xhc3M9XCJza2luc0l0ZW0gJyArIGFjdGl2ZUNsYXNzICsgJyAnICsgYWNjZXB0YWJsZSArICdcIiBkYXRhLWlkPScgKyBpdGVtc1tpXS5pZCArICdcIiBkYXRhLWluZGV4PScgKyBpICsgJyBkYXRhLWFwcF9pZD0nICsgaXRlbXNbaV0uYXBwX2lkICsgJz4nICsgZGlzYWJsZUJsICtcbiAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cInNraW5zSXRlbV9fbmFtZVwiPicgKyB0aXRsZSArICc8L2Rpdj4nICtcbiAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cInNraW5zSXRlbV9faW1nXCI+JyArXG4gICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJza2luc0l0ZW1fX2ltZ19fY2lyY2xlXCIgc3R5bGU9XCJib3JkZXItY29sb3I6ICMnICsgaXRlbXNbaV0uY29sb3IgKyAnXCI+PC9kaXY+JyArXG4gICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJza2luc0l0ZW1fX2ltZ19faVwiIGRhdGEtaWNvbj1cIicgKyBpdGVtc1tpXS5pY29uX3VybCArICcxMDB4MTAwXCI+JyArXG4gICAgICAgICAgICAgICAgLy8nPGltZyBzcmM9XCInICsgaXRlbXNbaV0uaWNvbl91cmwgKyAnMTAweDEwMFwiIGFsdD1cIlwiPicgK1xuICAgICAgICAgICAgICAgICc8L2Rpdj4nICtcbiAgICAgICAgICAgICAgICAnPC9kaXY+JyArXG4gICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJza2luc0l0ZW1fX3ByaWNlXCI+JyArIGl0ZW1zW2ldLnByaWNlICsgJyAnICsgY3VycmVuY3kgKyAnPC9kaXY+JyArXG4gICAgICAgICAgICAgICAgJzwvZGl2Pic7XG5cbiAgICAgICAgICAgIHZhciBtYXJrZXRIYXNoTmFtZSA9IGl0ZW1zW2ldLm1hcmtldF9oYXNoX25hbWUudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgIHZhciBtYXJrZXROYW1lO1xuICAgICAgICAgICAgaWYgKGl0ZW1zW2ldLm1hcmtldF9uYW1lID09IG51bGwpIHtcbiAgICAgICAgICAgICAgICBtYXJrZXROYW1lID0gaXRlbXNbaV0ubWFya2V0X2hhc2hfbmFtZS50b0xvd2VyQ2FzZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBtYXJrZXROYW1lID0gaXRlbXNbaV0ubWFya2V0X25hbWUudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHZhciBpdGVtQXBwSWQgPSBpdGVtc1tpXS5hcHBfaWQ7XG5cbiAgICAgICAgICAgIHZhciBkaXNhYmxlZCA9IGZhbHNlO1xuICAgICAgICAgICAgaWYgKHNlbGYuZmlsdGVyVGV4dCAhPSAnJykge1xuICAgICAgICAgICAgICAgIGlmIChtYXJrZXROYW1lLmluZGV4T2Yoc2VsZi5maWx0ZXJUZXh0LnRvTG9jYWxlTG93ZXJDYXNlKCkpIDwgMCl7XG4gICAgICAgICAgICAgICAgICAgIGRpc2FibGVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKG1hcmtldEhhc2hOYW1lLmluZGV4T2Yoc2VsZi5maWx0ZXJUZXh0LnRvTG9jYWxlTG93ZXJDYXNlKCkpIDwgMCl7XG4gICAgICAgICAgICAgICAgICAgIGRpc2FibGVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoc2VsZi5maWx0ZXJBcHBJZCAhPSAnJyl7XG4gICAgICAgICAgICAgICAgaWYgKHNlbGYuZmlsdGVyQXBwSWQgIT0gaXRlbUFwcElkKXtcbiAgICAgICAgICAgICAgICAgICAgZGlzYWJsZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChpdGVtc1tpXS50eXBlID09IDFcbiAgICAgICAgICAgICAgICAmJiAhZGlzYWJsZWRcbiAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICBpZiAoaXRlbXNbaV0uYWNjZXB0YWJsZSA9PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgICAgIGh0bWwgKz0gaXQ7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAvL2h0bWxEaXMgKz0gaXQ7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZiAoaXRlbXNbaV0udHlwZSA9PSAyKSB7XG4gICAgICAgICAgICAgICAgaWYgKHNlbGYud2luZG93V2lkdGggPiAxMDAwKSB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGxIdG1sICs9IGl0O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgaHRtbCArPSBpdDtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgc2VsZi5jb3VudFNlbGxJdGVtcysrO1xuICAgICAgICAgICAgICAgIHNlbGYuZmluYWxQcmljZSA9IHNlbGYuc3VtKHNlbGYuZmluYWxQcmljZSwgcGFyc2VGbG9hdChpdGVtc1tpXS5wcmljZV9yYXcpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICBodG1sID0gaHRtbCArIGh0bWxEaXM7XG4gICAgICAgICQoc2VsZi5maW5hbFByaWNlSW5wdXQpLnZhbChzZWxmLmZpbmFsUHJpY2UpO1xuICAgICAgICAkKHNlbGYuZmluYWxQcmljZUJsKS5odG1sKHBhcnNlRmxvYXQoc2VsZi5maW5hbFByaWNlKS50b0ZpeGVkKDIpICsgJyAnICsgY3VycmVuY3kpO1xuICAgICAgICAkKCcjY291bnRTZWxsSXRlbXMnKS5odG1sKHNlbGYuY291bnRTZWxsSXRlbXMpO1xuICAgICAgICAkKGNvdW50U2VsbEl0ZW1zTW9iKS5odG1sKHNlbGYuY291bnRTZWxsSXRlbXMpO1xuICAgICAgICB0aGlzLml0ZW1zV3JhcC5pbm5lckhUTUwgPSBodG1sO1xuICAgICAgICB0aGlzLml0ZW1zV3JhcFNlbGwuaW5uZXJIVE1MID0gc2VsbEh0bWw7XG4gICAgICAgIHRoaXMuY2xpY2thYmxlSXRlbXMgPSAkKCcuc2tpbnNJdGVtLnNraW5zSXRlbS0tYWNjZXB0YWJsZScpO1xuICAgICAgICBpZiAoc2VsZi5jb3VudFNlbGxJdGVtcyA+IDApIHtcbiAgICAgICAgICAgIHNlbGYuc2tpbnNTdWJtaXQuY2xhc3NMaXN0LnJlbW92ZSgnZGlzYWJsZWQnKTtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgIHNlbGYuc2tpbnNTdWJtaXQuY2xhc3NMaXN0LmFkZCgnZGlzYWJsZWQnKTtcbiAgICAgICAgfVxuICAgICAgICAkKHRoaXMuY2xpY2thYmxlSXRlbXMpLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHNlbGYuY2xpY2tJdGVtcyh0aGlzKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJCgnI3NraW5zQ291bnRTdW0nKS5odG1sKHBhcnNlRmxvYXQoc2VsZi5pbnZBbGxQcmljZSkudG9GaXhlZCgyKSArICcgJyArIGN1cnJlbmN5KTtcbiAgICAgICAgJCgnI3NraW5zQ291bnRVc2VyJykuaHRtbChzZWxmLnNraW5zQ291bnRVc2VyKTtcbiAgICAgICAgJCgnI2NvdW50U2VsbEl0ZW1zTW9iJykuaHRtbChzZWxmLmNvdW50U2VsbEl0ZW1zKTtcbiAgICAgICAgJCgnI2NvdW50U2VsbEl0ZW1zJykuaHRtbChzZWxmLmNvdW50U2VsbEl0ZW1zKTtcblxuICAgICAgICBpZiAoc2VsZi53aW5kb3dXaWR0aCA+IDEwMDApIHtcbiAgICAgICAgICAgICQoJy5za2luc19faXRlbXMnKS5jdXN0b21TY3JvbGwoKTtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICQoJy5za2luc19faXRlbXMnKS5jdXN0b21TY3JvbGwoJ2Rlc3Ryb3knKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuaXRlbXNXcmFwU2hvd0ljb25zKCk7XG4gICAgICAgIHRoaXMuaXRlbXNXcmFwLmFkZEV2ZW50TGlzdGVuZXIoXCJzY3JvbGxcIiwgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICB0aGlzLml0ZW1zV3JhcFNob3dJY29ucygpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgfSxcbiAgICBnZXRUaW1lUmVtYWluaW5nOiBmdW5jdGlvbiAoZW5kdGltZSkge1xuICAgICAgICB2YXIgdCA9IERhdGUucGFyc2UoZW5kdGltZSkgLSBEYXRlLnBhcnNlKG5ldyBEYXRlKCkpO1xuICAgICAgICB2YXIgc2Vjb25kcyA9IE1hdGguZmxvb3IoKHQgLyAxMDAwKSAlIDYwKTtcbiAgICAgICAgdmFyIG1pbnV0ZXMgPSBNYXRoLmZsb29yKCh0IC8gMTAwMCAvIDYwKSAlIDYwKTtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICd0b3RhbCc6IHQsXG4gICAgICAgICAgICAnbWludXRlcyc6IG1pbnV0ZXMsXG4gICAgICAgICAgICAnc2Vjb25kcyc6IHNlY29uZHNcbiAgICAgICAgfTtcbiAgICB9LFxuICAgIHVwZGF0ZUNsb2NrOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgICAgdmFyIHQgPSB0aGlzLmdldFRpbWVSZW1haW5pbmcodGhpcy5kZWFkbGluZSk7XG4gICAgICAgIHRpbWVyTWluLmlubmVySFRNTCA9IHQubWludXRlcztcbiAgICAgICAgdGltZXJTZWMuaW5uZXJIVE1MID0gdC5zZWNvbmRzO1xuICAgICAgICBpZiAodC50b3RhbCA8PSAwKSB7XG4gICAgICAgICAgICBzZWxmLndvcmtlcigpO1xuICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChzZWxmLnRpbWVpbnRlcnZhbCk7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIGluaXRUaW1lcjogZnVuY3Rpb24gKCkge1xuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgICAgIGlmICgkLmNvb2tpZSgnZGF0ZScgKyBkZXBvc2l0SWQpID09IHVuZGVmaW5lZCkgeyAvLyDQutGD0LrQuCDQvdC10YJcbiAgICAgICAgICAgIHRoaXMuZGVhZGxpbmUgPSBuZXcgRGF0ZSgpO1xuICAgICAgICAgICAgdGhpcy5kZWFkbGluZS5zZXRNaW51dGVzKHRoaXMuZGVhZGxpbmUuZ2V0TWludXRlcygpICsgMyk7XG4gICAgICAgICAgICAkLmNvb2tpZSgnZGF0ZScgKyBkZXBvc2l0SWQsIHRoaXMuZGVhZGxpbmUsIHtleHBpcmVzOiB0aGlzLmRlYWRsaW5lLCBwYXRoOiAnLyd9KTtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgIHRoaXMuZGVhZGxpbmUgPSAkLmNvb2tpZSgnZGF0ZScgKyBkZXBvc2l0SWQpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMudXBkYXRlQ2xvY2soKTtcbiAgICAgICAgdGhpcy50aW1laW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBzZWxmLnVwZGF0ZUNsb2NrKCk7XG4gICAgICAgIH0sIDEwMDApO1xuICAgIH0sXG4gICAgY2hhbmdlTGFuZzogZnVuY3Rpb24gKGxhbmcpIHtcbiAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBsb2NhdGlvbi5vcmlnaW4gKyAnLycgKyBsYW5nICsgJy8nICsgbG9jYXRpb24uc2VhcmNoO1xuICAgIH0sXG4gICAgY2hlY2tTdGVhbVN0YXR1czogZnVuY3Rpb24gKCkge1xuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBSb3V0aW5nLmdlbmVyYXRlKCdpc19zdGVhbV9yaXAnLCB7J19sb2NhbGUnOiBsb2NhbGV9KSxcbiAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uIChyZXMpIHtcbiAgICAgICAgICAgICAgICBpZiAodHlwZW9mIHJlcy5yZXNwb25zZUpTT04uc3RhdHVzID09ICdzdHJpbmcnKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiByZXMucmVzcG9uc2VKU09OLm1lc3NhZ2UgPT0gJ3N0cmluZycpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIuaGVhZF9fc3RlYW1fX2NpcmNsZVwiKS5hZGRDbGFzcygnY2lyY2xlX18nICsgcmVzLnJlc3BvbnNlSlNPTi5zdGF0dXMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3N0ZWFtU3RhdHVzJykuYWRkQ2xhc3MocmVzLnJlc3BvbnNlSlNPTi5zdGF0dXMpLnRleHQocmVzLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSxcbiAgICB3b3JrZXI6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIHVybDogUm91dGluZy5nZW5lcmF0ZSgnY2hlY2tfZGVwb3NpdCcsIHsnX2xvY2FsZSc6IGxvY2FsZSwgJ2RlcG9zaXRJZCc6IGRlcG9zaXRJZH0pLFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKGRhdGEpIHtcbiAgICAgICAgICAgICAgICBpZiAoZGF0YS5hY3Rpb24gPT0gJ3Nob3dfc3VjY2VzcycpIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnI2pzLWRlcG9zaXQtYW1vdW50JykudGV4dChkYXRhLmFtb3VudCk7XG4gICAgICAgICAgICAgICAgICAgICQoJy50U2VudCcpLmFkZENsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICQoJy5za2luc0ZpbmFsU3VjY2VzcycpLnJlbW92ZUNsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYgKGRhdGEuYWN0aW9uID09ICdzaG93X2ZhaWwnKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJy50U2VudCcpLmFkZENsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICQoJy5za2luc0ZpbmFsRmFpbCcpLnJlbW92ZUNsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICQoJyNza2luc0ZpbmFsRmFpbC1tZXNzYWdlJykuaHRtbChkYXRhLm1lc3NhZ2UpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYgKGRhdGEuYWN0aW9uID09ICdzaG93X3BlbmRpbmcnKSB7XG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoc2tpbnMud29ya2VyLCAxMDAwMCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9LFxuICAgIGxvYWRJbnZlbnRvcnk6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBSb3V0aW5nLmdlbmVyYXRlKCdpbnZlbnRvcnknLCB7J19sb2NhbGUnOiBsb2NhbGV9KSxcbiAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlcykge1xuICAgICAgICAgICAgICAgIGlmIChyZXMuc3RhdHVzID09ICdzdWNjZXNzJykge1xuICAgICAgICAgICAgICAgICAgICBza2lucy5yZXNwb25zZUl0ZW1zID0gcmVzLmludmVudG9yeS5pdGVtcztcbiAgICAgICAgICAgICAgICAgICAgc2tpbnMucmVzcG9uc2VJdGVtc1ZhbHVlID0gcmVzLmludmVudG9yeS5pdGVtc1ZhbHVlO1xuICAgICAgICAgICAgICAgICAgICBzZWxmLmRlcG9zaXRJbmZvID0gcmVzLmludmVudG9yeS5kZXBvc2l0O1xuICAgICAgICAgICAgICAgICAgICAkKCcjY29uZmlybVRyYWRlTGluaycpLmF0dHIoJ2hyZWYnLCByZXMuY29uZmlybV90cmFkZV9vZmZlcl91cmwpO1xuICAgICAgICAgICAgICAgICAgICBza2lucy5pbml0KCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkKCcjaW52dExvYWRFcnInKS5odG1sKCc8cD4nICsgcmVzLm1lc3NhZ2UgKyAnPC9wPicpLnJlbW92ZUNsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICQoJyNza2luc0JsUHJlJykuYWRkQ2xhc3MoJ2Vycm9yJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9LFxuICAgIHByZUluaXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdGhpcy5kZXBvc2l0SW5mby5zdGF0dXMgPSB0eXBlb2YgZGVwb3NpdFN0YXR1cyAhPT0gJ3VuZGVmaW5lZCcgPyBkZXBvc2l0U3RhdHVzIDogbnVsbDtcbiAgICAgICAgdGhpcy5jaGVja1N0ZWFtU3RhdHVzKCk7XG5cbiAgICAgICAgaWYgKHRoaXMuZGVwb3NpdEluZm8uc3RhdHVzID09ICduZXcnIHx8IHRoaXMuZGVwb3NpdEluZm8uc3RhdHVzID09ICdlcnJvcl9pbnZlbnRvcnknKSB7XG4gICAgICAgICAgICB0aGlzLmxvYWRJbnZlbnRvcnkoKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAodGhpcy5kZXBvc2l0SW5mby5zdGF0dXMgPT0gJ3BlbmRpbmcnIHx8IHRoaXMuZGVwb3NpdEluZm8uc3RhdHVzID09ICdlcnJvcl9ib3QnIHx8IHRoaXMuZGVwb3NpdEluZm8uc3RhdHVzID09ICdlcnJvcl91bmFjY2VwdGFibGVfaXRlbScpIHtcbiAgICAgICAgICAgIHRoaXMuaW5pdCgpO1xuICAgICAgICB9XG4gICAgfSxcbiAgICBpbml0OiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcblxuICAgICAgICAkKCcjc2tpbnNCbFByZScpLmhpZGUoKTtcblxuICAgICAgICBpZiAoc2VsZi5kZXBvc2l0SW5mby5zdGF0dXMgPT0gJ25ldycgfHwgc2VsZi5kZXBvc2l0SW5mby5zdGF0dXMgPT0gJ2Vycm9yX2ludmVudG9yeScpIHtcbiAgICAgICAgICAgICQoJyNza2luc0JsJykucmVtb3ZlQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICAgIHNlbGYuc2tpbnNTdWJtaXQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2tpbnNTdWJtaXQnKTtcblxuICAgICAgICAgICAgJCgnLnNraW5zX19pdGVtcycpLmN1c3RvbVNjcm9sbCgpO1xuXG4gICAgICAgICAgICB0aGlzLml0ZW1zV3JhcCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdza2luc0l0ZW1zV3JhcCcpO1xuICAgICAgICAgICAgdGhpcy5pdGVtc1dyYXBTZWxsID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NraW5zSXRlbXNXcmFwU2VsbCcpO1xuXG4gICAgICAgICAgICB2YXIgaW5pdEl0ZW1zID0gJCgnLnNraW5zSXRlbScpO1xuICAgICAgICAgICAgdGhpcy5pbml0T2JqID0ge307XG5cbiAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgc2VsZi5yZXNwb25zZUl0ZW1zLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgLy9zZWxmLmluaXRPYmpbc2VsZi5yZXNwb25zZUl0ZW1zW2ldLmlkXSA9IHtcbiAgICAgICAgICAgICAgICBzZWxmLmluaXRPYmpbaV0gPSB7XG4gICAgICAgICAgICAgICAgICAgIGlkOiBzZWxmLnJlc3BvbnNlSXRlbXNbaV0uaWQsXG4gICAgICAgICAgICAgICAgICAgIGljb25fdXJsOiBzZWxmLnJlc3BvbnNlSXRlbXNbaV0uaWNvbl91cmwsXG4gICAgICAgICAgICAgICAgICAgIG1hcmtldF9uYW1lOiBzZWxmLnJlc3BvbnNlSXRlbXNbaV0ubWFya2V0X25hbWUsXG4gICAgICAgICAgICAgICAgICAgIG1hcmtldF9oYXNoX25hbWU6IHNlbGYucmVzcG9uc2VJdGVtc1tpXS5tYXJrZXRfaGFzaF9uYW1lLFxuICAgICAgICAgICAgICAgICAgICBwcmljZTogc2VsZi5yZXNwb25zZUl0ZW1zW2ldLnByaWNlLFxuICAgICAgICAgICAgICAgICAgICBwcmljZV9yYXc6IHNlbGYucmVzcG9uc2VJdGVtc1tpXS5wcmljZV9yYXcsXG4gICAgICAgICAgICAgICAgICAgIGNvbG9yOiBzZWxmLnJlc3BvbnNlSXRlbXNbaV0uY29sb3IsXG4gICAgICAgICAgICAgICAgICAgIHJhcml0eTogc2VsZi5yZXNwb25zZUl0ZW1zW2ldLnJhcml0eSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogc2VsZi5yZXNwb25zZUl0ZW1zW2ldLnR5cGUsXG4gICAgICAgICAgICAgICAgICAgIGFwcF9pZDogc2VsZi5yZXNwb25zZUl0ZW1zW2ldLmFwcF9pZCxcbiAgICAgICAgICAgICAgICAgICAgYWNjZXB0YWJsZTogc2VsZi5yZXNwb25zZUl0ZW1zW2ldLmFjY2VwdGFibGVcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIGlmIChzZWxmLnJlc3BvbnNlSXRlbXNbaV0uYWNjZXB0YWJsZSkge1xuICAgICAgICAgICAgICAgICAgICBzZWxmLnNraW5zQ291bnRVc2VyKys7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHNlbGYuaW52QWxsUHJpY2UgPSBzZWxmLnJlc3BvbnNlSXRlbXNWYWx1ZTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWxmLnJlbmRlckl0ZW1zKCk7XG5cbiAgICAgICAgICAgIHZhciB0aW1lT3V0SWQ7XG5cbiAgICAgICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdyZXNpemUnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgY2xlYXJJbnRlcnZhbCh0aW1lT3V0SWQpO1xuICAgICAgICAgICAgICAgIHRpbWVPdXRJZCA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBzZWxmLndpbmRvd1dpZHRoID0gd2luZG93LmlubmVyV2lkdGg7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYucmVuZGVySXRlbXMoKTtcbiAgICAgICAgICAgICAgICB9LCA1MDApO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdza2luc1NlYXJjaCcpLm9uaW5wdXQgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMudmFsdWUubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAkKCdsYWJlbFtmb3I9c2tpbnNTZWFyY2hdJykuaGlkZSgpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkKCdsYWJlbFtmb3I9c2tpbnNTZWFyY2hdJykuc2hvdygpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHNlbGYuZmlsdGVySXRlbXModGhpcy52YWx1ZSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZmlsdGVyQnlBcHBJZCcpLmFkZEV2ZW50TGlzdGVuZXIoJ2NoYW5nZScsIGZ1bmN0aW9uKGV2ZW50KXtcbiAgICAgICAgICAgICAgICBzZWxmLmZpbHRlckFwcElkID0gZXZlbnQudGFyZ2V0LnZhbHVlO1xuICAgICAgICAgICAgICAgIHNlbGYuZmlsdGVySXRlbXMoc2VsZi5maWx0ZXJUZXh0KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cbiAgICAgICAgZWxzZSBpZiAoc2VsZi5kZXBvc2l0SW5mby5zdGF0dXMgPT0gJ3BlbmRpbmcnKSB7XG4gICAgICAgICAgICBzZWxmLmluaXRUaW1lcigpO1xuICAgICAgICAgICAgc2VsZi53b3JrZXIoKTtcbiAgICAgICAgICAgICQoJyNza2luc0JsTGFzdCwgI3RTZW50JykucmVtb3ZlQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIGlmIChzZWxmLmRlcG9zaXRJbmZvLnN0YXR1cyA9PSAnY29tcGxldGVkJykge1xuICAgICAgICAgICAgJCgnI3NraW5zQmxMYXN0LCAjc2tpbnNGaW5hbFN1Y2Nlc3MnKS5yZW1vdmVDbGFzcygnaGlkZScpO1xuICAgICAgICB9XG4gICAgICAgIGVsc2UgaWYgKHNlbGYuZGVwb3NpdEluZm8uc3RhdHVzID09ICdlcnJvcl9ib3QnIHx8IHNlbGYuZGVwb3NpdEluZm8uc3RhdHVzID09ICdlcnJvcl91bmFjY2VwdGFibGVfaXRlbScpIHtcbiAgICAgICAgICAgICQoJyNza2luc0JsTGFzdCwgI3NraW5zRmluYWxGYWlsJykucmVtb3ZlQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgfVxuICAgIH1cbn07XG5cbiQoZG9jdW1lbnQpLnJlYWR5KHNraW5zLnByZUluaXQoKSk7XG5cbnZhciBtb2RhbG4gPSB7XG4gICAgb3BlbjogZnVuY3Rpb24gKGlkLCB0aGF0KSB7XG5cbiAgICAgICAgaWYgKCQoJyNsYXN0X3BvcHVwJykubGVuZ3RoICYmIGlkLmluZGV4T2YoJ21vZGFsLXRhc2stcG9wJykgPCAwICYmIGlkLmluZGV4T2YoJ21vZGFsLXJlc3VsdC10YXNrJykgPCAwICYmIGlkLmluZGV4T2YoJ21vZGFsLXNlbWVuaWgnKSA8IDApIGlkID0gJyNtb2RhbC1zZW1lbmloMic7XG5cbiAgICAgICAgJCgnYm9keScpLmNzcygndG9wJywgLVJlYWxTY3JvbGwpO1xuICAgICAgICBpZiAoaWQgPT0gJyNtb2RhbC1yZXN1bHQtdGFzaycpIHtcbiAgICAgICAgICAgIHZhciBwZW9wbGVfaXRlbSA9ICQodGhhdCkucGFyZW50cygnW2RhdGEtYXZhdGFyXScpO1xuICAgICAgICAgICAgJCgnI21vZGFsLXJlc3VsdC10YXNrIC5uYW1lIHNwYW4nKS50ZXh0KHBlb3BsZV9pdGVtLmF0dHIoJ2RhdGEtbmFtZScpKTtcbiAgICAgICAgICAgICQoJyNtb2RhbC1yZXN1bHQtdGFzayAubW9kYWwtcmVzdWx0LXRhc2stbmFtZScpLmF0dHIoJ2hyZWYnLCBwZW9wbGVfaXRlbS5hdHRyKCdkYXRhLWxpbmsnKSk7XG5cbiAgICAgICAgICAgICR2b3RlX2J0biA9ICQoJyNtb2RhbC1yZXN1bHQtdGFzayAucmVzdWx0LXRhc2stdm90ZS1idG4nKTtcbiAgICAgICAgICAgICR2b3RlX2J0bi5hdHRyKCdkYXRhLWlkJywgcGVvcGxlX2l0ZW0uYXR0cignZGF0YS1pZCcpKTtcbiAgICAgICAgICAgIHZhciB1c2VyX3ZvdGVkID0gcGVvcGxlX2l0ZW0uYXR0cignZGF0YS11c2VyLXZvdGVkJyk7XG4gICAgICAgICAgICBpZiAodXNlcl92b3RlZCA9PSAnMScpIHtcbiAgICAgICAgICAgICAgICAkdm90ZV9idG4uZmluZCgnc3BhbicpLmh0bWwoJ9CS0Ysg0YPQttC1INCz0L7Qu9C+0YHQvtCy0LDQu9C4Jyk7XG4gICAgICAgICAgICAgICAgJHZvdGVfYnRuLmFkZENsYXNzKCdjYW50X3ZvdGUnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJHZvdGVfYnRuLmZpbmQoJ3NwYW4nKS5odG1sKCfQn9GA0L7Qs9C+0LvQvtGB0YPQuScpO1xuICAgICAgICAgICAgICAgICR2b3RlX2J0bi5yZW1vdmVDbGFzcygnY2FudF92b3RlJyk7XG4gICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgJCgnI21vZGFsLXJlc3VsdC10YXNrIC5kZXNjcmlwdGlvbicpLnRleHQocGVvcGxlX2l0ZW0uYXR0cignZGF0YS10ZXh0JykpO1xuICAgICAgICAgICAgJCgnI21vZGFsLXJlc3VsdC10YXNrIC5pbWFnZSBpbWcnKS5hdHRyKCdzcmMnLCBwZW9wbGVfaXRlbS5hdHRyKCdkYXRhLXBob3RvJykpO1xuICAgICAgICAgICAgJCgnI21vZGFsLXJlc3VsdC10YXNrIC5saWtlcycpLmh0bWwoJzxpPjwvaT4gJyArIHBlb3BsZV9pdGVtLmF0dHIoJ2RhdGEtbGlrZXMnKSk7XG4gICAgICAgICAgICAkKCcjbW9kYWwtcmVzdWx0LXRhc2sgLm1vZGFsLXJlc3VsdC10YXNrLW5hbWUgLmltZycpLmNzcygnYmFja2dyb3VuZC1pbWFnZScsICd1cmwoJyArIHBlb3BsZV9pdGVtLmF0dHIoJ2RhdGEtYXZhdGFyJykgKyAnKScpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGlkID09ICcjbW9kYWwtZGVsZXRlJykge1xuICAgICAgICAgICAgJCgnI21vZGFsLWRlbGV0ZSBhJykuYXR0cignaHJlZicsICcvbGs/ZGVsZXRlX3Bvc3Q9JyArICQodGhhdCkuYXR0cignZGF0YS1pZC1kZWxldGUnKSk7XG4gICAgICAgIH1cblxuICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ21vZGFsbk9wZW5lZCcpO1xuICAgICAgICAkKCdodG1sJykuYWRkQ2xhc3MoJ2ZpeGVkJyk7XG4gICAgICAgICQoaWQpLnNob3coKTtcbiAgICB9LFxuICAgIGNsb3NlOiBmdW5jdGlvbiAoZSwgdGhhdCkge1xuICAgICAgICBib2R5RWxlbS5jc3MoJ3RvcCcsIDApO1xuICAgICAgICBib2R5RWxlbS5zY3JvbGxUb3AoUmVhbFNjcm9sbCk7XG4gICAgICAgIGlmICghJChlLnRhcmdldCkuY2xvc2VzdCgnLm1vZGFsbi1jb250ZW50JykubGVuZ3RoKSB7XG4gICAgICAgICAgICAkKCdib2R5JykucmVtb3ZlQ2xhc3MoJ21vZGFsbk9wZW5lZCcpO1xuICAgICAgICAgICAgJCgnaHRtbCcpLnJlbW92ZUNsYXNzKCdmaXhlZCcpO1xuICAgICAgICAgICAgaWYgKCQodGhhdCkuaGFzQ2xhc3MoJ21vZGFsbicpKSAkKHRoYXQpLmhpZGUoKTtcbiAgICAgICAgICAgIGVsc2UgJCh0aGF0KS5wYXJlbnRzKCcubW9kYWxuJykuaGlkZSgpO1xuICAgICAgICB9XG4gICAgfVxufTtcblxudmFyIFJlYWxTY3JvbGwgPSAwO1xudmFyIGJvZHlFbGVtID0gJCgnYm9keScpO1xuJCh3aW5kb3cpLnNjcm9sbChmdW5jdGlvbiAoKSB7XG4gICAgQ3VycmVudFNjcm9sbCA9ICQod2luZG93KS5zY3JvbGxUb3AoKTtcbiAgICBpZiAoQ3VycmVudFNjcm9sbCAhPT0gMCkge1xuICAgICAgICBSZWFsU2Nyb2xsID0gQ3VycmVudFNjcm9sbDtcbiAgICB9XG59KTtcblxuJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5tb2RhbG4gLmNsb3NlLCAuYnRuLmJhY2ssIC5tb2RhbG4gLmJ0bi1jbG9zZScsIGZ1bmN0aW9uICgpIHtcbiAgICAkKHRoaXMpLnBhcmVudHMoJy5tb2RhbG4nKS5oaWRlKCk7XG4gICAgYm9keUVsZW0ucmVtb3ZlQ2xhc3MoJ21vZGFsbk9wZW5lZCcpO1xuICAgIGJvZHlFbGVtLmNzcygndG9wJywgMCk7XG4gICAgYm9keUVsZW0uc2Nyb2xsVG9wKFJlYWxTY3JvbGwpO1xufSk7XG4kKGRvY3VtZW50KS5vbignY2xpY2snLCAnW2RhdGEtbW9kYWxdJywgZnVuY3Rpb24gKCkge1xuICAgIG1vZGFsbi5vcGVuKCcjJyArICQodGhpcykuYXR0cignZGF0YS1tb2RhbC1uYW1lJyksIHRoaXMpO1xufSk7XG4kKGRvY3VtZW50KS5vbignY2xpY2snLCAnLm1vZGFsJywgZnVuY3Rpb24gKGUpIHtcbiAgICBpZiAoISQoZS50YXJnZXQpLmNsb3Nlc3QoJy5tb2RhbC1jb250ZW50JykubGVuZ3RoKSB7XG4gICAgICAgICQoJy5tb2RhbCcpLmhpZGUoKTtcbiAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdtb2RhbG5PcGVuZWQnKTtcbiAgICAgICAgJCgnaHRtbCcpLnJlbW92ZUNsYXNzKCdmaXhlZCcpO1xuICAgIH1cbn0pO1xuJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5jbG9zZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgJCgnLm1vZGFsJykuaGlkZSgpO1xuICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygnbW9kYWxuT3BlbmVkJyk7XG4gICAgJCgnaHRtbCcpLnJlbW92ZUNsYXNzKCdmaXhlZCcpO1xufSk7Il0sImZpbGUiOiJza2lucy5qcyJ9
