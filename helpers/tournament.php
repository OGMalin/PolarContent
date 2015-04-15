<?php

defined('_JEXEC') or die;


class TournamentHelper
{
	protected $tournament;
	protected $player;
	protected $result;
	
	public function makeTournament($line)
	{
		$table="";
		$this->tournament=null;
		$this->player=null;
		$this->result=null;
		$tid=1;
		$this->_loadData($tid);
		if ($this->tournament==null)
			return '** PolarTour Error **';
//		var_dump($this->tournament); return "** OK **";

		$lang = JFactory::getLanguage();
		$extension = 'com_polartour';
		$base_dir = JPATH_SITE;
		$language_tag = 'nb-NO';
		$reload = true;
		var_dump($lang);
		$lang->load($extension, $base_dir, $language_tag, $reload);
		$table="<table class='polartour_table'>\n";
		$table.="<thead class='polartour_head'>\n";
		$table.="<tr class='polartour_head_tr'>\n";
		$table.="<th class='poartour_head_th'>".JText::_('COM_POLARTOUR_TABLE_NUMBER')."</th>\n";
		$table.="<th class='poartour_head_th'>".JText::_('COM_POLARTOUR_TABLE_NAME')."</th>\n";
		$table.="<th class='poartour_head_th'>".JText::_('COM_POLARTOUR_TABLE_SCORE')."</th>\n";
		$table.="</tr>\n";
		$table.="</thead>\n";
		$table.="<tbody class='polartour_body'>\n";
		$table.="<tr class='polartour_body_tr'>\n";
		$table.="<td class='poartour_body_td'>"."</td>\n";
		$table.="<td class='poartour_body_td'>"."</td>\n";
		$table.="<td class='poartour_body_td'>"."</td>\n";
		$table.="</tr>\n";
		$table.="</tbody>\n";
		$table.="</table>\n";
		return $table;
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
			return '** PolarTour Error **';
		$this->tournament=$db->loadAssoc();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_player');
		$query->where('tournamentid='.$tid);
		$query->where('trashed=0');
		$db->setQuery($query);
		if ($db->execute())
			$this->player=$db->loadAssoc();
		
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__polartour_result');
		$query->where('tournamentid='.$tid);
		$query->where('trashed=0');
		$db->setQuery($query);
		if ($db->execute())
			$this->result=$db->loadAssoc();
	}
}