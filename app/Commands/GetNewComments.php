<?php


namespace App\Commands;


class GetNewComments extends GetComments
{
	/** @var string */
	protected $newerThan;

	/**
	 * GetNewComments constructor.
	 *
	 * @param int $id
	 * @param string $newerThan
	 */
	public function __construct($id, string $newerThan)
	{
		parent::__construct($id);
		$this->newerThan = $newerThan;
	}

	/**
	 * @return string
	 */
	public function getNewerThan()
	{
		return $this->newerThan;
	}
}