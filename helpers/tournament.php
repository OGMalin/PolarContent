<?php

defined('_JEXEC') or die;

function ScoreCompare($a,$b)
{
	if ($a[4] == $b[4])
	{
		if ($a[5] == $b[5])
		{
			if ($a[6] == $b[6])
			{
				if ($a[7] == $b[7])
				{
					if ($a[8] == $b[8])
					{
						if ($a[9] == $b[9])
						{
							return 0;
						}
						return ($a[9] < $b[9]) ? 1 : -1;
					}
					return ($a[8] < $b[8]) ? 1 : -1;
				}
				return ($a[7] < $b[7]) ? 1 : -1;
			}
			return ($a[6] < $b[6]) ? 1 : -1;
		}
		return ($a[5] < $b[5]) ? 1 : -1;
	}
	return ($a[4] < $b[4]) ? 1 : -1;
}

class TournamentHelper
{
	protected $tournament;
	protected $player;
	protected $result;
	protected $score;
	
	public function makeTournament($line)
	{
		$table="";
		$this->tournament=null;
		$this->player=null;
		$this->result=null;
		if (!$this->_getParametre($tid,$round,$type,$head,$line))
			return '** PolarTour Parametre error **';
//		echo "<pre>";var_dump(array($tid,$round,$type,$head));echo "</pre>";
		if (!$tid)
			return '** PolarTour tournament id missing **';
		if (!$this->_loadData($tid))
			return '** PolarTour Load data error **';

		$lastRound=$this->_getLastRound();
		if ($round>$lastRound)
			$round=$lastRound;

		$this->_calcResult($round);
		
		$table="<table class='polartour_table'>\n";
		if ($head!="")
			$table.="<caption>$head</caption>\n";
		$table.="<thead>\n";
		$table.="<tr>\n";
		$table.="<th>Pl.</th>\n";
		$table.="<th>Navn</th>\n";
		for ($i=1;$i<=$round;$i++)
			$table.="<th>$i</th>\n";
		$table.="<th>Poeng</th>\n";
		$table.="</tr>\n";
		$table.="</thead>\n";
		$table.="<tbody>\n";
		$class='polartour_tbody_tr_odd';
		foreach ($this->score as $s)
		{
			$table.="<tr class=$class>\n";
			$table.="<td>{$s[0]}</td>\n";
			$table.="<td>{$s[2]} {$s[3]}</td>\n";			
			for ($i=1;$i<=$round;$i++)
				$table.="<td>" . $this->_getRoundScore($i,$s[1],$round) . "</td>\n";
			$table.="<td>{$s[4]}</td>\n";
			$table.="</tr>\n";
			$class=($class=='polartour_tbody_tr_odd')?'polartour_tbody_tr_even':'polartour_tbody_tr_odd';
		}
		$table.="</tbody>\n";
		$table.="</table>\n";
		return $table;
	}

	/**
	 * 
	 * @param int $r Round
	 * @param int $p Player
	 * @param int $rounds Number of rounds
	 */
	protected function _getRoundScore($r,$p,$rounds)
	{
		$ret="&nbsp;";
		$c1='';$c2='';$c3='';
		// Find result
		
		for ($i=0;$i<count($this->score);$i++)
		{
			if ($this->score[$i][1]==$p)
			{
				if ($this->score[$i][0+$r+$rounds*2]=='')
					return $ret; 
				$ret='';
				if ($this->score[$i][9+$r]==0)
					$ret.='-';
				elseif ($this->score[$i][9+$r]==1)
					$ret.='+';
				else 
					$ret.='=';
				return $ret;
			}
		}
		
		return $ret;
	}
	/**
	 * 
	 */
	protected function _getLastRound()
	{
		$lastround=0;
		foreach ($this->result as $r)
		{
			if ($r['round']>$lastround)
				$lastround=$r['round'];
		}
		return $lastround;
	}
	
	/**
	 * 
	 * @param int			$tid		Tournament id
	 * @param int			$round	Round number
	 * @param int			$type		Table type
	 * @param string	$head		Headline
	 * @param string	$line		String to check
	 */
	protected function _getParametre(&$tid, &$round, &$type, &$head, $line)
	{
		// Defaults
		$tid=0;
		$round=99;
		$type=1;
		$head="";
		$start=strpos($line,"[tour ",0);
		if ($start===false)
			return false;
		$end=strpos($line,"]",$start);
		if ($end===false)
			return false;
		
		$attr=substr($line,$start,$end-$start);
		$start=$end+1;
		$end=strpos($line,"[/tour]",$start);
		if ($end===false)
			return false;
		
		$head=substr($line,$start,$end-$start);
				
		$start=0;
		while (($start=strpos($attr,' ',$start))!==false)
		{
			if (substr($attr,$start,4)==' id=')
			{
				$start+=5;
				$end=strpos($attr,"\"",$start);
				if ($end!==false)
					$tid=substr($attr,$start,$end-$start);
			}else if (substr($attr,$start,7)==' round=')
			{
				$start+=8;
				$end=strpos($attr,"\"",$start);
				if ($end!==false)
					$round=substr($attr,$start,$end-$start);
			}else if (substr($attr,$start,6)==' type=')
			{
				$start+=7;
				$end=strpos($attr,"\"",$start);
				if ($end!==false)
					$type=substr($attr,$start,$end-$start);
			}else
			{
				++$start;
			}
		}
		return true;
	}
	
	protected function _calcResult($round){
		$this->score=array();
		
//		echo "<pre>";var_dump($this->player);echo "</pre>";
		$i=0;
		foreach ($this->player as $p)
		{
			$this->score[$i++]=array(0,$p['id'],$p['firstname'],$p['lastname'],0,0,0,0,0,0);
		}
		
		foreach ($this->result as $r)
		{
			if ($r['round']<=$round)
			{
				$ws=$bs=0;
				$wp=$r['whiteid'];
				$bp=$r['blackid'];
				$skip=false;
				switch ($r['result'])
				{
					case 0:  // Ongoing
						$skip=true;
						break;
					case 1:  // draw
						$ws=$bs=0.5;
						break;
					case 2:  // White win
						$ws=1;
						break;
					case 3:  // Black win
						$bs=1;
						break;
					case 4:  // Unplayed draw
						$ws=$bs=0.5;
						break;
					case 5:  // Unplayed White win
						$ws=1;
						break;
					case 6:  // Unplayed Black win
						$bs=1;
						break;
				}
				if ($skip)
					continue;
				for ($i=0;$i<count($this->score);$i++)
				{
					if ($this->score[$i][1]==$wp)
					{
						$this->score[$i][4]+=$ws;
						$this->score[$i][9+$r['round']]=$ws;
						$this->score[$i][9+$r['round']+$round]=$bp;
						if ($r['result']>3)
							$this->score[$i][9+$r['round']+$round*2]='f';
						else
							$this->score[$i][9+$r['round']+$round*2]='w';
					}
					if ($this->score[$i][1]==$bp)
					{
						$this->score[$i][4]+=$bs;
						$this->score[$i][9+$r['round']]=$bs;
						$this->score[$i][9+$r['round']+$round]=$bp;
						if ($r['result']>3)
							$this->score[$i][9+$r['round']+$round*2]='f';
						else
							$this->score[$i][9+$r['round']+$round*2]='b';
					}
				}
			}
		}
		usort($this->score, 'ScoreCompare');
		
		$pl=1;
		$skip=1;
		$sc=$tb1=$tb2=$tb3=$tb4=$tb5=0;
//		echo "<pre>";var_dump($this->score);echo "</pre>";
		for ($i=0;$i<count($this->score);$i++)
		{
			if ($i>0)
			{
				if (($this->score[$i][4]==$sc) &&
						($this->score[$i][5]==$tb1) &&
						($this->score[$i][6]==$tb2) &&
						($this->score[$i][7]==$tb3) &&
						($this->score[$i][8]==$tb4) &&
						($this->score[$i][9]==$tb5))
				{
					++$skip;
				}else 
				{
					$pl+=$skip;
					$skip=1;
				}
			}
			$this->score[$i][0]=$pl;
			$sc=$this->score[$i][4];
			$tb1=$this->score[$i][5];
			$tb2=$this->score[$i][6];
			$tb3=$this->score[$i][7];
			$tb4=$this->score[$i][8];
			$tb5=$this->score[$i][9];
		}
//		echo "<pre>";var_dump($this->score);echo "</pre>";
	}
	
	protected function _loadData($tid)
	{
		$db=JFactory::getDbo();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_tournament');
		$query->where('id='.$tid);
		$query->where('trashed=0');
//		var_dump($query); return;
		$db->setQuery($query);		
		if (!$db->execute())
			return false;
		$this->tournament=$db->loadAssoc();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_player');
		$query->where('tournamentid='.$tid);
		$query->where('trashed=0');
		$db->setQuery($query);
		if ($db->execute())
			$this->player=$db->loadAssocList();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_result');
		$query->where('tournamentid='.$tid);
		$query->where('trashed=0');
		$db->setQuery($query);
		if ($db->execute())
			$this->result=$db->loadAssocList();
		return true;
	}
}