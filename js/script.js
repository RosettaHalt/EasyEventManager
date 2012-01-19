var j = jQuery.noConflict();

j(function() {

	//!< ページング
	var page = 0;
	var num = 10;
	var timer;
	var upev_tr = j('#update_event tr');
	function draw(){
		upev_tr.hide();
		j('#update_event tr:first, #update_event tr:gt(' + page * num + '):lt('+num+')').show();
		j('#current_page').html(page+1);
		j('#total_page').html(Math.floor((upev_tr.size()-1)/num+1));
	}
	j('#update_event #prev').click(function(){
		if (page > 0){
			page--;
			draw();
		}
		return false;
	});

	j('#update_event #next').click(function(){
		if (page < (upev_tr.size() - 1) / num - 1){
			page++;
			draw();
		}
		return false;
	});

	draw();

	//!< selectの変更時にページング切り替え
    j('select#foo').change(function(){
        num = j(this).val();
        if (page >= (upev_tr.size() - 1) / num - 1) {
        	page = Math.floor((upev_tr.size()-1)/num+1)-1;
        }
        draw();
    });

	//!< ソート時にテーブルのページングを調整
	j('#update_event th').click(function(){
		timer = setTimeout(function(){ draw(); }, 50);
	});

    //!< 日付入力のデイトピッカー
    j('#datepicker').datepicker({
		dateFormat: 'yy/mm/dd'
	});

	//!< チェックボックスを全選択
	var chk_inp = j('#check input');
	j('#all').click(function(){
		if(this.checked){
			chk_inp.attr('checked','checked');
		}
		else{
			chk_inp.removeAttr('checked');
		}
	});

});