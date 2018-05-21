
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