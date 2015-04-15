<?php
defined('_JEXEC') or die;


require_once 'helpers/diagram.php';
require_once 'helpers/tournament.php';

class plgContentPolarcontent extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
		{
			return true;
		}
		if ($this->params->get('diagram')==1)
		{
			$start=0;
			while (($start=strpos($article->text,"[fen",$start))!==false)
			{
				$end=strpos($article->text,'[/fen]',$start);
				if ($end===false)
					break;
				$diagram=makeDiagram(substr($article->text,$start,$end-$start+6));
				$article->text=substr_replace($article->text,$diagram,$start,$end-$start+6);
				$start+=strlen($diagram);
			}
		}
		if ($this->params->get('tournament')==1)
		{
			$start=0;
			while (($start=strpos($article->text,"[tour",$start))!==false)
			{
				$end=strpos($article->text,'[/tour]',$start);
				if ($end===false)
					break;
				$tour=new TournamentHelper();
				$tournament=$tour->makeTournament(substr($article->text,$start,$end-$start+7));
				$article->text=substr_replace($article->text,$tournament,$start,$end-$start+7);
				$start+=strlen($tournament);
			}
		}
		return true;
	}
	
}