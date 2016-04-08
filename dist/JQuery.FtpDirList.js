;
(function ($) {
    $.fn.FtpDirList = function () {
        var el = $(this);
        var makeTable = function(path){
            $.getJSON('ftp.php?path='+path, function(data){
                el.empty().append('<div class="btn-group">').find('.btn-group').append(function(){
                    var res = Array();
                    res.push('<button type="button" class="btn btn-default folder" data-path="/"><i class="glyphicon glyphicon-home"></i></button>');
                    var p='';
                    $.each(path.split('/'), function(i,v){
                        p+=v+'/';
                        if (v!=''){
                            res.push('<button type="button" class="btn btn-default folder" data-path="'+p+'">'+v+'</button>');
                        }
                    });
                    return res
                }).find('.folder').click(function(){
                    makeTable($(this).data('path'));
                });
                el.append('<table class="table table-bordered table-striped">');
                $.each(data.files, function(i,v){
                    if(v.type=='folder'){
                    el.find('table')
                        .append('<tr><td><a href="#" class="folder" data-path="'+v.path+'"><img height="32" src="img/folder.png" alt="" /> '+v.name+'</a></td><td width="155">'+v.date+'</td><td width="110">'+v.size+'</td></tr>')
                        .find('.folder').click(function(){
                            makeTable($(this).data('path'));
                        });
                    }else{
                        el.find('table')
                            .append('<tr><td><a href="'+ v.url+'"><img height="32" src="img/file.png" alt="" /> '+v.name+'</a></td><td width="155">'+v.date+'</td><td width="110">'+v.size+'</td></tr>')
                            .find('.folder').click(function(){
                                makeTable($(this).data('path'));
                            });
                    }

                });
            });
        };
        makeTable('/');
    }
})(jQuery);