/**
 * @api {get} https://skins4real.com/:lang/?public_key=:public_key&order_id=:order_id&trade_url=:trade_url&sign=:sign Начало пополнения
 * @apiName start transaction
 * @apiGroup Skins4real
 *
 * @apiParam {String="en", "ru"} lang Язык пользователя
 * @apiParam {String="RUB", "USD"} currency Валюта пополнения
 * @apiParam {String} public_key Ключ интеграции
 * @apiParam {Number} order_id Идентификатор транзакции в вашей системе
 * @apiParam {String} trade_url Трейд-ссылка пользователя https://steamcommunity.com/tradeoffer/new/?partner=123456789&token=LllLlL
 * @apiParam {String} sign Подпись запроса, формируется из соединения параметров(без sign, lang)  из query в строку - param:param_value; и последующим шифрованием hash_hmac('sha1', $sign, 'private_key')
 *
 * @apiDescription
 * Перенаправьте пользователя по этому адресу со всеми нужными параметрами.
 * Пример формирования подписи запроса
 * <p><pre>
 * ksort($query):<br/>
 * $sign = '';<br/>
 * foreach ($query as $key => $value) {<br/>
 *     $sign .= $key . ':' . $value . ';';<br/>
 * }<br/>
 * return hash_hmac('sha1', $sign, 'private_key');
 * </pre></p>
 */


/**
 * @api {post} yourcases.com/skins4real/push Pushback interface
 * @apiName Pushback
 * @apiGroup Interface
 *
 * @apiParam {String="RUB", "USD"} currency Валюта платежа
 * @apiParam {Number} amount Сумма пополнения
 * @apiParam {Number} transaction_id Идентификатор транзакции в skins4real
 * @apiParam {Number} order_id Идентификатор транзакции в вашей система
 * @apiParam {String} sign Подпись запроса, формируется из соединения параметров(без sign, lang) из query в строку - param:param_value; и последующим шифрованием hash_hmac('sha1', $sign, 'private_key')
 *
 * @apiResponseExample {json} Success-Response:
 *                            {"status":1}
 *
 * @apiDescription
 * <p>Ваша система должна принять этот запрос, пометить транзакцию как завершенную и ответить json {"status":1} если все нормально, или о дним из статусов из списка ниже</p>
 * <ul>
 * <li>Коды статусов которые понимает skins4real:</li>
 * <li>Успешно принят - 1;</li>
 * <li>Повторный успешнопринятый - 2;</li>
 * <li>Неправильная подпись запроса - 3;</li>
 * <li>Неизвестная ошибка - 4;</li>
 * <li>Неверные данные - 5;</li>
 * <li>Заблокированная запись в базе - 6;</li>
 * <li>Неизвестный идентификатор order_id - 7;</li>
 * </ul>
 * Если ваша система ответила статусами lock(6) или unknown error(4), пушбэк будет переотправляться каждые 30 секунд, 10 раз пока статус пушбэка не станет одним из терминальных
 */

/**
 * @api {get} yourcases.com/skins4real/success?order_id=:order_id Success page
 * @apiName Success page
 * @apiGroup Interface
 * @apiParam {Number} order_id Идентификатор транзакции в вашей система
 * @apiDescription
 * По этому адресу пользователь будет перенаправлен после завершения совершения обмена
 */
