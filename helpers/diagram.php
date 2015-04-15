<?php

defined('_JEXEC') or die;

function makeDiagram($line)
{
	// [fen size="35" text="this is a test" class="pull-left" invert="0"]r4nk1/1qn1rppp/b1p1p3/2PpP1N1/3P2NP/1p4PB/1P3P2/RQ2R1K1 w[/fen]
	$diagram='';
	$fen='rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR';
	$size=30;
	$invert='';
	$text='kjh';
	$class='';
	$font=2;
	$tomove='';

	$start=strpos($line,"[fen ",0);
	if ($start===false)
		return "";
	$end=strpos($line,"]",$start);
	if ($end===false)
		return "";

	$attr=substr($line,$start,$end-$start);
	$start=$end+1;
	$end=strpos($line,"[/fen]",$start);
	if ($end===false)
		return '';
	$fen=substr($line,$start,$end-$start);
		
	if (strpos($fen," w")!==false)
		$tomove="w";
	else if (strpos($fen," b")!==false)
		$tomove="b";

	$start=0;
	while (($start=strpos($attr,' ',$start))!==false)
	{
		if (substr($attr,$start,6)==' size=')
		{
			$start+=7;
			$end=strpos($attr,"\"",$start);
			if ($end!==false)
				$size=substr($attr,$start,$end-$start);
		}else if (substr($attr,$start,6)==' text=')
		{
			$start+=7;
			$end=strpos($attr,"\"",$start);
			if ($end!==false)
				$text=substr($attr,$start,$end-$start);
		}else if (substr($attr,$start,7)==' class=')
		{
			$start+=8;
			$end=strpos($attr,"\"",$start);
			if ($end!==false)
				$class=substr($attr,$start,$end-$start);
		}else if (substr($attr,$start,8)==' invert=')
		{
			$start+=9;
			$end=strpos($attr,"\"",$start);
			if ($end!==false)
				$invert=substr($attr,$start,$end-$start);
		}else
		{
			++$start;
		}
	}

	// 		$diagram="<pre>";
	// 		$diagram.="size=".$size."\n";
	// 		$diagram.="text=".$text."\n";
	// 		$diagram.="class=".$class."\n";
	// 		$diagram.="invert=".$invert."\n";
	// 		$diagram.="fen=".$fen."\n";
	// 		$diagram.="</pre>";

	for ($row=0;$row<8;$row++)
		for ($file=0;$file<8;$file++)
			$board[$file+(8*$row)]='';
				
			$cn=0;
			$len=strlen($fen);
			$file=0;
			$row=7;

			while (($cn<$len)&&($row>=0))
			{
				$c=substr($fen,$cn,1);
				$sq='';
				switch ($c)
				{
					case 'P': $sq='wp'; break;
					case 'N': $sq='wn'; break;
					case 'B': $sq='wb'; break;
					case 'R': $sq='wr'; break;
					case 'Q': $sq='wq'; break;
					case 'K': $sq='wk'; break;
					case 'p': $sq='bp'; break;
					case 'n': $sq='bn'; break;
					case 'b': $sq='bb'; break;
					case 'r': $sq='br'; break;
					case 'q': $sq='bq'; break;
					case 'k': $sq='bk'; break;
					case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8':
						$file+=$c;
						break;
					case '/':
						if ($file!=0)
							$file=8;
							break;
				}
				if ($sq!='')
				{
					$board[$file+($row*8)] = $sq . substr($board[$file+($row*8)],1,1);
					++$file;
				}
				if ($file>7)
				{
					$file=0;
					--$row;
				}
				$cn++;
			}

			if ($size<25)
			{
				$size=20;
				$font=1;
			}else if ($size<30)
			{
				$size=25;
				$font=1;
			}else if ($size<35)
			{
				$size=30;
				$font=2;
			}else if ($size<40)
			{
				$size=35;
				$font=2;
			}else if ($size<50)
			{
				$size=40;
				$font=3;
			}else if ($size<55)
			{
				$size=50;
				$font=3;
			}else
			{
				$size=55;
				$font=3;
			}

			$diagram=createTableDiagram($board, $size, $class, $font, $text);
			return $diagram;
}

function createTableDiagram($board, $size, $class, $font, $text)
{
	$imgpath="media/polarcontent/img/1/";
	$diagram  = "<table class=\"$class\" width=\"". ($size*9) . "\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$diagram .= "<tr>\n";
	$diagram .= "<td align=\"center\" width=\"" . ($size/2) . "\" height=\"$size\"><font face=\"Arial,Helvetica,Sans-serif\" size=\"$font\">8</font></td><td colspan=\"8\" rowspan=\"8\">\n";
	$diagram .= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" bordercolor=\"#000000\" bgcolor=\"#FFFFFF\"><tr><td width=\"" . ($size*8) . "\" height=\"" . ($size*8) . "\" valign=\"top\" align=\"left\">\n";
	$diagram .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$sqc='white';
	for ($row=7;$row>=0;$row--)
	{
	$diagram .= "<tr>\n";
			for ($file=0;$file<8;$file++)
			{
			$p=$board[$file+(8*$row)];
			if ($p!='')
				$diagram .= "<td class=\"$sqc\" width=\"$size\" height=\"$size\"><img src=\"" . $imgpath . $p . $size . ".gif\" width=\"$size\" height=\"$size\" border=\"0\"></td>\n";
				else
					$diagram .= "<td class=\"$sqc\" width=\"$size\" height=\"$size\">&nbsp;</td>\n";
					$sqc=($sqc=='black')?'white':'black';
			}
			$sqc=($sqc=='black')?'white':'black';
					$diagram .= "</tr>\n";
	}
	$diagram .= "</table></td></tr></table></td></tr>";
		for ($i=7;$i>0;$i--)
	$diagram .= "<tr><td align=\"center\" height=\"$size\"><font face=\"Arial,Helvetica,Sans-serif\" size=\"$font\">$i</font></td></tr>\n";
	$diagram .= "<tr>\n<td>&nbsp;</td>\n";
		for ($i=ord('a');$i<=ord('h');$i++)
		$diagram .= "<td valign=\"top\" width=\"$size\" align=\"center\"><font face=\"Arial,Helvetica,Sans-serif\" size=\"$font\">" . chr($i) . "</font></td>\n";
		$diagram .= "</tr>\n";
		if ($text != '')
			{
			$text=nl2br($text);
			$diagram .= "<tr><td colspan=\"9\" align=\"center\"><em class=\"bildetekst\">$text</em></td></tr>\n";
		}

		$diagram .= "</table\n";
		return $diagram;
}
