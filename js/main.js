var REGEXP_NUMERIC = /^\d+$/,
    REGEXP_CENA = /^[\d]+(.[\d]{1,2})?$/,
    SITE = 'http://' + DOMAIN,
    URL = SITE + '/index.php?' + VALUES,
    AJAX_MAIN = SITE + '/ajax/main.php?' + VALUES,
    IMAGE_UPLOAD_PATH = SITE + '/include/imageUpload.php',
    setCookie = function(name, value) {
        var exdate = new Date();
        exdate.setDate(exdate.getDate() + 1);
        document.cookie = name + '=' + value + '; path=/; expires=' + exdate.toGMTString();
    },
    getCookie = function(name) {
        var arr1 = document.cookie.split(name);
        if(arr1.length > 1) {
            var arr2 = arr1[1].split(/;/);
            var arr3 = arr2[0].split(/=/);
            return arr3[0] ? arr3[0] : arr3[1];
        } else
            return null;
    },
    delCookie = function(name) {
        var exdate = new Date();
        exdate.setDate(exdate.getDate()-1);
        document.cookie = name + '=; path=/; expires=' + exdate.toGMTString();
    },
    sortable = function() {
        $('._sort').sortable({
            axis:'y',
            update:function () {
                var dds = $(this).find('dd'),
                    arr = [];
                for(var n = 0; n < dds.length; n++)
                    arr.push(dds.eq(n).attr('val'));
                var send = {
                    op:'sort',
                    table:$(this).attr('val'),
                    ids:arr.join()
                };
                $('.path').addClass('busy');
                $.post(AJAX_MAIN, send, function(res) {
                    $('.path').removeClass('busy');
                }, 'json');
            }
        });
    },
    _end = function(count, arr) {
        /* ������������ ���������
         * ������: 1 ����
         * G.end(count, ['���', '��', '��']);
         */
        if(arr.length == 2)
            arr.push(arr[1]); // ���� � ������� ����� 2 ��������, �� ���������� ���, ������� ������ ������� � ������
        var send = arr[2];
        if(Math.floor(count / 10 % 10) != 1)
            switch(count % 10) {
                case 1: send = arr[0]; break;
                case 2: send = arr[1]; break;
                case 3: send = arr[1]; break;
                case 4: send = arr[1]; break;
            }
        return send;
    },
    hashLoc,
    hashSet = function(hash) {
        if(!hash && !hash.p)
            return;
        hashLoc = hash.p;
        var s = true;
        switch(hash.p) {
            case 'client':
                if(hash.d == 'info')
                    hashLoc += '_' + hash.id;
                break;
            case 'zayav':
                if(hash.d == 'info')
                    hashLoc += '_' + hash.id;
                else if(hash.d == 'add')
                    hashLoc += '_add' + (REGEXP_NUMERIC.test(hash.id) ? '_' + hash.id : '');
                else if(!hash.d)
                    s = false;
                break;
            case 'zp':
                if(hash.d == 'info')
                    hashLoc += '_' + hash.id;
                else
                    s = false;
                break;
            default:
                if(hash.d) {
                    hashLoc += '_' + hash.d;
                    if(hash.d1)
                        hashLoc += '_' + hash.d1;
                }
        }
        if(s)
            VK.callMethod('setLocation', hashLoc);
    };

$(document)
    .ajaxError(function(event, request, settings) {
        if(!request.responseText)
            return;
        alert('������:\n\n' + request.responseText);
        //var txt = request.responseText;
        //throw new Error('<br />AJAX:<br /><br />' + txt + '<br />');
    })
    .on('click', '#cache_clear', function() {
        $.post(AJAX_MAIN, {'op':'cache_clear'}, function(res) {
            if(res.success) {
                _msg('��� ������.');
                document.location.reload();
            }
        }, 'json');
    })
    .on('click', '.debug_toggle', function() {
        var d = getCookie('debug');
        setCookie('debug', d == 1 ? 0 : 1);
        _msg('Debug �������.');
        document.location.reload();
    })
    .on('click', '.sa_viewer_msg .leave', function() {
        delCookie('sa_viewer_id');
        document.location.href = URL + '&p=sa&d=ws';
    })

    .ready(function() {
        frameHidden.onresize = _fbhs;

        VK.callMethod('scrollWindow', 0);
        VK.callMethod('scrollSubscribe');
        VK.addCallback('onScroll', function(top) { VK_SCROLL = top; });

        sortable();
        _fbhs();
    });