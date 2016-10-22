if (top.location != location) {
    top.location.href = document.location.href;
}
$(function () {
    window.prettyPrint && prettyPrint();


    var today = new Date(2012, 1, 20);
    $('.dtp').datetimepicker({
        language: "zh-CN",
        format: "yyyy-mm-dd",
        startView: "month",
        minView: "month",
        maxView: "year",
        todayBtn: true,
        todayHighlight: true,
        autoclose: true,
        todayBtn: true
    });

});