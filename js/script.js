var j = jQuery.noConflict();

j(function() {
	var page = 0;
	var num = 10;
	function draw() {
		j('#update_event tr').hide();
		j('#update_event tr:first, #update_event tr:gt(' + page * num + '):lt('+num+')').show();
	}
	j('#update_event #prev').click(function() {
		if (page > 0) {	
			page--;
			draw();a
		}
	});
	
	j('#update_event #next').click(function() {
		if (page < (j('#update_event tr').size() - 1) / num - 1) {
			page++;
			draw();
		}
	});
	
	draw();
	
    j('select#foo').change(function(){
        num = j(this).val();
        draw();
    });
    
    //j("#datepicker").datepicker();
    j('#datepicker').datepicker({
		dateFormat: 'yy/mm/dd'
	});
    
});