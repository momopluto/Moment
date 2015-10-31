$(function(){
	$('.regForm input').focus(function(){$(this).parent().addClass('login-on')}).blur(function(){if($(this).val() == ''){$(this).parent().removeClass('login-on')}});
    /**表单验证*/
	var forminfo = $(".regForm").Validform({
		tiptype:function(msg,o,cssctl){
		if(!o.obj.is("form")){
				var objtip=o.obj.siblings(".Validform_checktip");
				cssctl(objtip,o.type);
				if(o.type==2){
					msg = "<i class='icon-ok'></i>";
				}else{
					msg = "<i class='icon-warn'></i><span class='spanTip'>" + msg + '</span>';
				}
				objtip.html(msg);
				}
		},
		datatype:{
				'username':function(gets,obj,curform,regxp){
					var reg_email = /^([a-zA-Z0-9]+[_|\_|\.\-]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,
					 reg_feif = /^[a-zA-Z0-9_\u4e00-\u9fa5]{3,20}$/;	
					if(reg_email.test(gets)){return '用户名不能为邮箱';}
					else if(!reg_feif.test(gets)){return '用户名,3~20个字符,支持字母/中文/数字/下划线';}
					else{return true;}
					},
				
		},
		ignoreHidden:true,  //当为true时对:hidden的表单元素将不做验证;
		ajaxPost:true,
        beforeSubmit:function(curform){
                if($('input[name=agree]').val()==1){$('#errormsg').addClass('hide');return true}else{$('#errormsg').removeClass('hide');return false}   
        }
		
	});

forminfo.config({url:"/regsave",});

$('#regInputSubmit').click(function(){
			forminfo.config({
		        ajaxpost:{
		            timeout:1000,
		            success:function(data,obj){
                        if(data.status === 'y'){
                            redirectTip('恭喜你，注册成功!', true, '/verify.html', 0);
                        }else{
                            msg = "<i class='icon-warn'></i><span class='spanTip'>" + data.msg + '</span>';
                            $('input[name='+data.selector+']').focus().next().html(msg);
                        } 
		            },
		            error:function(data,obj){
		            	 errorTip('网络异常,请重试!', 3);
		            }
		        }
		    });

		});    
});
