var webserver = require('webserver');
var server = webserver.create();
var page = require('webpage').create(),
    url = 'https://steamstat.us';

var service = server.listen(2121, function(request, response) {

    console.log('Request received at ' + new Date());
    page.open(url, function (status) {

        if (status == 'success')
        {
            var elements = page.evaluate(function () {
                return document.getElementById('csgo_community');
            });

            response.statusCode = 200;
            response.headers = {
                'Cache': 'no-cache',
                'Content-Type': 'text/json;charset=utf-8'
            };

            setTimeout(function(){
                var output = {'status' : 'success', 'datetime': new Date(), 'message': elements.innerText};
                response.write(JSON.stringify(output));
                response.close();
            }, 2000)
        }
    });
});