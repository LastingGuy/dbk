     // 公告栏的关闭按钮
      $('.closeBtn').click(function(){
        $('.noticeBar').hide();
      });


      //普通取件和VIP取件的切换
      $('.menuSelect').eq(0).show();
      $('.tab li').click(function(){
       $(this).siblings().each(function(index,element){
          $(this).removeClass('on');
          $('.menuSelect').hide();
       });
       $(this).addClass('on');
       $('.menuSelect').eq($(this).index()).fadeIn('fast');
      });


     //大中小件-对应的价格
      // $('.expressSize').change(function(){
      //   switch($('.expressSize').val())
      //   {
      //     case 'size1': $('.normal .price').text('1元'); break;
      //     case 'size2': $('.normal .price').text('2元'); break;
      //     case 'size3': $('.normal .price').text('底价2元， 每增加1千克增加1元'); break;
      //     default: ;
      //   }
      // });


     //轮播图
      $(function () {
        new Swipe(document.getElementById('bannerBox'), {
          speed: 500,
          auto: 3000,
          callback: function () {
            var lis = $(this.element).next("ol").children();
            lis.removeClass("on").eq(this.index + 1).addClass("on");
          }
        });
      });
