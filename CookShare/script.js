window.onload=function () {
var popup=document.getElementById('js-popup');
if (!popup) return;
popup.classList.add('is-show');
var blackBg=document.getElementById('js-black-bg');
var closeBtn=document.getElementById('btnTitle');
closePopUp(blackBg);
closePopUp(closeBtn);
function closePopUp(elem) {
if (!elem) return;
elem.addEventListener('click', function () {
popup.classList.remove('is-show');
}
)
}
}
$(function () {
$('li').click(function () {
$(this).addClass('active').siblings().removeClass('active');
}
); //-------------------------------------------------------------- 共通 -------------------------------------------------------------
//サムネイル表示
$('#image').change(function () {
if (this.files.length > 0) {
// 選択されたファイル情報を取得
var file=this.files[0]; // readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
var reader=new FileReader();
reader.readAsDataURL(file);
reader.onload=function () {
$('#thumbnail').attr('src', reader.result);
}
}
}
); //写真削除ボタン
//register.php
$('#btnImgeRecipesDel').on('click', function () {
$('#image').val("");
$('#lblImage').text("画像を選択してください");
$('#thumbnail').attr('src', 'image/recipes/noimage.png');
$('#btnImgeRecipesDel_flg').val("1");
}
); //setting.php
$('#btnImgeUsersDel').on('click', function () {
$('#image').val("");
$('#lblImage').text("画像を選択してください");
$('#thumbnail').attr('src', 'image/users/noimage.jpg');
$('#btnImgeUsersDel_flg').val("1");
}
); //-------------------------------------------------------------- list.php -------------------------------------------------------------  
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
); //$("#sortable").sortable();
//-------------------------------------------------------------- register.php -------------------------------------------------------------
//txtQuantity　半角のみ入力可
/*
$('#txtQuantity').on('input', function(e) {
let value=$(e.currentTarget).val();
value=value .replace(/[０-９]/g, function(s) {
return String.fromCharCode(s.charCodeAt(0) - 65248);
}
) .replace(/[^0-9]/g, '');
$(e.currentTarget).val(value);
}
);
*/
//btnImage 活性・非活性
if ($('#txtTitle').val()==="") {
$('#btnImage').prop('disabled', true);
}
else {
$('#btnImage').prop('disabled', false);
}
$('#txtTitle').keyup(function () {
if ($('#txtTitle').val()==="") {
$('#btnImage').prop('disabled', true);
}
else {
$('#btnImage').prop('disabled', false);
}
}
); //btnMaterials 活性・非活性
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
if ($('#txtPersons').val()==="") {
$('#btnSteps').prop('disabled', true);
}
else {
$('#btnSteps').prop('disabled', false);
}
$('#txtPersons').keyup(function () {
if ($('#txtPersons').val()==="") {
$('#btnSteps').prop('disabled', true);
}
else {
$('#btnSteps').prop('disabled', false);
}
}
);
var count=0;
var materials=[];
var tr=$("#table tr");
for (var i = 0; i 
< tr.length-1; i++) {
              var cells = tr.eq(i+1).children();
              for (var j = 0; j 
              < 2; j++) {
if (typeof materials[i]=="undefined")
materials[i]=[];
if(j===0){
if(cells.eq(j).find('.d-none').val() === '0'){
materials[i][j]='';
}else{
materials[i][j]=cells.eq(j).find('.d-none').val();
}
}else{
materials[i][j]=cells.eq(j).find('.txtQuantity').val();
}
if (materials[i][j]==='') {
count++;
}
}
}
if (count > 0 ) {
$('#btnSteps').prop('disabled', true);
}
else {
$('#btnSteps').prop('disabled', false);
}
$('#table').keyup(function () {
var count=0;
var materials=[];
var tr=$("#table tr");
for (var i = 0; i 
< tr.length-1; i++) {
              var cells = tr.eq(i+1).children();
              for (var j = 0; j 
              < 2; j++) {
if (typeof materials[i]=="undefined")
materials[i]=[];
if(j===0){
if(cells.eq(j).find('.d-none').val() === '0'){
materials[i][j]='';
}else{
materials[i][j]=cells.eq(j).find('.d-none').val();
}
}else{
materials[i][j]=cells.eq(j).find('.txtQuantity').val();
}
if (materials[i][j]==='') {
count++;
}
}
}
if (count > 0 ) {
$('#btnSteps').prop('disabled', true);
}
else {
$('#btnSteps').prop('disabled', false);
}
}
); //btnComment 活性・非活性
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
    if (steps[i][0]==='') {
    count++;
    }
    }
    if (count > 0) {
  $('#btnComment').prop('disabled', true);
  }
  else {
  $('#btnComment').prop('disabled', false);
  }
  $('#tb_step').keyup(function () {
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
      if (steps[i][0]==='') {
      count++;
      }
      }
      if (count > 0) {
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
      $('a.no_link').click(function () {
      return false;
      }
      ); //-------------------------------------------------------------- レシピ新規登録　テーブル　始--------------------------------------------------------------
      var $tableID='';
      var newTr='';
      var tableID_nm='';
      var $cloneTr='';
      $('.btn_modal').on('click', function () {
      if ($(this).data('target')==='#modal_material') {
      $tableID=$('#table');
      tableID_nm='table';
      }
      else if ($(this).data('target')==='#modal_step') {
      //alert($(this).data('target'));
      $tableID=$('#tb_step');
      tableID_nm='tb_step'; //alert('tableIDは' + Object.values($tableID));
      } //行削除--------------------------------------------------------------
      $tableID.on('click', '.table-remove', function () {
      //全て行削除→行追加　の場合に備えて、行を保持しておく（ドロップダウン保持のため）
      $cloneTr=$('#table tr').eq(1).clone();
      $(this).parents('tr').detach(); //btnSteps 活性・非活性--------------------------------------------------------------
      var count=0;
      if (tableID_nm==='table') {
      var materials=[];
      var tr=$tableID.find('tr');
      for (var i = 0; i 
      < tr.length-1; i++) {
                    var cells = tr.eq(i+1).children();
                    for (var j = 0; j 
                    < 2; j++) {
      if (typeof materials[i]=="undefined")
      materials[i]=[];
      if(j===0){
      if(cells.eq(j).find('.d-none').val() === '0'){
      materials[i][j]='';
      }else{
      materials[i][j]=cells.eq(j).find('.d-none').val();
      }
      }else{
      materials[i][j]=cells.eq(j).find('.txtQuantity').val();
      }
      if (materials[i][j]==='') {
      count++;
      }
      }
      }
      if (count > 0 || tr.length===1) {
      $('#btnSteps').prop('disabled', true);
      }
      else {
      $('#btnSteps').prop('disabled', false);
      }
      } //btnComment 活性・非活性--------------------------------------------------------------
      else if (tableID_nm==='tb_step') {
      var steps=[];
      var tr=$tableID.find('tr');
      for (var i=0, l=tr.length;
      i 
      < l;
          i++) {
          var cells=tr.eq(i).children();
          if (typeof steps[i]=="undefined") steps[i]=[];
          steps[i][0]=cells.eq(0).find('input').val();
          if (steps[i][0]==='') {
          count++;
          }
          }
          if (count > 0 || tr.length===1) {
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
        $rowNum=$tableID.find('tbody').children().length + 1; //table
        if (tableID_nm==='table') {
        var $clone='';
        var plus=0;
        if($tableID.find('tbody').children().length===0) {
        //全て行削除→行追加　の場合
        $clone=$cloneTr;
        }
        else {
        //通常　の場合
        $clone=$tableID.find('tbody tr').last().clone();
        plus=1;
        }
        var spoon_checked=''; //append前に最終行のラジオボタンの選択情報を保持
        if ($tableID.find('tbody tr').last().find('.rbTablespoon').prop("checked")) {
        spoon_checked='rbTablespoon';
        }
        else if ($tableID.find('tbody tr').last().find('.rbTeaspoon').prop("checked")) {
        spoon_checked='rbTeaspoon';
        } //append
        $tableID.find('table').append($clone); //txtQuantity クリア
        $tableID.find('tbody tr').last().find('#txtQuantity').val(''); //lblQuantity クリア
        $tableID.find('tbody tr').last().find('.lblQuantity').text(''); //append後に保持しておいたラジオボタンの選択情報をセット
        if (spoon_checked==='rbTablespoon') {
        $tableID.find('tbody tr:last').prev().find('.rbTablespoon').prop('checked', true);
        }
        else if (spoon_checked==='rbTeaspoon') {
        $tableID.find('tbody tr:last').prev().find('.rbTeaspoon').prop('checked', true);
        } //名前の設定
        //ddl_matelials--------------------------------------------------------------
        var $ddl_matelials_id=($tableID.find('tbody tr').last()).find('.ddl_matelials').attr('id');
        $ddl_matelials_id=$ddl_matelials_id.replace('ddl_matelials', '');
        $ddl_matelials_id='ddl_matelials' + (parseInt($ddl_matelials_id) + plus);
        ($tableID.find('tbody tr').last()).find('.ddl_matelials').attr('id', $ddl_matelials_id);
        ($tableID.find('tbody tr').last()).find('.input_name').attr('name', $ddl_matelials_id);
        var $input_name_id=($tableID.find('tbody tr').last()).find('.input_name').attr('id');
        $input_name_id=$input_name_id.replace('input_name', '');
        $input_name_id='input_name' + (parseInt($input_name_id) + plus);
        ($tableID.find('tbody tr').last()).find('.input_name').attr('id', $input_name_id);
        $('#' + $input_name_id).val(0); //txtQuantity
        var $txtQuantity_id=($tableID.find('tbody tr').last()).find('.txtQuantity').attr('id');
        $txtQuantity_id=$txtQuantity_id.replace('txtQuantity', '');
        $txtQuantity_id='txtQuantity' + (parseInt($txtQuantity_id) + plus);
        ($tableID.find('tbody tr').last()).find('.txtQuantity').attr('id', $txtQuantity_id); //lblQuantity--------------------------------------------------------------
        var $lblQuantity_id=($tableID.find('tbody tr').last()).find('.lblQuantity').attr('id');
        $lblQuantity_id=$lblQuantity_id.replace('lblQuantity', '');
        $lblQuantity_id='lblQuantity' + (parseInt($lblQuantity_id) + plus);
        ($tableID.find('tbody tr').last()).find('.lblQuantity').attr('id', $lblQuantity_id); //spoonGroup--------------------------------------------------------------
        var $spoonGroup_id=($tableID.find('tbody tr').last()).find('.spoonGroup').attr('id');
        $spoonGroup_id=$spoonGroup_id.replace('spoonGroup', '');
        $spoonGroup_id='spoonGroup' + (parseInt($spoonGroup_id) + plus);
        ($tableID.find('tbody tr').last()).find('.spoonGroup').attr('id', $spoonGroup_id); //大さじ--------------------------------------------------------------
        var $rbTablespoon_id=($tableID.find('tbody tr').last()).find('.rbTablespoon').attr('id');
        $rbTablespoon_id=$rbTablespoon_id.replace('rbTablespoon', '');
        $rbTablespoon_id='rbTablespoon' + (parseInt($rbTablespoon_id) + plus);
        ($tableID.find('tbody tr').last()).find('.rbTablespoon').attr('id', $rbTablespoon_id);
        ($tableID.find('tbody tr').last()).find('.lblTablespoon').attr('for', $rbTablespoon_id);
        ($tableID.find('tbody tr').last()).find('.rbTablespoon').attr('name', $spoonGroup_id);
        $('#' + $rbTablespoon_id).prop('checked', true); //小さじ--------------------------------------------------------------
        var $rbTeaspoon_id=($tableID.find('tbody tr').last()).find('.rbTeaspoon').attr('id');
        $rbTeaspoon_id=$rbTeaspoon_id.replace('rbTeaspoon', '');
        $rbTeaspoon_id='rbTeaspoon' + (parseInt($rbTeaspoon_id) + plus);
        ($tableID.find('tbody tr').last()).find('.rbTeaspoon').attr('id', $rbTeaspoon_id);
        ($tableID.find('tbody tr').last()).find('.lblTeaspoon').attr('for', $rbTeaspoon_id);
        ($tableID.find('tbody tr').last()).find('.rbTeaspoon').attr('name', $spoonGroup_id); //unit_id
        var $unit_id=($tableID.find('tbody tr').last()).find('.unit_id').attr('id');
        $unit_id=$unit_id.replace('unit_id', '');
        $unit_id='unit_id' + (parseInt($unit_id) + plus);
        ($tableID.find('tbody tr').last()).find('.unit_id').attr('id', $unit_id); //ddl_matelials
        $('#' + $ddl_matelials_id).hierarchySelect( {
        hierarchy: false, search: true, width: 200, initialValueSet: true, onChange: function (value) {
        if(value === 0){
        $('#' + $txtQuantity_id).prop('disabled', true);
        }
        else{
        $('#' + $txtQuantity_id).prop('disabled', false);
        }
        var res=(String(value)).split(",");
        var data= {
        data_unit_id: res[1]
        }
        ;
        $.ajax( {
        type: "post", url: "register.php", data: data, //Ajax通信が成功した場合
        success: function (data, dataType) {
        //PHPから返ってきたデータの表示
        //alert(data);
        var res=(String(data)).split("<!");
        res=(String(res[0])).split(","); //大さじ・小さじ　ラジオボタン　表示・非表示
        $('#' + $unit_id).val(res[0]);
        if (jQuery.trim(res[1]) !=='大さじ' && jQuery.trim(res[1]) !=='小さじ') {
        $('#' + $spoonGroup_id).hide();
        $('#' + $lblQuantity_id).text(res[1]);
        }
        else {
        $('#' + $spoonGroup_id).show();
        $('#' + $lblQuantity_id).text('杯');
        }
        $('#' + $txtQuantity_id).val('');
        if(res[0] === ''){
        $('#' + $lblQuantity_id).text('');
        }
        if (data=="送信が完了しました") {
        //alert(data);
        }
        else {}
        }
        , //Ajax通信が失敗した場合のメッセージ
        error: function () {
        alert('送信が失敗しました。');
        }
        }
        );
        }
        }
        );
        $('a.no_link').click(function () {
        return false;
        }
        );
        } //tb_step
        else if (tableID_nm==='tb_step') {
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
        $tableID.find('tbody').append(newTr);
        } //大さじ・小さじ　ラジオボタンが変更されたときに処理
        $('input[name="' + $spoonGroup_id + '"]:radio').change(function () {
        $('#' + $unit_id).val($(this).val());
        }
        );
        var count=0; //btnSteps 活性・非活性--------------------------------------------------------------
        if (tableID_nm==='table') {
        var materials=[];
        var tr=$tableID.find('tr');
        for (var i = 0; i 
        < tr.length-1; i++) {
                      var cells = tr.eq(i+1).children();
                      for (var j = 0; j 
                      < 2; j++) {
        if (typeof materials[i]=="undefined")
        materials[i]=[];
        if(j===0){
        if(cells.eq(j).find('.d-none').val() === '0'){
        materials[i][j]='';
        }else{
        materials[i][j]=cells.eq(j).find('.d-none').val();
        }
        }else{
        materials[i][j]=cells.eq(j).find('.txtQuantity').val();
        }
        if (materials[i][j]==='') {
        count++;
        }
        }
        }
        if (count > 0) {
        $('#btnSteps').prop('disabled', true);
        }
        else {
        $('#btnSteps').prop('disabled', false);
        }
        } //btnComment 活性・非活性--------------------------------------------------------------
        else if (tableID_nm==='tb_step') {
        var steps=[];
        var tr=$tableID.find('tr');
        for (var i=0, l=tr.length;
        i 
        < l;
            i++) {
            var cells=tr.eq(i).children();
            if (typeof steps[i]=="undefined") steps[i]=[];
            steps[i][0]=cells.eq(0).find('input').val();
            if (steps[i][0]==='') {
            count++;
            }
            }
            if (count > 0) {
          $('#btnComment').prop('disabled', true);
          }
          else {
          $('#btnComment').prop('disabled', false);
          }
          }
          }
          );
          //--------------------------------------------------------------
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
