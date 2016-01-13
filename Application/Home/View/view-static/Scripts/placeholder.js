$(function(){

    $('input').focus(function(){$(this).parent().addClass('login-on')}).blur(function(){if($(this).val() == ''){$(this).parent().removeClass('login-on')}});
    
        var doc=document,
        inputs=doc.getElementsByTagName('input'),
        supportPlaceholder='placeholder'in doc.createElement('input'),
        placeholder=function(input){
            var text=input.getAttribute('placeholder'),
                defaultValue=input.defaultValue;
                if(defaultValue==''){
                    input.value=text
                }
                input.onfocus=function(){
                    if(input.value===text){this.value=''}
                };
                input.onblur=function(){
                    if(input.value===''){this.value=text}
                }
        };
    if(!supportPlaceholder){
        for(var i=0,len=inputs.length;i<len;i++){
            var input=inputs[i],text=input.getAttribute('placeholder');
            if(input.type==='text'&&text){
                placeholder(input)
            }
        }
    }

            
   if(!supportPlaceholder){  //ie
            passinput = $('input[type="password"]');
            passinput.hide();
            $('input[name *= pwd]').show();
            $('input[name *= pwd]').click(function(){
                $(this).hide();
                $(this).next().show().focus();
            });
            passinput.blur(function(){
                if($.trim($(this).val()) === ''){
                    $(this).hide();
                    $(this).val('');
                    $(this).prev().show();
                }
            })
    }else{
        $('input[name *= "pwd"]').hide();
    }
});