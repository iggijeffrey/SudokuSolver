<?php
class SudokuBox {
    public $solved=FALSE;
	public $solved_value;
	public $solving="";
}

$submit_type = $_POST["submit_type"];
$solve_tries = $_POST["solve_tries"];
$auto_solve = $_POST["auto_solve"];
if($auto_solve=="")
	$auto_solve=1;
if($solve_tries=="")
	$solve_tries=0;
else
	$solve_tries++;
$box = array();
$all_solved=TRUE;
for($i=0;$i<9;$i++)
{
	$box[$i] = array();
	for($j=0;$j<9;$j++)
	{
		$n = $_POST["input".$i.$j.""];
		$box[$i][$j] = new SudokuBox();
		if($n>0)
		{
			if($n<=9)
			{
				$box[$i][$j]->solved = TRUE;
				$box[$i][$j]->solved_value = $n;
			}
			else
			{
				$box[$i][$j]->solving = $n;
				$all_solved=FALSE;
			}
		}
		else
		{
			$box[$i][$j]->solving = "123456789";
			$all_solved=FALSE;
		}
	}
}

$total_solved_this_round=0;
if($submit_type=="Solve")
{
	for($i=0;$i<9;$i++)
	{
		for($j=0;$j<9;$j++)
		{
			if($box[$i][$j]->solved==FALSE)
			{
				$solving_array = array();
				$nums = "".$box[$i][$j]->solving;
				$box[$i][$j]->solving="";
				for($a=0;$a<strlen($nums);$a++)
				{
					$solving_array[($nums[$a])]=TRUE;
				}
				$num_count=0;
				for($k=1;$k<=9;$k++)
				{
					$num_flag = TRUE;
					$x=0;
					$y=0;
					if($solving_array[$k]!=TRUE)
						$num_flag=FALSE;
					while($x<9 && $num_flag==TRUE)
					{
						if($i!=$x)
						{
							if($box[$x][$j]->solved_value==$k)
							{
								$num_flag=FALSE;
							}
						}
						$x++;
					}
					while($y<9 && $num_flag==TRUE)
					{
						if($j!=$y)
							if($box[$i][$y]->solved_value==$k)
								$num_flag=FALSE;
						$y++;
					}
					$b=3*intval($i/3);
					while($b<(3*intval($i/3)+3) && $num_flag==TRUE)
					{
						$c=3*intval($j/3);
						while($c<(3*intval($j/3)+3) && $num_flag==TRUE)
						{
							if(!($i==$b && $j==c))
								if($box[$b][$c]->solved_value==$k)
									$num_flag=FALSE;
							$c++;
						}
						$b++;
					}
					if($num_flag==TRUE)
					{
						$num_count ++;
						$box[$i][$j]->solving.=$k;
					}
					
				}
				if($num_count==1)
				{
					$box[$i][$j]->solved=TRUE;
					$box[$i][$j]->solved_value=$box[$i][$j]->solving;
					print("box[$i][$j] = ".$box[$i][$j]->solving."<br/>");
					$total_solved_this_round++;
				}
			}
		}
	}
}
if($all_solved==TRUE)
{
	print("Solved in ".($solve_tries-1)." tries!");
}
?>
<html>
<head>
	<title>Sudoku Solver</title>
</head>
<body>
<h1>Sudoku Solver</h1>
<form method='post' name='sudoku' id='sudoku' action='index.php'>
<table>
<?php
for($i=0;$i<9;$i++)
{
	print("<tr>");
	for($j=0;$j<9;$j++)
	{
		$border = "2px solid black;";
		$hborder = "";
		$vborder = "";
		$input_style = "text";
		$textstyle = "";
		$textbox_value = "";

		if(($i%3)==0)
			$hborder = "border-top: ".$border;
		if($i==8)
			$hborder = "border-bottom: ".$border;
		
		if(($j%3)==0)
			$vborder = "border-left: ".$border;
		if($j==8)
			$vborder = "border-right: ".$border;
		
		

		if($box[$i][$j]->solved==TRUE || $submit_type=="")
		{
			$textstyle = "color:blue;font-size:14pt;border: 1px inset #ccc;";
			$textbox_value = $box[$i][$j]->solved_value;
		}
		else
		{
			$textstyle = "color:red;font-size:8pt;border: 1px inset #ccc;";
			$textbox_value = $box[$i][$j]->solving;
		}
		print("<td style='".$textstyle."".$hborder." ".$vborder."width:30px;height:30px;'>");
			if($submit_type!=NULL)
			{
				$input_style = "hidden";
				print("<div  style='".$textstyle."width:30px;height:30px;'>".$textbox_value."</div>");
			}
			print("<input type='".$input_style."' maxlength='1' name='input".$i.$j."' value='".$textbox_value."' style='".$textstyle."width:30px;height:30px;' />
		</td>");
	}
	print("</tr>");
}
print("
<tr>
	<td colspan='9'>");
		if($solve_tries>0 && $all_solved==FALSE) print("<b>Solve Tries:</b> ".$solve_tries."<br/>");
		print("
		<input type='hidden' name='solve_tries' value='".$solve_tries."'>
		<input type='hidden' name='submit_type' value='Solve'>
		<input type='hidden' name='auto_solve' id='auto_solve' value='".$auto_solve."'>
		<input type='submit' value='Proceed'>
		Auto Solve: <input type='checkbox' id='auto_checkbox' ".($auto_solve==1?"checked='checked'":"")." onclick=\"if(document.getElementById('auto_solve').value=='1'){document.getElementById('auto_solve').value='0';}else{document.getElementById('auto_solve').value='1';}\"><br><br>
		<input type='button' value='Stop' onclick=\"document.getElementById('auto_solve').value='0';document.getElementById('auto_checkbox').checked=true;\">
		<input type='button' value='Clear' onclick=\"window.location='index.php';\">
	</td>
</tr>
</table>
</form>
<script language='Javascript'>
	function submit_main_form()
	{
		document.getElementById('sudoku').submit();
	}
	var submit_type = '".$submit_type."';
	var total_solved_this_round=".$total_solved_this_round.";
	var all_solved=".($all_solved==TRUE?"1":"0").";
	if(submit_type!='' && all_solved==0 && document.getElementById('auto_solve').value=='1')
	{
		setTimeout(submit_main_form(), 5000);
	}
</script>
</body>
</html>");
?>