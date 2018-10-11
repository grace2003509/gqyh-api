// 商品购买流程js操作
var flow_obj = {
    checkout_init: function() {
        //提交订单页面js操作

		// 检查是否选择到点自提
		$('.store_mention').change(function () {
			var checked = $(this).prop('checked');

			if (checked) {
				$('.shipping_method').hide();	// 物流选择
				$('.adr_bg, .no_need_shipping').hide();			// 收货地址
				$('.store_mention_time').show();		// 到店自提时间
				$('#shipping_select').hide();		// 到店自提时间

				var biz_id = $('#shipping_select').attr('Biz_ID');
				var total_price = $("#total_price").val();
				var total_coins = $("#total_coins").val();
				var biz_shipping_fee = $("#Shipping_ID_"+biz_id).val();
                var total_price_txt = total_price - biz_shipping_fee;
                total_price_txt = (Math.round(total_price_txt*100)/100).toFixed(2);
                if(total_coins > 0){
                    $("#total_price_txt").html('&yen' + total_price_txt+'元 + '+total_coins+'积分' );
				}else{
                    $("#total_price_txt").html('&yen' + total_price_txt+'元');
				}
			} else {
                $('.shipping_method').show();	// 物流选择
                $('.adr_bg, .no_need_shipping').show();			// 收货地址
                $('.store_mention_time').hide();		// 到店自提时间
                $('#shipping_select').show();

                var total_price = $("#total_price").val();
                var total_coins = $("#total_coins").val();
                total_price = (Math.round(total_price*100)/100).toFixed(2);
                if(total_coins > 0){
                    $("#total_price_txt").html('&yen' + total_price+'元 + '+total_coins+'积分' );
                }else{
                    $("#total_price_txt").html('&yen' + total_price+'元');
                }
			}
        });
		
		$('.Invoice_btn').click(function(){//发票
			var rel = $(this).attr("rel");
			if(!$(this).attr("checked")){
				$("#Invoice_info_"+rel).hide();
			}else{
				$("#Invoice_info_"+rel).show();
			}
		});
		
        $('.qty_selector a[name=minus]').click(function() {
            var qty_input_obj = $(this).next();
            var qty = parseInt($(qty_input_obj).attr('value')) - 1;
            var cart_id = $(qty_input_obj).next().attr('id');
            if (qty < 1) {
                qty = 1;
				global_obj.win_alert('最小购买数量为1！');
            }

            flow_obj.update_checkout_qty(qty, cart_id);

        });

        $('.qty_selector a[name=add]').click(function() {
            var qty_input_obj = $(this).prev().prev();
            var qty = parseInt($(qty_input_obj).attr('value')) + 1;
            var cart_id = $(qty_input_obj).next().attr('id');
            var Products_ID = cart_id.split('_')[1];
            var products_Count = parseInt($("#Products_Count_" + Products_ID).attr('value'));

            if (qty > products_Count) {
                qty = products_Count;
				global_obj.win_alert('最大购买数量为'+products_Count);
            }
            flow_obj.update_checkout_qty(qty, cart_id);
        });

        //$('.qty_selector input').change(function() {
		$('.qty_selector input').keyup(function() {
            var qty_input_obj = $(this);
            var qty = parseInt($(qty_input_obj).attr('value'));
            var cart_id = $(qty_input_obj).next().attr('id');
            var Products_ID = cart_id.split('_')[1];           
            var products_Count = parseInt($("#Products_Count_" + Products_ID).attr('value'));
            if (qty < 1) {
                qty = 1;
                $(qty_input_obj).attr('value', 1);
				global_obj.win_alert('最小购买数量为1！');
            }
			if(isNaN(qty)){
				qty=0;	 
			}
            if (qty > products_Count) {
                qty = products_Count;
				global_obj.win_alert('最大购买数量为'+products_Count);
                $(qty_input_obj).attr('value', products_Count);                
            }

            flow_obj.update_checkout_qty(qty, cart_id);
        });
		
		$('.qty_selector input').blur(function() {
			var qty_input_obj = $(this);
			var qty = parseInt($(qty_input_obj).attr('value'));
			var cart_id = $(qty_input_obj).next().attr('id');
			if (qty < 1) {
                qty = 1;
                $(qty_input_obj).attr('value', 1);
				global_obj.win_alert('最小购买数量为1！');
            }
			flow_obj.update_checkout_qty(qty, cart_id);
        });

        $("#submit-btn").removeAttr('disabled');

        $('#checkout_form').submit(function() {
            return false;
        });

		$('#checkout_form #submit-btn').click(function() {
            var  checkout= $('#checkout_form input[name=checkout]').val();
			if(checkout == 'checkout_virtual'){
				var AddressID = parseInt($('#checkout_form input[name=AddressID]').val());
				var Address_WeiXin = $('#Address_WeiXin').val();
				var msg = '';
				if ((AddressID == 0 || isNaN(AddressID)) && Address_WeiXin == "") {
					if (global_obj.check_form($('*[notnull]'))) {
						return false
					};
					var msg = '收货地址不能为空!';
					
				}
				if(msg != ''){
					new Toast({
						context: $("body"),
						message: msg,
						top: $("#footer").offset().top - 70,
						time: 4000
					}).show();
					return false;
				}
			} else {
				var AddressID = parseInt($('#checkout_form input[name=AddressID]').val());
				//var Address_WeiXin = $('#Address_WeiXin').val();
				var msg = '';
				if (AddressID == 0 || isNaN(AddressID)) {
					if (global_obj.check_form($('*[notnull]'))) {
						return false
					};
					var msg = '收货地址不能为空!';
				}
				
				if(msg != ''){
					new Toast({
						context: $("body"),
						message: msg,
						top: $("#footer").offset().top - 70,
						time: 4000
					}).show();
					return false;
				}
			}
			var me = $(this);
            me.attr('disabled', true);

            var param = $('#checkout_form').serialize();
            var url = $('#checkout_form').attr('action') + 'ajax/';

			
            $.post(url, param, function(data) {
                me.attr('disabled', false);
                if (data.status == 1) {
                    window.location.href = data.url;
                }else if(data.status == 2){
                    alert(data.msg);
                    window.location.href = data.url;
				}else {
					if(typeof data.msg != 'undefined'){
						global_obj.win_alert(data.msg);
					}   
				}
            }, 'json');
        });
		
		$(".panel-footer").on('click','input.coupon',function(e){
				var pre_coupon_value = $("#coupon_value").attr('value');
				var pre_biz_value = $("#prebiz_value").attr('value');
				var total_price = $("#total_price").attr('value');
				var coupon_price = $(this).attr('price');
				var biz_pid = $(this).attr('bizid');
				var total_price_bizb = $("#Sub_Totalall_"+biz_pid).attr('value');
				var check_ok = $(this).attr('check');
				if (pre_biz_value == biz_pid && check_ok == 0) {
					total_price = parseFloat(total_price) + parseFloat(pre_coupon_value);
					}
				if(coupon_price > (total_price - 1)){
                    global_obj.win_alert("使用优惠券可抵现金不能超过" + (total_price - 1) + "元");					
				}else{
                    if($(this).attr("usetype") == 1 && check_ok == 0){
                        total_price = total_price - coupon_price;						
                    }else if($(this).attr("usetype") == 0 && coupon_price > 0 && check_ok == 0){
						var coupon_jianvalue = total_price_bizb*(1-coupon_price);
						coupon_jianvalue = (Math.round(coupon_jianvalue*100)/100).toFixed(2);
                        total_price = total_price - coupon_jianvalue;
						$("#coupon_jianvalue").attr('value',coupon_jianvalue);
                    }else{
                        total_price = total_price;						
                    }
					 total_price = (Math.round(total_price*100)/100).toFixed(2);
                    $("#total_price_txt").html('&yen' + total_price);
                    $("#coupon_value").attr('value',coupon_price);
                    $("#prebiz_value").attr('value',biz_pid);
                    $("#total_price").attr('value',total_price);
                    $(this).attr('check',1);
				}				
		});
		
		$(".shipping_method").click(function(){
			var BizID = $(this).attr("Biz_ID");
			var top = $(window).height()/2;
			$("#shipping-modal-"+BizID).css('top',top-80);
			$("#shipping-modal-"+BizID).modal('show');
			
		});
		
		// $("#confirm_shipping_btn").live('click',function(){
		$("#confirm_shipping_btn").on('click',function(){
			var Biz_ID = $(this).attr('biz_id');
			$("#shipping-modal-"+Biz_ID).modal('hide');
		
			flow_obj.change_shipping_method(Biz_ID);
		});
		
		// $("#cancel_shipping_btn").live('click',function(){
		$("#cancel_shipping_btn").on('click',function(){
		    $(".shipping_modal_sun").modal('hide');
		});
		
        /**
         * json对象转字符串形式
         */
        function json2str(o) {
            var arr = [];
            var fmt = function(s) {
                if (typeof s == 'object' && s != null) return json2str(s);
                return /^(string|number)$/.test(typeof s) ? "'" + s + "'" : s;
            }
            for (var i in o) arr.push("'" + i + "':" + fmt(o[i]));
            return '{' + arr.join(',') + '}';
        }
    },
	
    update_checkout_qty: function(qty, cart_id) {
        var City_Code = $("#City_Code").attr('value');
		var Biz_ID = cart_id.split('_')[0];
        var Products_ID = cart_id.split('_')[1];
        var Shipping_ID = flow_obj.getShippingID();
        var Business = $("#Business_" + Products_ID).attr('value');
        var IsShippingFree = parseInt($("#IsShippingFree_" + Products_ID).attr('value'));
        var cart_key = $("#cart_key").attr('value');
		var offset = $("#offset" + Biz_ID).is(":checked");
		offset = offset==true?1:0;
        var param = {
            Shipping_ID:Shipping_ID,
            Business: Business,
            City_Code: City_Code,
            _Qty: qty,
			offset:offset,
            _CartID: cart_id,
            IsShippingFree: IsShippingFree,
			cart_key: cart_key,
            action: 'checkout_update'
        };
		
        var url = base_url + 'api/' + Users_ID + '/shop/cart/ajax/';
        var Cart_ID = cart_id.replace(/\;/g, "");
        $.post(url, param, function(data) {
            if (data.status == 1) {
                if (parseInt(data.biz_shipping_fee) == 0) {
                    $('#biz_shipping_fee_txt_' + Biz_ID).html('免运费');
                } else {
                    $('#biz_shipping_fee_txt_' + Biz_ID).html(data.biz_shipping_fee + '元');
                }
				var sub_total_price_txt = '&yen' + data.Sub_Total;
				if (data.Sub_Coin != 0) {
					sub_total_price_txt = sub_total_price_txt + ' + ' + data.Sub_Coin + '积分';
				}
                $('#subtotal_price_' + Cart_ID).html(sub_total_price_txt);
                $('#Sub_Totalall_' + Biz_ID).val(data.Sub_Total_source);
                $('#subtotal_qty_' + Cart_ID).html(data.Sub_Qty);
                $('#' + Cart_ID).attr('value', data.Sub_Qty);
				$('#biz_shipping_'+Biz_ID).html(data.biz_shipping_name);
				$('#coupon-list-'+Biz_ID).html(data.youhuicontent);				
				
				/******************* 与积分相关的提交订单页面需要修改的元素 start **********************/
				$('#jin_diyong'+Biz_ID).html(data.JF_diyong_Money);		//可抵用的金额
				$("#jin_jf" + Biz_ID).html(data.JF_diyong_Integral);	//用掉的积分
				$("#jin_zz").html(data.JF_current_Integration);
				$("#total_integral").html(data.JF_get_Integral);	//获得的总积分
				
				/******************* 与积分相关的提交订单页面需要修改的元素 end **********************/
				
				var total_price = 0;
				var coupon_price = parseFloat($('.coupon:checked').attr("price"));
				if($('.coupon:checked').attr("usetype") == 1){
					total_price = data.total - coupon_price;
				}else if($('.coupon:checked').attr("usetype") == 0 && coupon_price > 0){
					total_price = data.total*coupon_price;
				}else{
					coupon_price = 0;
					total_price = data.total;
				}
				
				total_price = total_price - coupon_price;
				if (data.JF_diyong_Money != 0) {					
				$("#Integration-list-" + Biz_ID).show();
				if (data.offset == 1) {
				total_price = total_price - data.JF_diyong_Money;
				}				
				} else {
				$("#checkout_form input[name=offset"+Biz_ID+"]").removeAttr("checked");				
				$("#Integration-list-" + Biz_ID).hide();	
				}
                //更新订单合计信息
                total_price = total_price + data.total_shipping_fee;

                var checked = $('.store_mention').prop('checked');
                if(checked === true){
                    total_price = total_price - data.total_shipping_fee;
				}
                total_price = (Math.round(total_price*100)/100).toFixed(2);
				var total_price_txt = '&yen' + total_price;
				if (data.coin != 0) {
					total_price_txt = total_price_txt + ' + ' + data.coin + '积分';
				}

                $("#total_price_txt").html(total_price_txt);
                $("#total_price").attr('value', total_price);
                $("#total_shipping_fee").attr('value', data.total_shipping_fee);
				$("#coupon_value").attr('value',0);
				$('#Shipping_ID_' + Biz_ID).attr("value",data.biz_shipping_fee);
            }
        }, 'json');
    },
	change_shipping_method:function(Biz_ID){
		
		var Shipping_ID = flow_obj.getShippingID();
		var City_Code = $("#City_Code").attr('value');
		var cart_key = $("#cart_key").attr('value');
		var action = 'change_shipping_method';
		var url = base_url + 'api/' + Users_ID + '/shop/cart/ajax/';
		var param = {
			Biz_ID:Biz_ID,
			Shipping_ID:Shipping_ID,
			City_Code:City_Code,
			cart_key:cart_key,
			action:action
			};
			
		$.post(url, param, function(data) {
			
			if(data.status == 1){
				var total_price = data.total + data.total_shipping_fee;
				if (parseFloat(data.biz_shipping_fee) == 0) {
                    $('#biz_shipping_fee_txt_' + Biz_ID).html('免运费');
                } else {
                    $('#biz_shipping_fee_txt_' + Biz_ID).html(data.biz_shipping_fee + '元');
                }

				$('#biz_shipping_'+Biz_ID).html(data.biz_shipping_name);
				$("#total_price_txt").html('&yen' + total_price);
                $("#total_price").attr('value', total_price);
                $("#total_shipping_fee").attr('value', data.total_shipping_fee);
				$("#total_shipping_fee_txt").html('&yen'+data.total_shipping_fee+'元');
				$('#Shipping_ID_' + Biz_ID).attr("value",data.biz_shipping_fee);
			}
			
		},'json');
		
	},
	
	getShippingID:function(){
		
		var Shiping_IDS = [];
		$("input.Shiping_ID_Val:checked").each(function(){
			var Biz_ID = $(this).attr('Biz_ID');
			var Shipping_ID = $(this).val();
            var obj =  new Object();
			obj.Biz_ID = Biz_ID;
			obj.Shipping_ID = Shipping_ID;
			Shiping_IDS.push(obj);
     	});
	   return Shiping_IDS;
		
	}

}
