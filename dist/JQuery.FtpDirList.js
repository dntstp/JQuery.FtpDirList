;
(function ($) {
    $.fn.FtpDirList = function (phppath, images) {
        phppath = typeof phppath !== 'undefined' ? phppath : 'ftp.php';
        images = typeof images !== 'undefined' ? images : 'img/';
        var el = $(this);
        var makeTable = function(path){
            $.getJSON(phppath+'?path='+path+'&images='+images, function(data){
                if(data.state=='OK') {
                    el.empty().append('<div class="btn-group">').find('.btn-group').append(function () {
                        var res = [];
                        res.push('<button type="button" class="btn btn-default folder" data-path="/"><i class="glyphicon glyphicon-home"></i></button>');
                        var p = '';
                        $.each(path.split('/'), function (i, v) {
                            p += v + '/';
                            if (v != '') {
                                res.push('<button type="button" class="btn btn-default folder" data-path="' + p + '">' + v + '</button>');
                            }
                        });
                        return res
                    }).find('.folder').click(function () {
                        makeTable($(this).data('path'));
                    });
                    el.append('<table class="table table-bordered table-striped">');
                    $.each(data.files, function (i, v) {
                        if (v.type == 'folder') {
                            el.find('table')
                                .append('<tr><td><a href="#" class="folder" data-path="' + v.path + '"><img width="32" src="' + v.img + '" alt="" /> ' + v.name + '</a></td><td width="155">' + v.date + '</td><td width="110">' + v.size + '</td></tr>')
                                .find('.folder').click(function () {
                                makeTable($(this).data('path'));
                            });
                        } else {
                            el.find('table')
                                .append('<tr><td><a href="' + v.url + '"><img width="32" src="' + v.img + '" alt="" /> ' + v.name + '</a></td><td width="155">' + v.date + '</td><td width="110">' + v.size + '</td></tr>')
                                .find('.folder').click(function () {
                                makeTable($(this).data('path'));
                            });
                        }

                    });
                }else{
                    el.empty().append('<p>'+data.error);
                }
            });
        };
        makeTable('/');
    }
})(jQuery);