<?php
namespace ITRound\Vbchbbonus;

class NoteWidget extends Vbchbbwidget {
	protected function getEditHtml()
	{
		$html='';
		$html.='<tr ><td colspan="2" align="center">';
		$html.=BeginNote('width="100%"');
		$html.=$this->getSettings('DEFAULT');
		$html.=EndNote();
		$html.='</td></tr>';
		return $html;
	}
}