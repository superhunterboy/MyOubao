/**
 * Created by root on 16-5-27.
 */

var clickDownload={
    bomao:function (a) {
        $(a).on('click',function () {
            $('.pop').fadeIn(500);

        });
        $('.closeTip').on('click',function () {
            $('.pop').fadeOut(300);
        });
    }
}

clickDownload.bomao('.a-4');
clickDownload.bomao('.b-4');
