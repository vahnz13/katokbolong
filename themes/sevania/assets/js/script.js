var app = (function() {
    app.prototype.init = function() {
        this.suggest();
    };
    app.prototype.non_uni = function(str) {
        return str.replace(/Ã |Ã¡|áº¡|áº£|Ã£|Ã¢|áº§|áº¥|áº­|áº©|áº«|Äƒ|áº±|áº¯|áº·|áº³|áºµ/gi, 'a').replace(/Ã¨|Ã©|áº¹|áº»|áº½|Ãª|á»|áº¿|á»‡|á»ƒ|á»…/gi, 'e').replace(/Ã¬|Ã­|á»‹|á»‰|Ä©/gi, 'i').replace(/Ã²|Ã³|á»|á»|Ãµ|Ã´|á»“|á»‘|á»™|á»•|á»—|Æ¡|á»|á»›|á»£|á»Ÿ|á»¡/gi, 'o').replace(/Ã¹|Ãº|á»¥|á»§|Å©|Æ°|á»«|á»©|á»±|á»­|á»¯/gi, 'u').replace(/á»³|Ã½|á»µ|á»·|á»¹/gi, 'y').replace(/Ä‘/gi, 'd').replace(/[^\w\s]/g, ' ').trim().replace(/\s+/g, '-').toLowerCase();
    };
    app.prototype.suggest = function() {
        var self = this;
        var suggest = $('#suggest');
        $('body, html').click(function() {
            suggest.find('#suggest-result').remove();
        });
        suggest.keyup(function(e) {
            if (e.keyCode == 40) {
                $('li').filter('.active').next().focus();
                e.preventDefault();
                return false;
            } else if (e.keyCode == 38) {
                $('li').filter('.active').prev().focus();
                e.preventDefault();
                return false;
            }
            if (e.keyCode == 13) {
                $('.form form').submit();
                return false;
            }
        }).on('focus', 'li', function(e) {
            $(this).addClass('active').siblings().removeClass();
            $('input[name="q"]').val($(this).text());
        });
        $('input#search-query').keyup(function(e) {
            e.preventDefault();
            if (e.keyCode == 40 || e.keyCode == 38) {
                $('li[tabindex=0]').focus();
                return false;
            }
            $.ajax({
                url: 'https://suggestqueries.google.com/complete/search',
                jsonp: 'jsonp',
                dataType: 'jsonp',
                data: {
                    q: this.value,
                    hl: 'id',
                    ds: 'yt',
                    client: 'youtube-reduced'
                }
            }).done(function(data) {
                var ar = data[0].split(' '),
                    match = [];
                var out = data[1].map(function(array, index) {
                    match[index] = [];
                    ar.map(function(word, i) {
                        regex = new RegExp(word, 'g');
                        m = regex.exec(array[0]);
                        if (m != null) {
                            match[index].push(word);
                        }
                    });
                    match[index] = match[index].reduce(function(a, b) {
                        if (a.indexOf(b) < 0) a.push(b);
                        return a;
                    }, []);
                    return array[0].replace(new RegExp('(' + match[index].join('|') + ')', 'g'), '<b>$1</b>');
                });
                dom = '<ul id="suggest-result">';
                for (i = 0; i < out.length; i++) {
                    dom +=
                        '<li tabindex="' +
                        i +
                        '"><a href="javascript:;">' +
                        out[i] +
                        '</a></li>' +
                        '\r\n';
                }
                dom += '</ul>';
                suggest.find('#suggest-result').remove();
                suggest.append(dom);
            });
        });
    };

    function app() {}
    return new app();
})();