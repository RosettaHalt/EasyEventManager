var j = jQuery.noConflict();

j(function() {
	
	//!< ページング
	var page = 0;
	var num = 10;
	var timer;
	function draw() {
		j('#update_event tr').hide();
		j('#update_event tr:first, #update_event tr:gt(' + page * num + '):lt('+num+')').show();
	}
	function redraw() {
		//alert(0);
		j('#update_event tr').hide();
		j('#update_event tr:first, #update_event tr:gt(' + page * num + '):lt('+num+')').show();
		
	}
	j('#update_event #prev').click(function() {
		if (page > 0) {	
			page--;
			draw();
		}
		return false;
	});
	
	j('#update_event #next').click(function() {
		if (page < (j('#update_event tr').size() - 1) / num - 1) {
			page++;
			draw();
		}
		return false;
	});
	
	draw();
	
	//!< selectの変更時にページング切り替え
    j('select#foo').change(function(){
        num = j(this).val();
        if (page >= (j('#update_event tr').size() - 1) / num - 1) {
        	page = Math.floor((j('#update_event tr').size()-1)/num+1)-1;
        }
        draw();
    });
	
	//!< ソート時にテーブルのページングを調整
	j('#update_event th').click(function() {
		//page = 0;
		timer = setTimeout(function() { redraw() }, 100);
		
	});
    
    //!< 日付入力のデイトピッカー
    j('#datepicker').datepicker({
		dateFormat: 'yy/mm/dd'
	});
	
	//!< チェックボックスを全選択
	j('#all').click(function(){
		if(this.checked){
			j('#check input').attr('checked','checked');
		}
		else{
			j('#check input').removeAttr('checked');
		}
	});
	
});