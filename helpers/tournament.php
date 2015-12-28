<?php

defined('_JEXEC') or die;

require_once JPATH_SITE .'/components/com_polartour/helpers/tournament.php';

class TournamentContentHelper
{
	protected $tournament;
	protected $player;
	protected $result;
	
	/**
	 * 
	 * @param		string $line	Line to be konverted [tour attr]Headline[/tour] 
	 * @return	string 				Table to be displayed
	 */
	public function makeTournament($line)
	{
		$table="";
		$tour=new TournamentHelper();
		
		if (!$this->_getParametre($tid,$round,$type,$xtable,$head,$line))
			return '** PolarTour Parametre error **';
//		echo "<pre>";var_dump(array($tid,$round,$type,$head));echo "</pre>";
		if (!$tid)
			return '** PolarTour tournament id missing **';
		if (!$this->_loadData($tid))
			return '** PolarTour Load data error **';
		
		$tour->tournament=$this->tournament;
		$tour->player=$this->player;
		$tour->result=$this->result;
		$tour->head=$head;
		
		if ($xtable==1)
			$table=$tour->displayTable($round);
		return $table;
	}

	/**
	 * 
	 * @param integer	$tid		Tournament id
	 * @param integer	$round	Round number
	 * @param integer	$type		Table type
	 * @param integer	$xtable	Show pm/xtable
	 * @param string	$head		Headline
	 * @param string	$line		String to check
	 */
	protected function _getParametre(&$tid, &$round, &$type, &$xtable, &$head, $line)
	{
		// Defaults
		$tid=0;
		$round=99;
		$type=1;
		$xtable=1;
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
			}else if (substr($attr,$start,8)==' xtable=')
			{
				$start+=9;
				$end=strpos($attr,"\"",$start);
				if ($end!==false)
					$xtable=substr($attr,$start,$end-$start);
			}else
			{
				++$start;
			}
		}
		return true;
	}
	
	/**
	 * 
	 * @param		integer $tid Id for tournament to be loaded
	 * @return	boolean       
	 */
	protected function _loadData($tid)
	{
		$db=JFactory::getDbo();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_tournament');
		$query->where('id='.$tid);
//		var_dump($query); return;
		$db->setQuery($query);		
		$this->tournament=$db->loadAssoc();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_player');
		$query->where('tournamentid='.$tid);
		$db->setQuery($query);
		$this->player=$db->loadAssocList();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_result');
		$query->where('tournamentid='.$tid);
		$db->setQuery($query);
		$this->result=$db->loadAssocList();
		return true;
	}
}