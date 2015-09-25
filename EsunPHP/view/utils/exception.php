<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>出错了</title>

<style>
h3{}
h3 span{background-color: #cc0000; color: #fce94f; font-size: x-large; width:25px; margin-right:5px;
        border-radius: 15px 15px 15px 15px; height:25px;display: inline-block; text-align:center;}
.text{line-height:25px;}
</style>

<body>

<h1><?php echo strip_tags($e['message']);?></h1>
<div class="content">
<?php if(isset($e['file'])) {?>
	<div class="info">
		<div class="title">
			<h3><span>!</span>错误位置</h3>
		</div>
		<div class="text">
			<p style="font-weight:bold;">FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
		</div>
	</div>
<?php }?>

<?php if(isset($e['trace'])) {?>
	<div class="info">
		<div class="title">
			<h3><span>!</span>TRACE</h3>
		</div>
		<div class="text">
			<p><?php echo nl2br($e['trace']);?></p>
		</div>
	</div>
<?php }?>
</div>

</body>
</html>