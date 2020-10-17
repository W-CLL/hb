<?php
$res = file_get_contents('http://e.xitong1.top/abc/index/jiekou');
$res=json_decode($res,true);
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
<meta charset="UTF-8">
<title>海豹留言后台</title>
<style type="text/css">
</style>
</head>
<body>
 <div>
        <table class="table table-hover"  style="table-layout: fixed;">
					<thead>
						<tr>
						
							<th width="15%">时间</th>
							<th width="15%">电话</th>
							<th width="15%">留言内容</th>
							<th width="15%">IP地址</th>
							<th width="350px">访问URL</th>
						</tr>
					</thead>
					<tbody>
           <?php
            foreach ($res as $k =>$v){
  				    echo "<tr>";
				
					echo	'<td>'.$v['sub_time'].'</td>';
					echo	'<td>'.$v['customer_phone'].'</td>';
					echo	'<td>'.$v['customer_content'].'</td>';
					echo	'<td>'.$v['customer_ip'].'</td>';
					echo	'<td>'.$v['customer_url'].'</td>';
					echo "</tr>";
/*  				    echo "<tr>";
					echo	'<td>1</td>';
					echo	'<td>2</td>';
					echo	'<td>3</td>';
					echo	'<td>4</td>';
					echo	'<td>5</td>';
					echo	'<td>6</td>';
					echo "</tr>"; */
            }
						?>

					</tbody>

				</table>
				</div>
</body>
</html>
