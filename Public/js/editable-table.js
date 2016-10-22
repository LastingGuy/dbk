
var privilege = {
    gOrderQ: "订单查询",
    gOrderE: "订单编辑",
    gMaterialsQ: "物资查询",
    gMaterialsE: "物资编辑",
    gStockQ: "库存查询",
    gStockE: "库存编辑",
    gWorkScheQ: "生产计划查询",
    gWorkScheE: "生产计划编辑",
    gGalQ: "镀铝锌查询",
    gGalE: "镀铝锌编辑",
    gColorQ: "彩涂生产查询",
    gColorE: "彩涂生产编辑",
    gCostQ: "成本查询",
    gCostE: "成本编辑",
    gQualityQ: "质量查询",
    gQualityE: "质量编辑",
    gUserQ: "用户管理",
    gUserE: "用户查询"
};

$('#my_multi_select3').multiSelect({
    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='搜索...'>",
    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='搜索...'>",
    afterInit: function (ms) {
        var that = this,
            $selectableSearch = that.$selectableUl.prev(),
            $selectionSearch = that.$selectionUl.prev(),
            selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
            selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';
        that.qs1 = $selectableSearch.quicksearch(selectableSearchString).on('keydown', function (e) {
            if (e.which === 40) {
                that.$selectableUl.focus();
                return false;
            }
        });
        that.qs2 = $selectionSearch.quicksearch(selectionSearchString).on('keydown', function (e) {
            if (e.which == 40) {
                that.$selectionUl.focus();
                return false;
            }
        });
    },
    afterSelect: function () {
        this.qs1.cache();
        this.qs2.cache();
    },
    afterDeselect: function () {
        this.qs1.cache();
        this.qs2.cache();
    },
    selectableFooter: "<h4 style='text-align: center'>拥有权限</h4>",
    selectionFooter: "<h4 style='text-align: center'>可选权限</h4>"

});

function save(){
    var nRow = $(this).parents('tr')[0];
    var jqInputs = $('input', nRow);
    var arr = $('#my_multi_select3').val();
    var privilege = {
        gOrderQ: 0,
        gOrderE: 0,
        gMaterialsQ: 0,
        gMaterialsE: 0,
        gStockQ: 0,
        gStockE: 0,
        gWorkScheQ: 0,
        gWorkScheE: 0,
        gGalQ: 0,
        gGalE: 0,
        gColorQ: 0,
        gColorE: 0,
        gCostQ: 0,
        gCostE: 0,
        gQualityQ: 0,
        gQualityE: 0,
        gUserQ: 0,
        gUserE: 0
    }
    for(var data in arr)
    {
        privilege[arr[data]] = 1;
    }

    //增加
    if ($(this).attr("data-mode") == "new"){
        $.post("{:U('admin/group/add')}",{
                gId: jqInputs[0].value,
                group_name: jqInputs[1].value,
                gOrderQ: privilege['gOrderQ'],
                gOrderE: privilege['gOrderE'],
                gMaterialsQ: privilege['gMaterialsQ'],
                gMaterialsE: privilege['gMaterialsE'],
                gStockQ: privilege['gStockQ'],
                gStockE: privilege['gStockE'],
                gWorkScheQ: privilege['gWorkScheQ'],
                gWorkScheE: privilege['gWorkScheE'],
                gGalQ: privilege['gGalQ'],
                gGalE: privilege['gGalE'],
                gColorQ: privilege['gColorQ'],
                gColorE: privilege['gColorE'],
                gCostQ: privilege['gCostQ'],
                gCostE: privilege['gCostE'],
                gQualityQ: privilege['gQualityQ'],
                gQualityE: privilege['gQualityE'],
                gUserQ: privilege['gUserQ'],
                gUserE: privilege['gUserE'],
            },
            function(data){
                if(data['result']==1){
                    alert("增加成功！");
                }
                else if(data['result']==0){
                    alert("增加失败！");
                }

            });
    }
    else{
        //修改
        $.post("{:U('admin/group/edit')}",{
                gId: jqInputs[0].value,
                group_name: jqInputs[1].value,
                gOrderQ: privilege['gOrderQ'],
                gOrderE: privilege['gOrderE'],
                gMaterialsQ: privilege['gMaterialsQ'],
                gMaterialsE: privilege['gMaterialsE'],
                gStockQ: privilege['gStockQ'],
                gStockE: privilege['gStockE'],
                gWorkScheQ: privilege['gWorkScheQ'],
                gWorkScheE: privilege['gWorkScheE'],
                gGalQ: privilege['gGalQ'],
                gGalE: privilege['gGalE'],
                gColorQ: privilege['gColorQ'],
                gColorE: privilege['gColorE'],
                gCostQ: privilege['gCostQ'],
                gCostE: privilege['gCostE'],
                gQualityQ: privilege['gQualityQ'],
                gQualityE: privilege['gQualityE'],
                gUserQ: privilege['gUserQ'],
                gUserE: privilege['gUserE'],
            },
            function(data){
                if(data['result']==1){
                    alert("修改成功！");
                }
                else if(data['result']==0){
                    alert("修改失败！");
                }

            });
    }

}


var EditableTable = function () {

'use strict';

    return {
        init: function () {
            function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }
                oTable.fnDraw();
            }

            function editRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                jqTds[0].innerHTML = '<input type="text" class="form-control small" value="' + aData[0] + '">';
                jqTds[1].innerHTML = '<input type="text" class="form-control small" value="' + aData[1] + '">';
                jqTds[2].innerHTML = '<a class="edit" href=""  onclick="save()"><span class="label label-primary">Save</span></a>';
                jqTds[3].innerHTML = '<a class="cancel" href=""><span class="label label-info">Cancel</span></a>';
            }


            function saveRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate('<a class="edit" href=""><span class="label label-success">Edit</span></a>', nRow, 4, false);
                oTable.fnUpdate('<a class="delete" href=""><span class="label label-danger">Delete</span></a>', nRow, 5, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 4, false);
                oTable.fnDraw();
            }
            var oTable = $('#editable-sample').dataTable({
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"]
                ],
                "iDisplayLength": 5,
                "sDom": "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-6'i><'col-lg-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ records per page",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [0]
                }]
            });
            jQuery('#editable-sample_wrapper .dataTables_filter input').addClass("form-control medium");
            jQuery('#editable-sample_wrapper .dataTables_length select').addClass("form-control xsmall");
            var nEditing = null;
            $('#editable-sample_new').click(function (e) {
                e.preventDefault();
                var aiNew = oTable.fnAddData(['', '', '<a class="edit" href="">Edit</a>', '<a class="cancel" data-mode="new" href="">Cancel</a>']);
                var nRow = oTable.fnGetNodes(aiNew[0]);
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                jqTds[0].innerHTML = '<input type="text" class="form-control small" value="' + aData[0] + '">';
                jqTds[1].innerHTML = '<input type="text" class="form-control small" value="' + aData[1] + '">';
                jqTds[2].innerHTML = '<a class="edit" href=""  onclick="save()" data-mode="new"><span class="label label-primary">Save</span></a>';
                jqTds[3].innerHTML = '<a class="cancel" href="" data-mode="new"><span class="label label-info">Cancel</span></a>';
                nEditing = nRow;

                for(var each  in privilege){
                    var obj = {};
                    obj.text = privilege[each];
                    obj.value = each;
                    $('#my_multi_select3').multiSelect("addOption",obj);
                }
                $('#current_group').empty();
                $('#current_group').append("<small >正 在 新 增 群 组</small>");
            });
            $('#editable-sample a.delete').live('click', function (e) {
                e.preventDefault();
                if (confirm("Are you sure to delete this row ?") == false) {
                    return;
                }
                var nRow = $(this).parents('tr')[0];

                var jqInputs = $('input',nRow);
                $.post("{:U('admin/group/delete')}",{
                    gId :jqInputs[0].value
                },
                function(data){
                    if(data['result']==1){
                        alert("修改成功！");
                        oTable.fnDeleteRow(nRow);
                    }
                    else if(data['result']==0){
                        alert("修改失败！");
                    }
                })
            });
            $('#editable-sample a.cancel').live('click', function (e) {
                e.preventDefault();
                if ($(this).attr("data-mode") == "new") {
                    var nRow = $(this).parents('tr')[0];
                    oTable.fnDeleteRow(nRow);
                    $('#my_multi_select3').empty();
                    $('.ms-list').empty();
                    $('#current_group').empty();
                } else {
                    restoreRow(oTable, nEditing);
                    nEditing = null;
                    $('#my_multi_select3').empty();
                    $('.ms-list').empty();
                    $('#current_group').empty();
                }
            });
            $('#editable-sample a.edit').live('click', function (e) {
                e.preventDefault();
                var nRow = $(this).parents('tr')[0];
                if (nEditing !== null && nEditing != nRow) {
                    restoreRow(oTable, nEditing);
                    editRow(oTable, nRow);
                    nEditing = nRow;
                } else if (nEditing == nRow && this.innerHTML == "Save") {
                    saveRow(oTable, nEditing);
                    nEditing = null;
                    alert("Updated! Do not forget to do some ajax to sync with backend :)");
                } else {
                    editRow(oTable, nRow);
                    nEditing = nRow;

                }
            });
        }
    };
}();


