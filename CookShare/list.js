$(function () {

$('a.no_link').click(function () {
    return false;
  }
  );

$('#table').change(function() {
var count=0;
var list=[]; //全行を取得
var tr=$("#table tr");
for (var i=0, l=tr.length;
i 
< l;
    i++) {
    //1行目から順にth、td問わず列を取得
    var cells=tr.eq(i).children();
    for (var j=0, m=cells.length;
    j 
    < m;
j++) {
if (typeof list[i]=="undefined") list[i]=[]; //materials[i][j] = cells.eq(j).text();//i行目j列の文字列を取得
list[i][j]=cells.eq(j).find('input').val();
if(list[i][j]==='') {
count++;
}
}
}
if(count > 0) {
//$('.registration').prop('disabled', true);
}
else {
//$('.registration').prop('disabled', false);
}
}
); // 1. 「全選択」する
$('#all').on('click', function() {
$("input[name='chk[]']").prop('checked', this.checked);
}
); // 2. 「全選択」以外のチェックボックスがクリックされたら、
//なぜか、$('#boxes :input').lengthが２倍されるので、２で割る
$("input[name='chk[]']").on('click', function() {
if ($('#boxes :checked').length==$('#boxes :input').length / 2) {
// 全てのチェックボックスにチェックが入っていたら、「全選択」 = checked
$('#all').prop('checked', true);
}
else {
// 1つでもチェックが入っていたら、「全選択」 = checked
$('#all').prop('checked', false);
}
}
);
const $tableID=$('#table');
const $BTN=$('#export-btn');
const $EXPORT=$('#export');
const newTr='';

  //行追加--------------------------------------------------------------
  $('.table-add').on('click', 'i', ()=> {
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
      ($tableID.find('tbody tr').last()).find('.txtQuantity').attr('id', $txtQuantity_id); 
      $('#' + $txtQuantity_id).show();


      //lblQuantity--------------------------------------------------------------
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


      //original_flg--------------------------------------------------------------
      var $original_flg_id=($tableID.find('tbody tr').last()).find('.original_flg').attr('id');
      $original_flg_id=$original_flg_id.replace('original_flg', '');
      $original_flg_id='original_flg' + (parseInt($original_flg_id) + plus);
      ($tableID.find('tbody tr').last()).find('.original_flg').attr('id', $original_flg_id);
      $('#' + $original_flg_id).val(1);



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
            type: "post", url: "list.php", data: data, //Ajax通信が成功した場合
            success: function (data, dataType) {
              //PHPから返ってきたデータの表示
              //alert(data);
              var res=(String(data)).split("<!");
              res=(String(res[0])).split(","); //大さじ・小さじ　ラジオボタン　表示・非表示
              $('#' + $unit_id).val(res[0]);
              if (jQuery.trim(res[1]) !=='大さじ' && jQuery.trim(res[1]) !=='小さじ') {
                $('#' + $spoonGroup_id).hide();
                $('#' + $lblQuantity_id).text(res[1]);
                $('#' + $txtQuantity_id).show();
                $('#' + $lblQuantity_id).show();
              }
              else {
                //$('#' + $spoonGroup_id).show();
                $('#' + $spoonGroup_id).hide();
                //$('#' + $lblQuantity_id).text('杯');
                $('#' + $txtQuantity_id).hide();
                $('#' + $lblQuantity_id).hide();
              }
              $('#' + $txtQuantity_id).val(''); //送信完了後フォームの内容をリセット
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
  
  $('input[name="' + $spoonGroup_id + '"]:radio').change(function () {
      $('#' + $unit_id).val($(this).val());
    }
    );









  var count=0;
  var list=[]; //全行を取得
  var tr=$tableID.find('tr');
  for (var i=0, l=tr.length;
  i 
  < l;
      i++) {
      //1行目から順にth、td問わず列を取得
      var cells=tr.eq(i).children();
      for (var j=0, m=cells.length;
      j 
      < m;
  j++) {
  if (typeof list[i]=="undefined") list[i]=[]; //materials[i][j] = cells.eq(j).text();//i行目j列の文字列を取得
  list[i][j]=cells.eq(j).find('input').val();
  if(list[i][j]==='') {
  count++;
  }
  }
  }
  if(count > 0) {
  //$('.registration').prop('disabled', true);
  }
  else {
  //$('.registration').prop('disabled', false);
  }
  }
  );
  //行削除--------------------------------------------------------------
  $tableID.on('click', '.table-remove', function () {
    //全て行削除→行追加　の場合に備えて、行を保持しておく（ドロップダウン保持のため）
      $cloneTr=$('#table tr').eq(1).clone();
  $(this).parents('tr').detach();
  var count=0;
  var list=[]; //全行を取得
  var tr=$tableID.find('tr');
  for (var i=0, l=tr.length;
  i 
  < l;
      i++) {
      //1行目から順にth、td問わず列を取得
      var cells=tr.eq(i).children();
      for (var j=0, m=cells.length;
      j 
      < m;
  j++) {
  if (typeof list[i]=="undefined") list[i]=[]; //materials[i][j] = cells.eq(j).text();//i行目j列の文字列を取得
  list[i][j]=cells.eq(j).find('input').val();
  if(list[i][j]==='') {
  count++;
  }
  }
  }
  if(count > 0 || tr.length===1) {
  //$('.registration').prop('disabled', true);
  }
  else {
  //$('.registration').prop('disabled', false);
  }
  }
  );
  $tableID.on('click', '.table-up', function () {
  const $row=$(this).parents('tr');
  if ($row.index()===0) {
  return;
  }
  $row.prev().before($row.get(0));
  }
  );
  $tableID.on('click', '.table-down', function () {
  const $row=$(this).parents('tr');
  $row.next().after($row.get(0));
  }
  ); // A few jQuery helpers for exporting only
  jQuery.fn.pop=[].pop;
  jQuery.fn.shift=[].shift;
  $BTN.on('click', ()=> {
  const $rows=$tableID.find('tr:not(:hidden)');
  const headers=[];
  const data=[]; // Get the headers (add special header logic here)
  $($rows.shift()).find('th:not(:empty)').each(function () {
  headers.push($(this).text().toLowerCase());
  }
  ); // Turn all existing rows into a loopable array
  $rows.each(function () {
  const $td=$(this).find('td');
  const h= {}
  ; // Use the headers from earlier to name our hash keys
  headers.forEach((header, i)=> {
  h[header]=$td.eq(i).text();
  }
  );
  data.push(h);
  }
  ); // Output the result
  $EXPORT.text(JSON.stringify(data));
  }
  );
  }
  );
