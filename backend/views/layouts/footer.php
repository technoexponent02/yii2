<footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.6
    </div>
    <strong>Copyright &copy; 2017 <!-- <a href="https://www.technoexponent.com/">Technoexponent</a>. --></strong> All rights
    reserved.
</footer>
<script type="text/javascript">

setInterval(function(){ 
	$.ajax({
		type:"post",
		url: "<?php echo Yii::$app->urlManager->createUrl(['site/refresh-msg-count']);?>" ,
		data: {},			
		dataType: "json",			
		success:function(res) {					
			if(res)
			{
				$('#problem_msg_count').html(res.problem_msg_count);
				$('#loby_msg_count').html(res.loby_msg_count);
				$('#active_msg_count').html(res.active_msg_count);
				$('#hold_msg_count').html(res.hold_msg_count);
			}
		}
	});
 }, 2000);
 
</script>