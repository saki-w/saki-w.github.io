$(function () {
$('#txtQuantity').on('input', function(e) {
let value=$(e.currentTarget).val();
value=value .replace(/[０-９]/g, function(s) {
return String.fromCharCode(s.charCodeAt(0) - 65248);
}
) .replace(/[^0-9]/g, '');
$(e.currentTarget).val(value);
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
$('.registration').prop('disabled', true);
}
else {
$('.registration').prop('disabled', false);
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
const newTr=` 
<tr class="hide"> 
  <td class="pt-3-half" contenteditable="true"> 
    <input type="text" class="form-control py-0" id="txtMaterial" name="txtMaterial"> 
  </td> 
  <td class="pt-3-half" contenteditable="true"> 
    <input type="text" class="form-control py-0" id="txtQuantity" name="txtQuantity"> 
  </td> 
  <td> 
    <span class="table-remove">
      <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
      </button>
    </span> 
  </td>`;
  $('.table-add').on('click', 'i', ()=> {
  const $clone=$tableID.find('tbody tr').last().clone(true).removeClass('hide table-line');
  if ($tableID.find('tbody tr').length===0) {
  $('tbody').append(newTr);
  }
  $tableID.find('tbody').append(newTr);
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
  $('.registration').prop('disabled', true);
  }
  else {
  $('.registration').prop('disabled', false);
  }
  }
  );
  $tableID.on('click', '.table-remove', function () {
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
  $('.registration').prop('disabled', true);
  }
  else {
  $('.registration').prop('disabled', false);
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
