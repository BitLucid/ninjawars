<?php
class Clan
{
	private $m_id;
	private $m_name;

	public function __construct($p_id, $p_name) {
		$this->setID($p_id);
		$this->setName($p_name);
	}

	public function getID()
	{ return $this->m_id; }

	public function getName()
	{ return $this->m_name; }

	public function setID($p_id)
	{ $this->m_id = (int)$p_id; }

	public function setName($p_name)
	{ $this->m_name = trim($p_name); }
}
?>
