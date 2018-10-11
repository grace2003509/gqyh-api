//全选
function selectAll(){
    $("INPUT[type='checkbox']").each( function() {
        $(this).attr('checked', true);
        $(this).parents('.checkbox').find('span').addClass('checked');
    });
}
//反选
function invertSelect(){
    $("INPUT[type='checkbox']").each( function() {
        if($(this).prop('checked')) {
            $(this).prop('checked', false);
            $(this).parents('.checkbox').find('span').removeClass('checked');
        }else{
            $(this).prop('checked', true);
            $(this).parents('.checkbox').find('span').addClass('checked');
        }
    });
}