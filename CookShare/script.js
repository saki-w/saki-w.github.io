window.onload=function() {
var popup=document.getElementById('js-popup');
if(!popup) return;
popup.classList.add('is-show');
var blackBg=document.getElementById('js-black-bg');
var closeBtn=document.getElementById('btnTitle');
closePopUp(blackBg);
closePopUp(closeBtn);
function closePopUp(elem) {
if(!elem) return;
elem.addEventListener('click', function() {
popup.classList.remove('is-show');
}
)
}
}
$(function () {
$('li').click(function() {
$(this).addClass('active').siblings().removeClass('active');
});
//-------------------------------------------------------------- 共通 -------------------------------------------------------------
//サムネイル表示
$('#image').change(function() {
if (this.files.length > 0) {
// 選択されたファイル情報を取得
var file=this.files[0];
// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
var reader=new FileReader();
reader.readAsDataURL(file);
reader.onload=function() {
$('#thumbnail').attr('src', reader.result);
}
}
}
);
//写真削除ボタン
//register.php
$('#btnImgeRecipesDel').on('click', function () {
$('#image').val("");
$('#lblImage').text("画像を選択してください");
$('#thumbnail').attr('src', 'image/recipes/noimage.png');
$('#btnImgeRecipesDel_flg').val("1");
}
);
//setting.php
$('#btnImgeUsersDel').on('click', function () {
$('#image').val("");
$('#lblImage').text("画像を選択してください");
$('#thumbnail').attr('src', 'image/users/noimage.jpg');
$('#btnImgeUsersDel_flg').val("1");
}
);
//-------------------------------------------------------------- list.php -------------------------------------------------------------  
//チェックボックス
//「全選択」する
$('#all').on('click', function () {
$("input[name='chk[]']").prop('checked', this.checked);
}
); //「全選択」以外のチェックボックスがクリックされたら、
$("input[name='chk[]']").on('click', function () {
if ($('#boxes :checked').length==$('#boxes :input').length) {
// 全てのチェックボックスにチェックが入っていたら、「全選択」 = checked
$('#all').prop('checked', true);
}
else {
// 1つでもチェックが入っていたら、「全選択」 = checked
$('#all').prop('checked', false);
}
}
);
//$("#sortable").sortable();
//-------------------------------------------------------------- register.php -------------------------------------------------------------
//txtQuantity　半角のみ入力可
$('#txtQuantity').on('input', function(e) {
let value=$(e.currentTarget).val();
value=value .replace(/[０-９]/g, function(s) {
return String.fromCharCode(s.charCodeAt(0) - 65248);
}
) .replace(/[^0-9]/g, '');
$(e.currentTarget).val(value);
}
);
//btnImage 活性・非活性
if($('#txtTitle').val()==="") {
$('#btnImage').prop('disabled', true);
}
else {
$('#btnImage').prop('disabled', false);
}
$('#txtTitle').keyup(function() {
if($('#txtTitle').val()==="") {
$('#btnImage').prop('disabled', true);
}
else {
$('#btnImage').prop('disabled', false);
}
}
);
//btnMaterials 活性・非活性
/*
$('#image').change(function() {
if($('#image').val()==="") {
$('#btnMaterials').prop('disabled', true);
}
else {
$('#btnMaterials').prop('disabled', false);
}
}
);
*/
//btnSteps 活性・非活性
var count=0;
var materials=[];
var tr=$("#table tr");
for (var i=0, l=tr.length;
i 
< l;
    i++) {
    var cells=tr.eq(i).children();
    for (var j=0, m=cells.length;
    j 
    < m;
j++) {
if (typeof materials[i]=="undefined") materials[i]=[];
materials[i][j]=cells.eq(j).find('input').val();
if(materials[i][j]==='') {
count++;
}
}
}
if(count > 0) {
$('#btnSteps').prop('disabled', true);
}
else {
$('#btnSteps').prop('disabled', false);
}
$('#table').keyup(function() {
var count=0;
var materials=[];
var tr=$("#table tr");
for (var i=0, l=tr.length;
i 
< l;
    i++) {
    var cells=tr.eq(i).children();
    for (var j=0, m=cells.length;
    j 
    < m;
j++) {
if (typeof materials[i]=="undefined") materials[i]=[];
materials[i][j]=cells.eq(j).find('input').val();
if(materials[i][j]==='') {
count++;
}
}
}
if(count > 0) {
$('#btnSteps').prop('disabled', true);
}
else {
$('#btnSteps').prop('disabled', false);
}
}
);
//btnComment 活性・非活性
var count=0;
var steps=[];
var tr=$("#tb_step tr");
for (var i=0, l=tr.length;
i 
< l;
    i++) {
    var cells=tr.eq(i).children();
    if (typeof steps[i]=="undefined") steps[i]=[];
    steps[i][0]=cells.eq(0).find('input').val();
    if(steps[i][0]==='') {
    count++;
    }
    }
    if(count > 0) {
  $('#btnComment').prop('disabled', true);
  }
  else {
  $('#btnComment').prop('disabled', false);
  }
  $('#tb_step').keyup(function() {
  var count=0;
  var steps=[];
  var tr=$("#tb_step tr");
  for (var i=0, l=tr.length;
  i 
  < l;
      i++) {
      var cells=tr.eq(i).children();
      if (typeof steps[i]=="undefined") steps[i]=[];
      steps[i][0]=cells.eq(0).find('input').val();
      if(steps[i][0]==='') {
      count++;
      }
      }
      if(count > 0) {
    $('#btnComment').prop('disabled', true);
    }
    else {
    $('#btnComment').prop('disabled', false);
    }
    }
    );
    /*
    //正しい書き方が不明
    $tableID.change(function(){
    //作り方ボタン　活性・非活性
    var count = 0;
    if(tableID_nm === 'table'){
    var materials = [];
    var tr = $tableID.find('tr');
    for (var i = 0, l = tr.length; i 
    < l; i++) {
        var cells = tr.eq(i).children();
        for (var j = 0, m = cells.length; j 
        < m; j++) {
    if (typeof materials[i] == "undefined")
    materials[i] = [];
    //materials[i][j] = cells.eq(j).text();
    materials[i][j] = cells.eq(j).find('input').val();
    if(materials[i][j] === ''){
    count++;
    }
    }
    }
    if(count > 0){
    $('#btnSteps').prop('disabled',true);
    }else{
    $('#btnSteps').prop('disabled',false);
    }
    }else if(tableID_nm === 'tb_step'){
    var steps = [];
    var tr = $tableID.find('tr');
    for (var i = 0, l = tr.length; i 
    < l; i++) {
        var cells = tr.eq(i).children();
        if (typeof steps[i] == "undefined")
        steps[i] = [];
        steps[i][0] = cells.eq(0).find('input').val();
        if(steps[i][0] === ''){
        count++;
        }
        }
        if(count > 0){
      $('#btnComment').prop('disabled',true);
      }else{
      $('#btnComment').prop('disabled',false);
      }
      }
      });
      */
      $('a.no_link').click(function() {
      return false;
      }
      ); //-------------------------------------------------------------- レシピ新規登録　テーブル　始--------------------------------------------------------------
      var $tableID='';
      var newTr='';
      var tableID_nm='';
      $('.btn_modal').on('click', function () {
      if ($(this).data('target')==='#modal_material') {
      $tableID=$('#table');
      tableID_nm='table';
      }
      else if ($(this).data('target')==='#modal_step') {
      //alert($(this).data('target'));
      $tableID=$('#tb_step');
      tableID_nm='tb_step'; //alert('tableIDは' + Object.values($tableID));
      }
      //行削除--------------------------------------------------------------
      $tableID.on('click', '.table-remove', function () {
      $(this).parents('tr').detach(); //btnSteps 活性・非活性--------------------------------------------------------------
      var count=0;
      if(tableID_nm==='table') {
      var materials=[];
      var tr=$tableID.find('tr');
      for (var i=0, l=tr.length;
      i 
      < l;
          i++) {
          var cells=tr.eq(i).children();
          for (var j=0, m=cells.length;
          j 
          < m;
      j++) {
      if (typeof materials[i]=="undefined") materials[i]=[];
      materials[i][j]=cells.eq(j).find('input').val();
      if(materials[i][j]==='') {
      count++;
      }
      }
      }
      if(count > 0 || tr.length===1) {
      $('#btnSteps').prop('disabled', true);
      }
      else {
      $('#btnSteps').prop('disabled', false);
      }
      } //btnComment 活性・非活性--------------------------------------------------------------
      else if(tableID_nm==='tb_step') {
      var steps=[];
      var tr=$tableID.find('tr');
      for (var i=0, l=tr.length;
      i 
      < l;
          i++) {
          var cells=tr.eq(i).children();
          if (typeof steps[i]=="undefined") steps[i]=[];
          steps[i][0]=cells.eq(0).find('input').val();
          if(steps[i][0]==='') {
          count++;
          }
          }
          if(count > 0 || tr.length===1) {
        $('#btnComment').prop('disabled', true);
        }
        else {
        $('#btnComment').prop('disabled', false);
        }
        }
        }
        ); //ソート　アップ--------------------------------------------------------------
        $tableID.on('click', '.table-up', function () {
        const $row=$(this).parents('tr');
        if ($row.index()===0) {
        return;
        }
        $row.prev().before($row.get(0));
        }
        ); //ソート　ダウン--------------------------------------------------------------
        $tableID.on('click', '.table-down', function () {
        const $row=$(this).parents('tr');
        $row.next().after($row.get(0));
        }
        );
        }
        ); //行追加--------------------------------------------------------------
        var $rowNum=0;
        $('.table-add').on('click', 'i', ()=> {
        //var $clone = $tableID.find('tbody tr').last().clone(true).removeClass('hide table-line');
        //if ($tableID.find('tbody tr').length === 0) {
        //  $tableID.find('tbody').append(newTr);
        //}
        //$tableID.find('table').append($clone);
        $rowNum=$tableID.find('tbody').children().length + 1;
        if(tableID_nm==='table') {
        newTr=` 
        <tr class="hide"> 
          <td class="pt-3-half"> 
            <input type="text" id="txtMaterial" name="txtMaterial" class="form-control mb-4" placeholder="例）にんじん"> 
          </td> 
          <td class="pt-3-half"> 
            <input type="text" id="txtQuantity" name="txtQuantity" class="form-control mb-4" placeholder="例）1"> 
          </td> 
          <td class="pt-3-half"> 
            <span class="table-up"> 
              <a href="#!" class="indigo-text"> 
                <i class="fas fa-long-arrow-alt-up" aria-hidden="true"> 
                </i> 
              </a> 
            </span> 
            <span class="table-down"> 
              <a href="#!" class="indigo-text"> 
                <i class="fas fa-long-arrow-alt-down" aria-hidden="true"> 
                </i> 
              </a> 
            </span> 
          </td> 
          <td> 
            <span class="table-remove"> 
              <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除 
              </button> 
            </span> 
          </td> 
        </tr>`;
        }
        else if(tableID_nm==='tb_step') {
        newTr=` 
        <tr class="hide"> 
          <td class="pt-3-half"> 
            <div class="col-auto"> 
              <label class="sr-only" for="inlineFormInputGroup"> 
              </label> 
              <div class="input-group mb-2"> 
                <div class="input-group-prepend"> 
                  <div class="input-group-text">` + $rowNum + ` 
                  </div> 
                </div> 
                <input type="text" class="form-control py-0" id="txtStep" name="txtStep" placeholder="例）にんじんを千切りします。"> 
              </div> 
            </div> 
          </td> 
          <td class="pt-3-half"> 
            <span class="table-up"> 
              <a href="#!" class="indigo-text"> 
                <i class="fas fa-long-arrow-alt-up" aria-hidden="true"> 
                </i> 
              </a> 
            </span> 
            <span class="table-down"> 
              <a href="#!" class="indigo-text"> 
                <i class="fas fa-long-arrow-alt-down" aria-hidden="true"> 
                </i> 
              </a> 
            </span> 
          </td> 
          <td> 
            <span class="table-remove"> 
              <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除 
              </button> 
            </span> 
          </td> 
        </tr>`;
        }
        $tableID.find('tbody').append(newTr);
        var count=0; //btnSteps 活性・非活性--------------------------------------------------------------
        if(tableID_nm==='table') {
        var materials=[];
        var tr=$tableID.find('tr');
        for (var i=0, l=tr.length;
        i 
        < l;
            i++) {
            var cells=tr.eq(i).children();
            for (var j=0, m=cells.length;
            j 
            < m;
        j++) {
        if (typeof materials[i]=="undefined") materials[i]=[];
        materials[i][j]=cells.eq(j).find('input').val();
        if(materials[i][j]==='') {
        count++;
        }
        }
        }
        if(count > 0) {
        $('#btnSteps').prop('disabled', true);
        }
        else {
        $('#btnSteps').prop('disabled', false);
        }
        } //btnComment 活性・非活性--------------------------------------------------------------
        else if(tableID_nm==='tb_step') {
        var steps=[];
        var tr=$tableID.find('tr');
        for (var i=0, l=tr.length;
        i 
        < l;
            i++) {
            var cells=tr.eq(i).children();
            if (typeof steps[i]=="undefined") steps[i]=[];
            steps[i][0]=cells.eq(0).find('input').val();
            if(steps[i][0]==='') {
            count++;
            }
            }
            if(count > 0) {
          $('#btnComment').prop('disabled', true);
          }
          else {
          $('#btnComment').prop('disabled', false);
          }
          }
          }
          );
          const $BTN=$('#export-btn');
          const $EXPORT=$('#export');
          jQuery.fn.pop=[].pop;
          jQuery.fn.shift=[].shift;
          $BTN.on('click', ()=> {
          const $rows=$tableID.find('tr:not(:hidden)');
          const headers=[];
          const data=[];
          $($rows.shift()).find('th:not(:empty)').each(function () {
          headers.push($(this).text().toLowerCase());
          }
          );
          $rows.each(function () {
          const $td=$(this).find('td');
          const h= {}
          ;
          headers.forEach((header, i)=> {
          h[header]=$td.eq(i).text();
          }
          );
          data.push(h);
          }
          );
          $EXPORT.text(JSON.stringify(data));
          }
          ); //-------------------------------------------------------------- レシピ新規登録　テーブル　終--------------------------------------------------------------
          }
          );
