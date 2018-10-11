$(document).ajaxStart(function() {
    Pace.restart();
});

(function ($, global) {
    var zh_cn = {
        format: 'YYYY/MM/DD',
        applyLabel: '确定',
        cancelLabel: '取消',
        weekLabel: '周',
        customRangeLabel: '自定义'
    };
    global.daterangepicker = function (el) {
        var $el = $(el);
        $el.daterangepicker({
            locale: zh_cn,
            linkedCalendars: false,
            autoUpdateInput: false
        }, function (begin_date, end_date) {
            var fmt = this.locale.format;
            this.element.val(begin_date.format(fmt) +' - '+ end_date.format(fmt)).trigger('change');
        });
        $el.triggerHandler('change');
        return $el
    }
    global.datepicker = function (el) {
        var $el = $(el);
        var fmt = 'YYYY-MM-DD',
            opts = {
                locale: zh_cn,
                singleDatePicker: true,
                autoUpdateInput:  false     // 默认时间设置
            };
        if($el.data('min')){
            opts['minDate'] = $el.data('min');
            opts.locale['format'] = 'YYYY-MM-DD HH:mm';
        }
        if ($el.data('time-incr')) {        // 选择分钟间隔
            fmt += ' HH:mm';
            opts = $.extend(opts, {
                timePicker: true,
                timePickerIncrement: $el.data('time-incr')
            });
        }

        $el.daterangepicker(opts, function(time) {
            this.element.val(time.format(fmt)).trigger('change');
        });
        $el.triggerHandler('change');
        return $el
    }

    // 验证身份证
    var id_weights = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];
    var id_verifies = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];
    function is_id(value) {
        if (! value) return null;
        if (/^\d{17}[xX\d]$/.test(value)) {
            var arr = value.split(''),
                sum = 0, i = 17;
            if (arr[17] == 'x' || arr[17] == 'X') arr[17] = 10;
            while (i--) sum += id_weights[i] * arr[i];
            return arr[17] == id_verifies[sum%11]
        }
        return false
    }
    // 验证手机号码
    function is_tel(value) {
        if (! value) return null;
        return /^1[345678]\d{9}$/.test(value)
    }

    global.validatorOptions = {
        delay: 100,
        custom: {
            id: function ($el) {
                return is_id($el.val()) == false
            },
            tel: function($el) {
                return is_tel($el.val()) == false
            }
        }
    }


    // persistent selections
    global.memory = (function() {
        var _key = 'memory_storage';
        var memory = localStorage.getItem(_key);
        memory = memory ? JSON.parse(memory) : {};

        $(window).on('unload',function () {
            localStorage.setItem(_key, JSON.stringify(memory));
        });
        return {
            remember: function (key, value, info) {
                if (!(key in memory)) memory[key] = {};
                memory[key][value] = info === undefined || info === null || info;
            },
            goodbye: function (key) {
                if (key in memory) delete memory[key];
            },
            values: function(key, all) {
                var array = [ ];
                if (key in memory) {
                    var store = memory[key];
                    for(var value in store) {
                        if (store[value] !== null) {
                            if (all) {
                                array.push({ value: store[value] });
                            } else {
                                array.push(value);
                            }
                        }
                    }
                }
                return array;
            },
            forget: function (key, value) {
                if ((key in memory) && (value in memory[key])) memory[key][value] = null;
            },
            recall: function (key, value) {
                return (key in memory) && (value in memory[key]) && memory[key][value] !== null;
            }
        };
    })();

    // 弹框提示
    global.ensure = function(message, callback) {
        $('.modal-body').html(message);
        $('#myModal').modal('show');
        $('.modal-footer').children().show();
        $('#confirm').unbind('click').click(function () {
            $('#myModal').modal('hide');
            $('#myModal').one('hidden.bs.modal',function(){
                if(typeof callback == 'function'){
                    callback();
                }
            })
        })
    }

    global.succeed = function(message, timeout, callback) {
        $('.modal-body').html(message);
        $('.modal-footer').children().hide();
        if ((timeout == '') ||(timeout == null)) {
            timeout = 3;
        }
        $('#myModal').modal('show');
        setTimeout("$('#myModal').modal('hide')", timeout * 1000);
            $('#myModal').one('hidden.bs.modal',function(){
                if(typeof callback == 'function'){
                    callback();
                }
            })
    }

    global.fail = function(message, timeout, callback){
        $('.modal-body').html(message);
        $('.modal-footer').children().hide();
        if ((timeout == '') ||(timeout == null)) {
            timeout = 5;
        }
        $('#myModal').modal('show');
        setTimeout("$('#myModal').modal('hide')",timeout*1000);

            $('#myModal').one('hidden.bs.modal',function(){
                if(typeof callback == 'function'){
                    callback();
                }
            })

    }

})(jQuery, window);

jQuery(function($) {

    $("#callback_info").fadeOut(3000);

    daterangepicker('.date-range');
    datepicker('.date');

    // persistent selections
    var remember= memory.remember,
        forget  = memory.forget,
        recall  = memory.recall;
    function persisent(){
        var key = $(this).closest('table').data('task');
        if ($(this).prop('checked')) {
            var info = $(this).closest('tr').data('info');
            remember(key, $(this).val(), info || '');
        } else {
            forget(key, $(this).val())
        }
    }
    $('table[data-task] tbody').on('task-done', function() {
        var key = $(this).closest('table').data('task');
        memory.goodbye(key);
        $(':checked', this).prop('checked', false);
    }).on('click', 'td :checkbox', persisent).find('td :checkbox').each(function() {
        var key = $(this).closest('table').data('task');
        if (recall(key, $(this).val())) {
            if ($(this).prop('disabled')) {
                forget(key, $(this).val());
            } else {
                $(this).prop('checked', true);
            }
        }
    }).end().each(function(_, tbody) {
        $('form.query :reset').click(function() {
            $(tbody).trigger('task-done');
        });
    });


    $('form.query :reset').click(function(evt) {
        evt.preventDefault();
        var form = $(this).closest('form');
        $('input', form).val('');
        $('select',form).each(function(){
            $(this).val($("option:first",this).val());
        });
    });

    //多选
    $('thead #all:checkbox').click(function(){
        var inputs = $(this).closest('table').find('tbody :checkbox').not('[disabled]');
        inputs.prop("checked", $(this).prop('checked'));

        inputs.each(persisent);
    });

    // 消息通知
    $('.navbar-static-top .fa-envelope-o:first').each(function() {
        var $me = $(this).parent('a');
        $.get($me.attr('href')+'?json=1', function (resp) {
            if (resp.code === 0 && resp.info.total > 0) {
                //$me.contents().last().replaceWith(' (' + resp.info.total +')');
                $me.find('.label-success').text(resp.info.total);
            }
        }, 'json');
    });
});
