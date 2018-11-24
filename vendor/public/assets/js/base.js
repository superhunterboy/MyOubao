jQuery(document).ready(function($) {
/**
 *页面面板左右缩进
 */
	$(".sidebar-collapse-icon").data('state', 1)
	$(".sidebar-collapse-icon").click(function(){
        var state = $(this).data('state');
        if(+state){
            parent.document.getElementById('attachucp').cols="55,*";
			$('.sidebar-list ').hide();
        } else {
            parent.document.getElementById('attachucp').cols="180,*";
			$('.sidebar-list ').show();
        }
        $(this).data('state', +!state)
    });

    /**
     *折叠面板方向图标
     */


    /**
     *菜单状态
     */
     //$(".sidebar-nav").find('.sub-menu:first').addClass('in').end().find('[data-toggle="collapse"]:first').addClass('active');
     $(".sidebar-list").find('[target="main"]').click(function(event) {
        $(".sidebar-list").find('[target="main"]').removeClass('active').end().find('[data-toggle="collapse"]').removeClass('active');
         $(this).addClass('active');
         $(this).parents('li.sidebar-list').find('[data-toggle="collapse"]').addClass('active');
     });

    /**
     *页面加载动画
     */
     $(':submit').click(function(){
        //$('a').click(function(){
        var dHid = $(document).height();
        var spinner='<div class="spinnerBox"><div class="spinner">'
                   +'  <div class="bounce1"></div>'
                   +'  <div class="bounce2"></div>'
                   +'  <div class="bounce3"></div>'
                   +'</div></div>';
        $('body').append(spinner).css('position','relative');
        $('.spinnerBox').css('height', dHid+'px');
    });

     /**
      * 全选allCheckbox
      */
    
    //查询选中值放入指定 容器
    
    var checkValFun = function( checkName,valeId){
        var data = []
        $('input[name="'+checkName+'"]:checked').each(function() { 
            if(this.checked) data.push($(this).val());
         });
         $('.'+valeId).val(data);
    }
    
    $("#allCheckbox").click(function() {
        
         if (this.checked) {
            $('tbody').find('input[name="selectFlag"]').each(function() { 
                $(this).prop("checked", true);  
            }) 
         }else{
            $('tbody').find('input[name="selectFlag"]').each(function() { 
                $(this).prop("checked", false); 
            }) 
         }
        
        checkValFun( 'selectFlag','checkboxId');
    });
    var $subBox = $('tbody').find('input[name="selectFlag"]');
    $subBox.click(function(){
        checkValFun( 'selectFlag','checkboxId');
        $("#allCheckbox").prop("checked",$subBox.length == $('tbody').find("input[name='selectFlag']:checked").length ? true : false);
       

    });
    

});
